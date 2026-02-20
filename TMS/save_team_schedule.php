<?php
/**
 * File: schedule_team_assign.php
 * Always redirect after saving; email failures must not break the request.
 */

include 'db_connect.php';
include 'send_email.php';

function handleError(string $message, ?mysqli $conn = null, ?mysqli_stmt $stmt = null): void
{
    if ($stmt) $stmt->close();
    if ($conn) $conn->close();

    echo "<p style='color:red; font-size:20px; font-weight:bold'>" . htmlspecialchars($message) . "</p>";
    exit;
}

function isValidYmd(string $date): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt) return false;

    $errors = DateTime::getLastErrors();
    if (!empty($errors['warning_count']) || !empty($errors['error_count'])) return false;

    return $dt->format('Y-m-d') === $date;
}

function isAjaxRequest(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function flushClientResponse(string $body): void
{
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

/**
 * Build member email HTML (includes scheduled weeks + team members list)
 */
function buildAssignedWeeksEmailHtml(string $recipientName, string $teamName, array $insertedRanges, array $teamMembers): string
{
    $weeksRows = '';
    foreach ($insertedRanges as $range) {
        $start = htmlspecialchars($range['start']);
        $end   = htmlspecialchars($range['end']);
        $weeksRows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$start</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$end</td>
            </tr>
        ";
    }

    $membersRows = '';
    foreach ($teamMembers as $m) {
        $n = htmlspecialchars($m['name']);
        $e = htmlspecialchars($m['email']);
        $membersRows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$n</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$e</td>
            </tr>
        ";
    }

    $safeName = htmlspecialchars($recipientName);
    $safeTeam = htmlspecialchars($teamName);

    return "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Team Assigned Work</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
  <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
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
          <td style='padding:30px;color:#333;font-size:15px;'>
            <p>Dear <b>$safeName</b>,</p>
            <p>Your team <b>$safeTeam</b> has been assigned to work the following weeks:</p>

            <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
              <thead>
                <tr>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Start week</th>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>End week</th>
                </tr>
              </thead>
              <tbody>$weeksRows</tbody>
            </table>

            <p style='margin-top:18px;'>Your team members are:</p>

            <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:10px;'>
              <thead>
                <tr>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Member</th>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Email</th>
                </tr>
              </thead>
              <tbody>$membersRows</tbody>
            </table>

            <div style='text-align:center;margin:35px 0;'>
              <a href='https://openlinks.co.za/index.php?page=priority_requests'
                 style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                Go to Openlinks
              </a>
            </div>

            <p>Thank you for your attention and timely action.</p>
            <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
          </td>
        </tr>

        <tr>
          <td style='background:#f0f3f7;padding:20px;font-size:12px;color:#555;text-align:center;'>
            Telephone: 041 004 0454 &nbsp;|&nbsp;
            <a href='https://www.openlinks.co.za' style='color:#0f1f3d;text-decoration:none;'>www.openlinks.co.za</a>
            <br><br><small>Automated Notification – Do not reply</small>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>";
}

/**
 * Build PM email HTML (team scheduled + weeks + members)
 */
function buildPmTeamScheduledEmailHtml(string $pmName, string $teamName, array $insertedRanges, array $teamMembers): string
{
    $safePm   = htmlspecialchars($pmName);
    $safeTeam = htmlspecialchars($teamName);

    $weeksRows = '';
    foreach ($insertedRanges as $range) {
        $start = htmlspecialchars($range['start']);
        $end   = htmlspecialchars($range['end']);
        $weeksRows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$start</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$end</td>
            </tr>
        ";
    }

    $membersRows = '';
    foreach ($teamMembers as $m) {
        $n = htmlspecialchars($m['name']);
        $e = htmlspecialchars($m['email']);
        $membersRows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$n</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$e</td>
            </tr>
        ";
    }

    return "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Team Scheduled</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
  <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
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
          <td style='padding:30px;color:#333;font-size:15px;'>
            <p>Dear <b>$safePm</b>,</p>

            <p>
              This is to confirm that the team <b>$safeTeam</b> has been scheduled to work the following weeks:
            </p>

            <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
              <thead>
                <tr>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Start week</th>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>End week</th>
                </tr>
              </thead>
              <tbody>$weeksRows</tbody>
            </table>

            <p style='margin-top:18px;'>Team members:</p>

            <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:10px;'>
              <thead>
                <tr>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Member</th>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Email</th>
                </tr>
              </thead>
              <tbody>$membersRows</tbody>
            </table>

            <div style='text-align:center;margin:35px 0;'>
              <a href='https://openlinks.co.za/index.php?page=schedule_teams_lvl3'
                 style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                View Schedule
              </a>
            </div>

            <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
            <p><small>Automated Notification – Do not reply</small></p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>";
}

/**
 * Fetch team info + manager + members
 * NOTE: team_id might be a 4-digit string; use "s" bind and string $teamId
 */
function fetchTeamInfo(mysqli $conn, int $teamId): array
{
    $stmt = $conn->prepare("
        SELECT DISTINCT
            ts.team_name,
            pm.email AS manager_email,
            CONCAT(pm.firstname, ' ', pm.lastname) AS manager_name,
            mem.email AS member_email,
            CONCAT(mem.firstname, ' ', mem.lastname) AS member_name
        FROM team_schedule ts
        LEFT JOIN users pm  ON pm.id  = ts.pm_manager
        LEFT JOIN users mem ON mem.id = ts.team_members
        WHERE ts.team_id = ?
    ");
    if (!$stmt) {
        throw new RuntimeException("Prepare failed (team info): {$conn->error}");
    }

    $stmt->bind_param("i", $teamId);
    $stmt->execute();
    $stmt->store_result();

    $teamName = $managerEmail = $managerName = $memberEmail = $memberName = null;
    $stmt->bind_result($teamName, $managerEmail, $managerName, $memberEmail, $memberName);

    $finalTeamName = null;
    $finalManagerEmail = null;
    $finalManagerName = null;

    // store members unique by email, but also build list array later for email tables
    $membersByEmail = [];

    while ($stmt->fetch()) {
        if ($finalTeamName === null && !empty($teamName)) $finalTeamName = $teamName;
        if ($finalManagerEmail === null && !empty($managerEmail)) $finalManagerEmail = $managerEmail;
        if ($finalManagerName === null && !empty($managerName)) $finalManagerName = $managerName;

        if (!empty($memberEmail) && !empty($memberName)) {
            $membersByEmail[$memberEmail] = $memberName;
        }
    }

    $stmt->close();

    $membersList = [];
    foreach ($membersByEmail as $email => $name) {
        $membersList[] = ['email' => $email, 'name' => $name];
    }

    return [
        'team_name'      => $finalTeamName ?? 'Unknown Team',
        'manager_email'  => (string)($finalManagerEmail ?? ''),
        'manager_name'   => (string)($finalManagerName ?? 'Manager'),
        'members_map'    => $membersByEmail,
        'members_list'   => $membersList,
    ];
}

/**
 * Send emails to members + PM. Email failures must not break flow.
 */
function safeSendAssignedWeeksEmails(mysqli $conn, int $teamId, array $insertedRanges): void
{
    if (empty($insertedRanges)) return;

    if (!function_exists('sendEmailNotification')) {
        error_log("sendEmailNotification() not found; emails skipped.");
        return;
    }

    $info = fetchTeamInfo($conn, $teamId);

    $teamName     = $info['team_name'];
    $pmEmail      = $info['manager_email'];
    $pmName       = $info['manager_name'];
    $membersList  = $info['members_list'];
    $subject      = "Team {$teamName} Assigned working Weeks";

    // ✅ email each member
    foreach ($membersList as $m) {
        $email = $m['email'];
        $name  = $m['name'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $html = buildAssignedWeeksEmailHtml($name, $teamName, $insertedRanges, $membersList);
        sendEmailNotification($email, $subject, $html);
    }

    // ✅ email PM with full details too
    if (!empty($pmEmail) && filter_var($pmEmail, FILTER_VALIDATE_EMAIL)) {
        $pmSubject = "Team Scheduled: {$teamName}";
        $pmHtml = buildPmTeamScheduledEmailHtml($pmName, $teamName, $insertedRanges, $membersList);
        sendEmailNotification($pmEmail, $pmSubject, $pmHtml);
    } else {
        error_log("PM email missing/invalid for teamId={$teamId}. PM email found: ".$pmEmail);
    }
}

// ------------------------------
// MAIN
// ------------------------------
$isAjax = isAjaxRequest();

if (!isset($_POST['team_id'], $_POST['period_ids']) || !is_array($_POST['period_ids'])) {
    if ($isAjax) {
        http_response_code(400);
        echo 'INVALID_SUBMISSION';
        exit;
    }
    handleError("Invalid submission: Please select a team and periods.", $conn);
}

$teamId  = (int) $_POST['team_id'];
$periods = $_POST['period_ids'];

if ($teamId <= 0 || empty($periods)) {
    if ($isAjax) {
        http_response_code(400);
        echo 'INVALID_TEAM_OR_PERIODS';
        exit;
    }
    handleError("Invalid team or periods selected.", $conn);
}

$duplicates = [];
$insertedRanges = [];

$conn->begin_transaction();

$insertStmt = $conn->prepare("
    INSERT INTO schedule_work_team (Work_Team, startweek, endweek)
    VALUES (?, ?, ?)
");
if (!$insertStmt) {
    handleError("Prepare failed: {$conn->error}", $conn);
}

try {
    foreach ($periods as $range) {
        if (!is_string($range) || strpos($range, '|') === false) {
            throw new RuntimeException("Invalid week format received.");
        }

        [$startweek, $endweek] = explode('|', $range, 2);
        $startweek = trim($startweek);
        $endweek   = trim($endweek);

        if (!isValidYmd($startweek) || !isValidYmd($endweek)) {
            throw new RuntimeException("Invalid date format detected.");
        }
        if ($startweek > $endweek) {
            throw new RuntimeException("Start week cannot be after end week ($startweek → $endweek).");
        }

        $insertStmt->bind_param("iss", $teamId, $startweek, $endweek);

        if (!$insertStmt->execute()) {
            if ($insertStmt->errno === 1062) {
                $duplicates[] = "$startweek → $endweek";
                continue;
            }
            throw new RuntimeException("Insert failed: {$insertStmt->error}");
        }

        $insertedRanges[] = ['start' => $startweek, 'end' => $endweek];
    }

    $conn->commit();
    $insertStmt->close();

    // ✅ Redirect ALWAYS happens now
    if ($isAjax) {
        ignore_user_abort(true);
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_write_close();
        }
        flushClientResponse(!empty($duplicates) ? 'OK_WITH_DUPLICATES' : 'OK');

        try {
            safeSendAssignedWeeksEmails($conn, $teamId, $insertedRanges);
        } catch (Throwable $t) {
            error_log("Email send failed after ajax response: " . $t->getMessage());
        }

        $conn->close();
        exit;
    }

    $redirectUrl = !empty($duplicates)
        ? "index.php?page=schedule_teams_lvl3&warning=" . urlencode("Some weeks were skipped (already assigned).")
        : "index.php?page=schedule_teams_lvl3&saved=1";

    header("Location: $redirectUrl", true, 303);

    // Flush response, keep script running for emails (if supported).
    ignore_user_abort(true);
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        @ob_end_flush();
        flush();
    }

    // ✅ Email failures won't break the user redirect
    try {
        safeSendAssignedWeeksEmails($conn, $teamId, $insertedRanges);
    } catch (Throwable $t) {
        error_log("Email send failed after redirect: " . $t->getMessage());
    }

    $conn->close();
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    if ($isAjax) {
        if (isset($insertStmt) && $insertStmt instanceof mysqli_stmt) {
            $insertStmt->close();
        }
        http_response_code(500);
        echo 'SAVE_FAILED: ' . $e->getMessage();
        $conn->close();
        exit;
    }
    handleError($e->getMessage(), $conn, $insertStmt);
}
