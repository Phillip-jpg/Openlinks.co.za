<?php
declare(strict_types=1);

date_default_timezone_set('Africa/Johannesburg');

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/send_email.php';

// Keep SQL CURDATE()/CURTIME() aligned with South Africa local time (UTC+02:00).
$conn->query("SET time_zone = '+02:00'");

header('Content-Type: application/json; charset=UTF-8');

const INTERVAL_STATUS_PENDING = 0;
const INTERVAL_STATUS_SENT = 1;
const INTERVAL_STATUS_FAILED = 2;

function normalizeTimeValue(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }
    $t = strtotime($value);
    if ($t === false) {
        return '';
    }
    return date('H:i:s', $t);
}

function ensureReminderIntervalTable(mysqli $conn): void
{
    $sql = "
        CREATE TABLE IF NOT EXISTS reminder_interval (
            id INT(11) NOT NULL AUTO_INCREMENT,
            parent_reminder_id INT(11) NOT NULL,
            team_id INT(11) NOT NULL DEFAULT 0,
            responsible INT(11) NOT NULL DEFAULT 0,
            interval_date DATE NOT NULL,
            trigger_time TIME NOT NULL,
            scheduled_for DATETIME NOT NULL,
            status TINYINT(1) NOT NULL DEFAULT 0,
            sent_at DATETIME NULL DEFAULT NULL,
            error_message TEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_parent_interval (parent_reminder_id, interval_date, trigger_time),
            KEY idx_parent_status (parent_reminder_id, status),
            KEY idx_team_id (team_id),
            KEY idx_responsible (responsible),
            KEY idx_scheduled_for (scheduled_for)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    if (!$conn->query($sql)) {
        throw new RuntimeException('Failed to ensure reminder_interval table: ' . $conn->error);
    }

    // Backfill schema for existing tables created before team_id/responsible existed.
    $checkTeamColumn = $conn->prepare("SHOW COLUMNS FROM reminder_interval LIKE 'team_id'");
    if (!$checkTeamColumn) {
        throw new RuntimeException('Failed to prepare team_id column check: ' . $conn->error);
    }
    $checkTeamColumn->execute();
    $teamColumnExists = (bool)$checkTeamColumn->get_result()->fetch_assoc();
    $checkTeamColumn->close();
    if (!$teamColumnExists) {
        if (!$conn->query("ALTER TABLE reminder_interval ADD COLUMN team_id INT(11) NOT NULL DEFAULT 0 AFTER parent_reminder_id")) {
            throw new RuntimeException('Failed to add team_id column: ' . $conn->error);
        }
        $conn->query("ALTER TABLE reminder_interval ADD KEY idx_team_id (team_id)");
    }

    $checkResponsibleColumn = $conn->prepare("SHOW COLUMNS FROM reminder_interval LIKE 'responsible'");
    if (!$checkResponsibleColumn) {
        throw new RuntimeException('Failed to prepare responsible column check: ' . $conn->error);
    }
    $checkResponsibleColumn->execute();
    $responsibleColumnExists = (bool)$checkResponsibleColumn->get_result()->fetch_assoc();
    $checkResponsibleColumn->close();
    if (!$responsibleColumnExists) {
        if (!$conn->query("ALTER TABLE reminder_interval ADD COLUMN responsible INT(11) NOT NULL DEFAULT 0 AFTER team_id")) {
            throw new RuntimeException('Failed to add responsible column: ' . $conn->error);
        }
        $conn->query("ALTER TABLE reminder_interval ADD KEY idx_responsible (responsible)");
    }
}

function getParentFilter(): int
{
    if (PHP_SAPI === 'cli' && isset($GLOBALS['argv']) && is_array($GLOBALS['argv'])) {
        foreach ($GLOBALS['argv'] as $arg) {
            if (strpos($arg, '--parent_id=') === 0) {
                return (int)substr($arg, strlen('--parent_id='));
            }
        }
    }
    return isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;
}

function fetchDueParentReminders(mysqli $conn, int $parentId = 0): array
{
    $sql = "
        SELECT r.*
        FROM reminders r
        WHERE r.status = 1
          AND COALESCE(r.every_days, 0) > 0
          AND DATE(r.start_date) <= CURDATE()
          AND CURDATE() <= DATE(r.scheduled_end_date)
          AND MOD(DATEDIFF(CURDATE(), DATE(r.start_date)), r.every_days) = 0
          AND CURTIME() >= r.trigger_time
    ";

    if ($parentId > 0) {
        $sql .= " AND r.id = ? ";
    }

    $sql .= " ORDER BY r.id ASC ";

    if ($parentId > 0) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Prepare failed (due reminders): ' . $conn->error);
        }
        $stmt->bind_param('i', $parentId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    $res = $conn->query($sql);
    if (!$res) {
        throw new RuntimeException('Query failed (due reminders): ' . $conn->error);
    }
    return $res->fetch_all(MYSQLI_ASSOC);
}

function getOrCreateIntervalRecord(
    mysqli $conn,
    int $parentReminderId,
    int $teamId,
    string $intervalDate,
    string $triggerTime
): array {
    $select = $conn->prepare("
        SELECT id, status, team_id
        FROM reminder_interval
        WHERE parent_reminder_id = ?
          AND interval_date = ?
          AND trigger_time = ?
        LIMIT 1
    ");
    if (!$select) {
        throw new RuntimeException('Prepare failed (select interval): ' . $conn->error);
    }

    $select->bind_param('iss', $parentReminderId, $intervalDate, $triggerTime);
    $select->execute();
    $row = $select->get_result()->fetch_assoc();
    $select->close();

    if ($row) {
        $intervalId = (int)$row['id'];
        $savedTeamId = (int)($row['team_id'] ?? 0);
        if ($savedTeamId !== $teamId) {
            $update = $conn->prepare("
                UPDATE reminder_interval
                SET team_id = ?
                WHERE id = ?
            ");
            if ($update) {
                $update->bind_param('ii', $teamId, $intervalId);
                $update->execute();
                $update->close();
            }
        }

        return ['id' => (int)$row['id'], 'status' => (int)$row['status']];
    }

    $scheduledFor = $intervalDate . ' ' . $triggerTime;
    $insert = $conn->prepare("
        INSERT INTO reminder_interval
            (parent_reminder_id, team_id, responsible, interval_date, trigger_time, scheduled_for, status)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$insert) {
        throw new RuntimeException('Prepare failed (insert interval): ' . $conn->error);
    }
    $unassignedResponsible = 0;
    $pendingStatus = INTERVAL_STATUS_PENDING;
    $insert->bind_param('iiisssi', $parentReminderId, $teamId, $unassignedResponsible, $intervalDate, $triggerTime, $scheduledFor, $pendingStatus);

    if (!$insert->execute()) {
        $errNo = $insert->errno;
        $insert->close();

        if ($errNo === 1062) {
            return getOrCreateIntervalRecord($conn, $parentReminderId, $teamId, $intervalDate, $triggerTime);
        }
        throw new RuntimeException('Insert failed (interval): ' . $conn->error);
    }

    $id = (int)$insert->insert_id;
    $insert->close();
    return ['id' => $id, 'status' => INTERVAL_STATUS_PENDING];
}

function markIntervalSent(mysqli $conn, int $intervalId): void
{
    $stmt = $conn->prepare("
        UPDATE reminder_interval
        SET status = ?, sent_at = NOW(), error_message = NULL
        WHERE id = ?
    ");
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (mark sent): ' . $conn->error);
    }
    $status = INTERVAL_STATUS_SENT;
    $stmt->bind_param('ii', $status, $intervalId);
    $stmt->execute();
    $stmt->close();
}

function markIntervalFailed(mysqli $conn, int $intervalId, string $error): void
{
    $stmt = $conn->prepare("
        UPDATE reminder_interval
        SET status = ?, error_message = ?
        WHERE id = ?
    ");
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (mark failed): ' . $conn->error);
    }
    $status = INTERVAL_STATUS_FAILED;
    $safeError = substr($error, 0, 5000);
    $stmt->bind_param('isi', $status, $safeError, $intervalId);
    $stmt->execute();
    $stmt->close();
}

function fetchUserEmailName(mysqli $conn, int $userId): array
{
    $stmt = $conn->prepare("SELECT email, CONCAT(firstname, ' ', lastname) AS full_name FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return ['email' => '', 'name' => ''];
    }
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    return [
        'email' => (string)($row['email'] ?? ''),
        'name' => trim((string)($row['full_name'] ?? '')),
    ];
}

function fetchClientEmailName(mysqli $conn, int $clientId): array
{
    $stmt = $conn->prepare("SELECT Email, company_name FROM yasccoza_openlink_market.client WHERE CLIENT_ID = ? LIMIT 1");
    if (!$stmt) {
        return ['email' => '', 'name' => ''];
    }
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    return [
        'email' => (string)($row['Email'] ?? ''),
        'name' => (string)($row['company_name'] ?? ''),
    ];
}

function fetchRepEmailName(mysqli $conn, int $repId): array
{
    $stmt = $conn->prepare("SELECT REP_EMAIL, REP_NAME FROM client_rep WHERE REP_ID = ? LIMIT 1");
    if (!$stmt) {
        return ['email' => '', 'name' => ''];
    }
    $stmt->bind_param('i', $repId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    return [
        'email' => (string)($row['REP_EMAIL'] ?? ''),
        'name' => (string)($row['REP_NAME'] ?? ''),
    ];
}

function fetchWorkTypeName(mysqli $conn, int $workTypeId): string
{
    $stmt = $conn->prepare("SELECT task_name FROM task_list WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return '';
    }
    $stmt->bind_param('i', $workTypeId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    return (string)($row['task_name'] ?? '');
}

function fetchTeamNameAndMembers(mysqli $conn, int $teamId): array
{
    $stmt = $conn->prepare("
        SELECT DISTINCT
            ts.team_name,
            u.email AS member_email,
            CONCAT(u.firstname, ' ', u.lastname) AS member_name
        FROM team_schedule ts
        LEFT JOIN users u ON u.id = ts.team_members
        WHERE ts.team_id = ?
    ");
    if (!$stmt) {
        return ['team_name' => '', 'members' => []];
    }

    $stmt->bind_param('i', $teamId);
    $stmt->execute();
    $res = $stmt->get_result();

    $teamName = '';
    $members = [];
    $seen = [];
    while ($row = $res->fetch_assoc()) {
        if ($teamName === '' && !empty($row['team_name'])) {
            $teamName = (string)$row['team_name'];
        }

        $email = strtolower(trim((string)($row['member_email'] ?? '')));
        $name = trim((string)($row['member_name'] ?? ''));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && !isset($seen[$email])) {
            $members[] = ['email' => $email, 'name' => $name];
            $seen[$email] = true;
        }
    }

    $stmt->close();
    return ['team_name' => $teamName, 'members' => $members];
}

function addUniqueRecipient(array &$recipients, string $email, string $name): void
{
    $email = strtolower(trim($email));
    $name = trim($name);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return;
    }
    if (!isset($recipients[$email])) {
        $recipients[$email] = ['email' => $email, 'name' => ($name !== '' ? $name : 'User')];
    }
}

function formatReminderEmailDate(string $value): string
{
    $time = strtotime($value);
    if ($time === false) {
        return trim($value) !== '' ? $value : '-';
    }
    return date('l, d F Y', $time);
}

function formatReminderEmailTime(string $value): string
{
    $time = strtotime($value);
    if ($time === false) {
        return trim($value) !== '' ? $value : '-';
    }
    return date('H:i', $time);
}

function buildTriggeredReminderEmailHtml(string $recipientName, array $payload): string
{
    $safeTeam = htmlspecialchars((string)$payload['team_name'], ENT_QUOTES, 'UTF-8');
    $safeEntity = htmlspecialchars((string)$payload['entity_name'], ENT_QUOTES, 'UTF-8');
    $safeClient = htmlspecialchars((string)$payload['client_name'], ENT_QUOTES, 'UTF-8');
    $safeRep = htmlspecialchars((string)$payload['rep_name'], ENT_QUOTES, 'UTF-8');
    $safeWorkType = htmlspecialchars((string)$payload['work_type_name'], ENT_QUOTES, 'UTF-8');
    $safeMeetingDay = htmlspecialchars((string)$payload['meeting_day'], ENT_QUOTES, 'UTF-8');
    $safeMeetingTime = htmlspecialchars((string)$payload['meeting_time'], ENT_QUOTES, 'UTF-8');
    $safePlatform = htmlspecialchars((string)$payload['online_meeting'], ENT_QUOTES, 'UTF-8');
    $safeLink = htmlspecialchars((string)$payload['meeting_link'], ENT_QUOTES, 'UTF-8');
    $meetingLinkHtml = $safeLink !== ''
        ? "<a href='{$safeLink}' target='_blank' rel='noopener noreferrer'>{$safeLink}</a>"
        : '-';

    return "
<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <title>Reminder Notification</title>
  <style>
    .details-table { width:100%; border-collapse:collapse; margin:12px 0 16px 0; }
    .details-table th { width:38%; background:#f0f3f7; color:#0f1f3d; padding:10px; font-size:13px; text-align:left; border:1px solid #d7dde5; }
    .details-table td { background:#ffffff; color:#2f3b4a; padding:10px; font-size:13px; border:1px solid #d7dde5; }
  </style>
</head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
  <table width='100%' cellpadding='0' cellspacing='0' style='padding:28px 0;background:#f4f6f8;'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
        <tr>
          <td style='padding:20px;background:#0f1f3d;color:#fff;'>
            <table width='100%'><tr>
              <td align='left'><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200' alt='OpenLinks'></td>
              <td align='right' style='font-size:13px;line-height:18px;'>
                <b>OpenLinks Corporations (Pty) Ltd</b><br>
                314 Cape Road, Newton Park<br>
                Port Elizabeth, Eastern Cape 6070
              </td>
            </tr></table>
          </td>
        </tr>
        <tr>
          <td style='padding:24px;color:#333;font-size:14px;line-height:1.55;'>
            <p style='margin:0 0 10px 0;'><b>Dear {$safeRep}, {$safeEntity}, and {$safeTeam},</b></p>
            <p style='margin:0 0 12px 0;'><b>Meeting &amp; Schedule Details</b></p>

            <table class='details-table' cellpadding='0' cellspacing='0'>
              <tbody>
                <tr><th>Client / Account</th><td>{$safeClient}</td></tr>
                <tr><th>Rep</th><td>{$safeRep}</td></tr>
                <tr><th>Work Type</th><td>{$safeWorkType}</td></tr>
                <tr><th>Day</th><td>{$safeMeetingDay}</td></tr>
                <tr><th>Time</th><td>{$safeMeetingTime}</td></tr>
                <tr><th>Meeting Platform</th><td>{$safePlatform}</td></tr>
                <tr><th>Meeting Link</th><td>{$meetingLinkHtml}</td></tr>
              </tbody>
            </table>

            <p style='margin:0 0 14px 0;'>
              Please ensure all relevant stakeholders are available and prepared to proceed as scheduled.
              Any constraints or risks to delivery should be communicated in advance.
            </p>

            <p style='margin:0;'>Regards,<br><b>Openlinks Operations</b></p>
          </td>
        </tr>
        <tr>
          <td style='background:#f0f3f7;padding:20px;font-size:12px;color:#555;text-align:center;'>
            Telephone: 041 004 0454 &nbsp;|&nbsp;
            <a href='https://www.openlinks.co.za' style='color:#0f1f3d;text-decoration:none;'>www.openlinks.co.za</a>
            <br><br><small>Automated Notification - Do not reply</small>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>";
}

function sendReminderIntervalEmails(mysqli $conn, array $parent, string $intervalDate): array
{
    if (!function_exists('sendEmailNotification')) {
        return ['ok' => false, 'error' => 'sendEmailNotification function not available.'];
    }

    $pm = fetchUserEmailName($conn, (int)$parent['who']);
    $client = fetchClientEmailName($conn, (int)$parent['account']);
    $rep = fetchRepEmailName($conn, (int)$parent['account_rep']);
    $teamInfo = fetchTeamNameAndMembers($conn, (int)$parent['team']);
    $workTypeName = fetchWorkTypeName($conn, (int)$parent['work_type']);

    $recipients = [];
    addUniqueRecipient($recipients, (string)$pm['email'], (string)$pm['name']);
    addUniqueRecipient($recipients, (string)$rep['email'], (string)$rep['name']);
    foreach ($teamInfo['members'] as $member) {
        addUniqueRecipient($recipients, (string)$member['email'], (string)$member['name']);
    }

    if (empty($recipients)) {
        return ['ok' => false, 'error' => 'No valid recipients found for parent reminder ID ' . (int)$parent['id']];
    }

    $payload = [
        'team_name' => $teamInfo['team_name'] !== '' ? (string)$teamInfo['team_name'] : ('Team #' . (int)$parent['team']),
        'entity_name' => $pm['name'] !== '' ? (string)$pm['name'] : ('Entity #' . (int)$parent['who']),
        'client_name' => $client['name'] !== '' ? (string)$client['name'] : ('Client #' . (int)$parent['account']),
        'rep_name' => $rep['name'] !== '' ? (string)$rep['name'] : ('Rep #' . (int)$parent['account_rep']),
        'work_type_name' => $workTypeName,
        'meeting_day' => ucfirst((string)$parent['meeting_day']),
        'meeting_time' => formatReminderEmailTime((string)$parent['meeting_time']),
        'online_meeting' => (string)$parent['online_meeting'],
        'meeting_link' => (string)$parent['meeting_link'],
    ];

    $subject = 'Reminder Trigger: ' . (string)$parent['reminder_name'] . ' - ' . $intervalDate;
    $failed = [];
    foreach ($recipients as $recipient) {
        $html = buildTriggeredReminderEmailHtml((string)$recipient['name'], $payload);
        $ok = sendEmailNotification((string)$recipient['email'], $subject, $html);
        if (!$ok) {
            $failed[] = (string)$recipient['email'];
        }
    }

    if (!empty($failed)) {
        return ['ok' => false, 'error' => 'Email failed for: ' . implode(', ', $failed)];
    }

    return ['ok' => true, 'error' => ''];
}

try {
    ensureReminderIntervalTable($conn);

    $parentFilter = getParentFilter();
    $dueParents = fetchDueParentReminders($conn, $parentFilter);
    $intervalDate = date('Y-m-d');

    $result = [
        'ok' => true,
        'now' => date('Y-m-d H:i:s'),
        'due_count' => count($dueParents),
        'sent_count' => 0,
        'skipped_count' => 0,
        'failed_count' => 0,
        'details' => [],
    ];

    foreach ($dueParents as $parent) {
        $parentId = (int)$parent['id'];
        $teamId = (int)$parent['team'];
        $triggerTime = normalizeTimeValue((string)$parent['trigger_time']);
        if ($triggerTime === '') {
            $result['failed_count']++;
            $result['details'][] = ['parent_id' => $parentId, 'status' => 'failed', 'message' => 'Invalid trigger_time.'];
            continue;
        }

        $interval = getOrCreateIntervalRecord($conn, $parentId, $teamId, $intervalDate, $triggerTime);
        $intervalId = (int)$interval['id'];
        $intervalStatus = (int)$interval['status'];

        if ($intervalStatus === INTERVAL_STATUS_SENT) {
            $result['skipped_count']++;
            $result['details'][] = ['parent_id' => $parentId, 'interval_id' => $intervalId, 'status' => 'skipped', 'message' => 'Already sent for this interval.'];
            continue;
        }

        $send = sendReminderIntervalEmails($conn, $parent, $intervalDate);
        if ($send['ok']) {
            markIntervalSent($conn, $intervalId);
            $result['sent_count']++;
            $result['details'][] = ['parent_id' => $parentId, 'interval_id' => $intervalId, 'status' => 'sent'];
        } else {
            markIntervalFailed($conn, $intervalId, (string)$send['error']);
            $result['failed_count']++;
            $result['details'][] = ['parent_id' => $parentId, 'interval_id' => $intervalId, 'status' => 'failed', 'message' => $send['error']];
        }
    }

    echo json_encode($result, JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES);
}
