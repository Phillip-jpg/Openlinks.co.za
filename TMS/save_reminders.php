<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

header('Content-Type: text/plain; charset=UTF-8');

function normalize_time(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $time = strtotime($value);
    if ($time === false) {
        return '';
    }

    return date('H:i:s', $time);
}

function normalize_datetime(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $time = strtotime($value);
    if ($time === false) {
        return '';
    }

    return date('Y-m-d H:i:s', $time);
}

function finishAsyncResponse(string $body): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        @session_write_close();
    }

    ignore_user_abort(true);
    @set_time_limit(0);

    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Connection: close');
        header('Content-Length: ' . strlen($body));
    }

    echo $body;

    while (ob_get_level() > 0) {
        @ob_end_flush();
    }
    @flush();

    if (function_exists('fastcgi_finish_request')) {
        @fastcgi_finish_request();
    }
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
        $recipients[$email] = [
            'email' => $email,
            'name' => $name !== '' ? $name : 'User',
        ];
    }
}

function buildReminderCreatedEmailHtml(string $recipientName, array $payload): string
{
    $safeTitle = htmlspecialchars((string)$payload['reminder_name'], ENT_QUOTES, 'UTF-8');
    $safeEveryDays = htmlspecialchars((string)$payload['every_days'], ENT_QUOTES, 'UTF-8');
    $safeTeam = htmlspecialchars((string)$payload['team_name'], ENT_QUOTES, 'UTF-8');
    $safePm = htmlspecialchars((string)$payload['pm_name'], ENT_QUOTES, 'UTF-8');
    $safeEntityEmail = htmlspecialchars((string)$payload['entity_email'], ENT_QUOTES, 'UTF-8');
    $safeClient = htmlspecialchars((string)$payload['client_name'], ENT_QUOTES, 'UTF-8');
    $safeAccountReference = htmlspecialchars((string)$payload['account_reference'], ENT_QUOTES, 'UTF-8');
    $safeRep = htmlspecialchars((string)$payload['rep_name'], ENT_QUOTES, 'UTF-8');
    $safeWorkType = htmlspecialchars((string)$payload['work_type_name'], ENT_QUOTES, 'UTF-8');
    $safeMeetingDay = htmlspecialchars((string)$payload['meeting_day'], ENT_QUOTES, 'UTF-8');
    $safeMeetingTime = htmlspecialchars((string)$payload['meeting_time'], ENT_QUOTES, 'UTF-8');
    $safeTriggerTime = htmlspecialchars((string)$payload['trigger_time'], ENT_QUOTES, 'UTF-8');
    $safeStartDate = htmlspecialchars((string)$payload['start_date'], ENT_QUOTES, 'UTF-8');
    $safeEndDate = htmlspecialchars((string)$payload['scheduled_end_date'], ENT_QUOTES, 'UTF-8');
    $safePlatform = htmlspecialchars((string)$payload['online_meeting'], ENT_QUOTES, 'UTF-8');
    $safeDescription = htmlspecialchars((string)$payload['description'], ENT_QUOTES, 'UTF-8');
    $safeMeetingLink = htmlspecialchars((string)$payload['meeting_link'], ENT_QUOTES, 'UTF-8');

    return "
<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <title>New Reminder Created</title>
  <style>
    .email-wrap { background-color:#f4f6f8; padding:30px 0; }
    .email-card { background:#ffffff; border-radius:8px; overflow:hidden; font-family:Arial,sans-serif; }
    .section-title { margin:18px 0 10px 0; color:#0f1f3d; font-size:15px; font-weight:700; }
    .content-text { color:#333; font-size:14px; line-height:1.55; margin:0 0 10px 0; }
    .mandate-table { width:100%; border-collapse:collapse; margin:12px 0 16px 0; }
    .mandate-table th { background:#0f1f3d; color:#ffffff; padding:10px; font-size:13px; text-align:left; border:1px solid #d7dde5; }
    .mandate-table td { padding:10px; font-size:13px; color:#2f3b4a; border:1px solid #d7dde5; vertical-align:top; }
    .mandate-table tr:nth-child(even) td { background:#f7f9fc; }
    .muted-note { color:#5c6675; font-size:13px; margin:0 0 10px 0; }
  </style>
</head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
  <table width='100%' cellpadding='0' cellspacing='0' class='email-wrap'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' class='email-card'>
        <tr>
          <td style='padding:20px;background:#0f1f3d;color:white;'>
            <table width='100%'>
              <tr>
                <td align='left'>
                  <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200' alt='OpenLinks'>
                </td>
                <td align='right' style='font-size:13px;line-height:18px;'>
                  <b>OpenLinks Corporations (Pty) Ltd</b><br>
                  314 Cape Road, Newton Park<br>
                  Port Elizabeth, Eastern Cape 6070
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style='padding:24px;color:#333;font-size:14px;'>
            <p class='content-text'><b>Dear {$safeTeam}, {$safeRep}, and Implicated Stakeholders,</b></p>
            <p class='content-text'>
              This email confirms activation of a Fixed Alignment &amp; Service Governance Mandate for the following:
            </p>

            <table class='mandate-table' cellpadding='0' cellspacing='0'>
              <thead>
                <tr>
                  <th colspan='2'>Mandate Details</th>
                </tr>
              </thead>
              <tbody>
                <tr><td><b>Entity</b></td><td>{$safePm}</td></tr>
                <tr><td><b>Account</b></td><td>{$safeClient}</td></tr>
                <tr><td><b>Account Representative</b></td><td>{$safeRep}</td></tr>
                <tr><td><b>Serving Team</b></td><td>{$safeTeam}</td></tr>
                <tr><td><b>Work Type</b></td><td>{$safeWorkType}</td></tr>
                <tr>
                <td><b>Effective Date</b><td>{$safeStartDate}</td></td>
                </tr>
                <tr><td><b>Description</b></td><td>{$safeDescription}</td></tr>
              </tbody>
            </table>

            <p class='section-title'>Fixed Meeting Structure</p>
            <table class='mandate-table' cellpadding='0' cellspacing='0'>
              <thead>
                <tr>
                  <th>Service Structure</th>
                  <th>Day</th>
                  <th>Time</th>
                  <th>Platform</th>
                  <th>Meeting Link</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Service Team and Account Representative</td>
                  <td>{$safeMeetingDay}</td>
                  <td>{$safeMeetingTime}</td>
                  <td>{$safePlatform}</td>
                  <td><a href='{$safeMeetingLink}' target='_blank' rel='noopener noreferrer'>{$safeMeetingLink}</a></td>
                </tr>
              </tbody>
            </table>
            <p class='muted-note'>
              These meetings are fixed to the above day and time for the duration of the mandate unless formally amended.
            </p>

            <p class='section-title'>Reminder Schedule</p>
            <p class='content-text'>Automated reminders will be issued at the following intervals for each meeting:</p>
            <table class='mandate-table' cellpadding='0' cellspacing='0'>
              <thead>
                <tr>
                  <th>Frequency</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Every {$safeEveryDays} day(s)</td>
                </tr>
              </tbody>
            </table>
            <p class='muted-note'>
              Reminders will include the meeting type, date, time, and format. Where necessary, online meeting links will be provided to ensure efficient alignment.
            </p>

            <p class='section-title'>Purpose</p>
            <p class='content-text'>
              These engagements ensure structured alignment, service quality oversight, issue resolution, and documented accountability.
            </p>
            <p class='content-text'>This structure remains active for the full duration of the mandate.</p>
            <p class='content-text'>For clarification, please contact {$safeEntityEmail}.</p>

            <p style='margin-top:18px;' class='content-text'>Kind regards,<br><b>OpenLinks Operations System</b></p>
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

function safeSendReminderCreatedEmails(mysqli $conn, array $payload): void
{
    $sendFile = __DIR__ . '/send_email.php';
    if (!is_file($sendFile)) {
        error_log('send_email.php not found; reminder emails skipped.');
        return;
    }

    require_once $sendFile;
    if (!function_exists('sendEmailNotification')) {
        error_log('sendEmailNotification() missing; reminder emails skipped.');
        return;
    }

    $recipients = [];

    $pm = fetchUserEmailName($conn, (int)$payload['who']);
    addUniqueRecipient($recipients, $pm['email'], $pm['name']);

    $client = fetchClientEmailName($conn, (int)$payload['account']);
    addUniqueRecipient($recipients, $client['email'], $client['name']);

    $rep = fetchRepEmailName($conn, (int)$payload['account_rep']);
    addUniqueRecipient($recipients, $rep['email'], $rep['name']);

    $teamInfo = fetchTeamNameAndMembers($conn, (int)$payload['team']);
    foreach ($teamInfo['members'] as $member) {
        addUniqueRecipient($recipients, (string)$member['email'], (string)$member['name']);
    }

    if (empty($recipients)) {
        return;
    }

    $workTypeName = fetchWorkTypeName($conn, (int)$payload['work_type']);
    $clientName = $client['name'] !== '' ? $client['name'] : ('Client #' . (int)$payload['account']);
    $repName = $rep['name'] !== '' ? $rep['name'] : ('Rep #' . (int)$payload['account_rep']);
    $pmName = $pm['name'] !== '' ? $pm['name'] : ('PM #' . (int)$payload['who']);
    $pmEmail = $pm['email'] !== '' ? $pm['email'] : 'support@openlinks.co.za';
    $teamName = $teamInfo['team_name'] !== '' ? $teamInfo['team_name'] : ('Team #' . (int)$payload['team']);

    $emailPayload = [
        'reminder_name' => (string)$payload['reminder_name'],
        'every_days' => (string)$payload['every_days'],
        'team_name' => (string)$teamName,
        'pm_name' => (string)$pmName,
        'entity_email' => (string)$pmEmail,
        'client_name' => (string)$clientName,
        'account_reference' => (string)$payload['account'],
        'rep_name' => (string)$repName,
        'work_type_name' => (string)$workTypeName,
        'meeting_day' => (string)$payload['meeting_day'],
        'meeting_time' => (string)$payload['meeting_time'],
        'trigger_time' => (string)$payload['trigger_time'],
        'start_date' => (string)$payload['start_date'],
        'scheduled_end_date' => (string)$payload['scheduled_end_date'],
        'online_meeting' => (string)$payload['online_meeting'],
        'meeting_link' => (string)$payload['meeting_link'],
        'description' => (string)$payload['description'],
    ];

    $subject = 'New Reminder Created: ' . (string)$payload['reminder_name'];
    foreach ($recipients as $recipient) {
        $html = buildReminderCreatedEmailHtml((string)$recipient['name'], $emailPayload);
        sendEmailNotification((string)$recipient['email'], $subject, $html);
    }
}

function safeInsertReminderCreatedPmNotification(mysqli $conn, int $pmId, int $teamId, int $reminderId): void
{
    if ($pmId <= 0 || $reminderId <= 0) {
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO pm_notifications (PM_ID, Job_ID, team_id, Notification_Type)
        VALUES (?, ?, ?, 887)
    ");

    if (!$stmt) {
        error_log('Failed to prepare reminder PM notification insert: ' . $conn->error);
        return;
    }

    $stmt->bind_param('iii', $pmId, $reminderId, $teamId);
    if (!$stmt->execute()) {
        error_log('Failed to insert reminder PM notification: ' . $stmt->error);
    }
    $stmt->close();
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo 'Invalid request method.';
    exit;
}

$who = (int)($_POST['manager_id'] ?? 0);
if ($who <= 0 && isset($_SESSION['login_id'])) {
    $who = (int)$_SESSION['login_id'];
}

$account = (int)($_POST['CLIENT_ID'] ?? 0);
$work_type = (int)($_POST['worktype_id'] ?? 0);
$reminder_name = substr(trim((string)($_POST['reminder_name'] ?? '')), 0, 225);
$every_days = (int)($_POST['every_days'] ?? 1);
$team = (int)($_POST['team_id'] ?? 0);
$account_rep = (int)($_POST['CLIENT_REP'] ?? 0);
$meeting_time = normalize_time((string)($_POST['meeting_time'] ?? ''));
$start_date = normalize_datetime((string)($_POST['start_date'] ?? ''));
$scheduled_end_date = normalize_datetime((string)($_POST['end_date'] ?? ''));
$meeting_day = substr(trim((string)($_POST['meeting_day'] ?? '')), 0, 225);
$trigger_time = $meeting_time;
$online_meeting = substr(trim((string)($_POST['online_meeting'] ?? '')), 0, 225);
$meeting_link = substr(trim((string)($_POST['meeting_link'] ?? '')), 0, 225);
$descriptionInput = (string)($_POST['description'] ?? '');
$descriptionPlain = html_entity_decode(strip_tags($descriptionInput), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$descriptionPlain = preg_replace('/\s+/', ' ', (string)$descriptionPlain);
$description = substr(trim((string)$descriptionPlain), 0, 225);
$status = (int)($_POST['status'] ?? 1);
$id = (int)($_POST['id'] ?? 0);
$loginType = (int)($_SESSION['login_type'] ?? 0);
$loginId = (int)($_SESSION['login_id'] ?? 0);

if (
    $who <= 0 ||
    $account <= 0 ||
    $work_type <= 0 ||
    $reminder_name === '' ||
    $every_days <= 0 ||
    $team <= 0 ||
    $account_rep <= 0 ||
    $meeting_time === '' ||
    $start_date === '' ||
    $scheduled_end_date === '' ||
    $meeting_day === '' ||
    $online_meeting === '' ||
    $meeting_link === '' ||
    !in_array($status, [0, 1], true)
) {
    http_response_code(422);
    echo 'Please complete all required reminder fields.';
    exit;
}

if (!filter_var($meeting_link, FILTER_VALIDATE_URL)) {
    http_response_code(422);
    echo 'Meeting link must be a valid URL.';
    exit;
}

if ($id > 0) {
    if (!in_array($loginType, [1, 2], true)) {
        http_response_code(401);
        echo 'unauthorized';
        exit;
    }

    if ($loginType === 2) {
        $ownerStmt = $conn->prepare("SELECT who FROM reminders WHERE id = ?");
        if (!$ownerStmt) {
            http_response_code(500);
            echo 'Unable to verify reminder owner.';
            exit;
        }
        $ownerStmt->bind_param('i', $id);
        $ownerStmt->execute();
        $ownerRow = $ownerStmt->get_result()->fetch_assoc();
        $ownerStmt->close();

        if (!$ownerRow || (int)$ownerRow['who'] !== $loginId) {
            http_response_code(401);
            echo 'unauthorized';
            exit;
        }
    }

    $stmt = $conn->prepare(
        "UPDATE reminders SET
            who = ?,
            account = ?,
            work_type = ?,
            reminder_name = ?,
            every_days = ?,
            team = ?,
            account_rep = ?,
            meeting_time = ?,
            start_date = ?,
            scheduled_end_date = ?,
            meeting_day = ?,
            trigger_time = ?,
            online_meeting = ?,
            meeting_link = ?,
            description = ?,
            status = ?
        WHERE id = ?"
    );

    if (!$stmt) {
        http_response_code(500);
        echo 'Unable to prepare update query.';
        exit;
    }

    $stmt->bind_param(
        'iiisiiisssssssssii',
        $who,
        $account,
        $work_type,
        $reminder_name,
        $every_days,
        $team,
        $account_rep,
        $meeting_time,
        $start_date,
        $scheduled_end_date,
        $meeting_day,
        $trigger_time,
        $online_meeting,
        $meeting_link,
        $description,
        $status,
        $id
    );
} else {
    $stmt = $conn->prepare(
        "INSERT INTO reminders (
            who,
            account,
            work_type,
            reminder_name,
            every_days,
            team,
            account_rep,
            meeting_time,
            start_date,
            scheduled_end_date,
            meeting_day,
            trigger_time,
            online_meeting,
            meeting_link,
            description,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        http_response_code(500);
        echo 'Unable to prepare save query.';
        exit;
    }

    $stmt->bind_param(
        'iiisiiissssssssi',
        $who,
        $account,
        $work_type,
        $reminder_name,
        $every_days,
        $team,
        $account_rep,
        $meeting_time,
        $start_date,
        $scheduled_end_date,
        $meeting_day,
        $trigger_time,
        $online_meeting,
        $meeting_link,
        $description,
        $status
    );
}

if ($stmt->execute()) {
    $shouldSendCreationEmail = ($id <= 0);
    $createdReminderId = $shouldSendCreationEmail ? (int)$stmt->insert_id : 0;

    if ($shouldSendCreationEmail) {
        safeInsertReminderCreatedPmNotification($conn, $who, $team, $createdReminderId);
    }

    $emailPayload = [
        'who' => $who,
        'account' => $account,
        'work_type' => $work_type,
        'reminder_name' => $reminder_name,
        'every_days' => $every_days,
        'team' => $team,
        'account_rep' => $account_rep,
        'meeting_time' => $meeting_time,
        'start_date' => $start_date,
        'scheduled_end_date' => $scheduled_end_date,
        'meeting_day' => $meeting_day,
        'trigger_time' => $trigger_time,
        'online_meeting' => $online_meeting,
        'meeting_link' => $meeting_link,
        'description' => $description,
    ];

    $stmt->close();

    if ($shouldSendCreationEmail) {
        // Respond immediately, then continue sending emails in background.
        finishAsyncResponse('1');

        try {
            safeSendReminderCreatedEmails($conn, $emailPayload);
        } catch (Throwable $t) {
            error_log('Reminder email send failed: ' . $t->getMessage());
        }

        exit;
    }

    echo '1';
    exit;
} else {
    http_response_code(500);
    echo 'Unable to save reminder: ' . $stmt->error;
}

$stmt->close();
