<?php
include 'db_connect.php';
include 'send_email.php'; // ✅ ensures sendEmailNotification() exists

// -----------------------------------------------------------
// EMAIL HELPERS (KEEPING YOUR WORDING)
// -----------------------------------------------------------

/**
 * Fetch team + manager + members (team_id is stored as a 4-digit STRING like '0042')
 */
function fetchTeamInfo(mysqli $conn, string $team_id): array
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

    $stmt->bind_param("s", $team_id);
    $stmt->execute();
    $stmt->store_result();

    $teamName = $managerEmail = $managerName = $memberEmail = $memberName = null;
    $stmt->bind_result($teamName, $managerEmail, $managerName, $memberEmail, $memberName);

    $finalTeamName = null;
    $finalManagerEmail = null;
    $finalManagerName = null;

    $members = [];
    $seen = [];

    while ($stmt->fetch()) {
        if ($finalTeamName === null && !empty($teamName)) $finalTeamName = $teamName;
        if ($finalManagerEmail === null && !empty($managerEmail)) $finalManagerEmail = $managerEmail;
        if ($finalManagerName === null && !empty($managerName)) $finalManagerName = $managerName;

        // Avoid duplicates in email list
        if (!empty($memberEmail) && !empty($memberName) && empty($seen[$memberEmail])) {
            $members[] = ['email' => $memberEmail, 'name' => $memberName];
            $seen[$memberEmail] = true;
        }
    }

    $stmt->close();

    return [
        'team_name'     => $finalTeamName ?? 'Unknown Team',
        'manager_email' => $finalManagerEmail ?? '',
        'manager_name'  => $finalManagerName ?? 'Manager',
        'members'       => $members,
    ];
}

/**
 * Fetch a single user's email + full name (for newly added members)
 */
function fetchUserEmailAndName(mysqli $conn, int $user_id): array
{
    $stmt = $conn->prepare("SELECT email, CONCAT(firstname,' ',lastname) AS full_name FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) return ['email' => '', 'name' => ''];

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    $email = $name = null;
    $stmt->bind_result($email, $name);
    $stmt->fetch();
    $stmt->close();

    return ['email' => (string)$email, 'name' => (string)$name];
}

/**
 * Build members <tbody> rows (used in BOTH emails)
 */
function buildMembersRowsHtml(array $members): string
{
    $rows = '';
    foreach ($members as $m) {
        $n = htmlspecialchars($m['name']);
        $e = htmlspecialchars($m['email']);
        $rows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$n</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$e</td>
            </tr>
        ";
    }
    return $rows;
}

/**
 * Build welcome email HTML (YOUR ORIGINAL WORDING KEPT)
 */
function buildTeamWelcomeEmailHtml(string $memberName, string $teamName, string $managerName, string $rows): string
{
    $safeMemberName = htmlspecialchars($memberName);
    $safeTeamName   = htmlspecialchars($teamName);
    $safeManager    = htmlspecialchars($managerName);

    return "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Added to Team</title></head>
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
            <p>Dear <b>$safeMemberName</b>,</p>
            <p>Welcome to the <b>$safeTeamName</b> Team we are very pleased to have you join us.</p>

            <p>You have been added to a working team made up of the following members:</p>

            <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
              <thead>
                <tr>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Name</th>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Email</th>
                </tr>
              </thead>
              <tbody>$rows</tbody>
            </table>

            <p style='margin-top:16px;'>
              This team works closely together to deliver on its responsibilities, and each member plays an important role in ensuring successful outcomes.
              Your contribution is important, and the team looks forward to working with you.
            </p>
        
             <p style='margin-top:16px;'>
            The <b>$safeTeamName</b> Team reports on both delivery and guidance, meaning we focus not only on completing tasks, but also on supporting one another to achieve quality results in a structured and accountable way. 
            You will be working in an environment that values cooperation, communication, and shared responsibility.
            </p>
            
             <p style='margin-top:16px;'>
            We encourage you to introduce yourself to the team and feel free to reach out to any of the members if you need assistance getting started. 
            A positive and collaborative approach is part of how this team operates, and we are confident that you will be a great addition.
            </p>
            
            <p style='margin-top:16px;'>
            Once again, welcome to the <b>$safeTeamName</b> Team. We are excited to have you with us and look forward to working with you.
            </p>
            
            <p>
              Kind regards,<br>
              <b>$safeManager</b>
            </p>

            <div style='text-align:center;margin:35px 0;'>
              <a href='https://openlinks.co.za/index.php?page=priority_requests'
                 style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                Go to Openlinks
              </a>
            </div>

            <p><small>Automated Notification Do not reply</small></p>
          </td>
        </tr>

        <tr>
          <td style='background:#f0f3f7;padding:20px;font-size:12px;color:#555;text-align:center;'>
            Telephone: 041 004 0454 &nbsp;|&nbsp;
            <a href='https://www.openlinks.co.za' style='color:#0f1f3d;text-decoration:none;'>www.openlinks.co.za</a>
            <br><br><small>Automated Notification Do not reply</small>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>";
}

/**
 * Manager email (YOUR ORIGINAL WORDING KEPT) - FILE SECTION REMOVED
 */
function buildManagerTeamCreatedEmailHtml(string $managerName, string $teamName, string $rows): string
{
    $safeManager  = htmlspecialchars($managerName);
    $safeTeamName = htmlspecialchars($teamName);

    return "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>Team Created</title>
</head>

<body style='margin:0;padding:0;background-color:#f4f6f8;'>
<table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
<tr>
<td align='center'>

<table width='600' cellpadding='0' cellspacing='0'
style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>

<!-- HEADER -->
<tr>
<td style='padding:20px;background:#0f1f3d;color:white;'>
<table width='100%'>
<tr>
<td align='left'>
<img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'>
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

<!-- BODY -->
<tr>
<td style='padding:30px;color:#333;font-size:15px;'>

<p>Dear <b>$safeManager</b>,</p>

<p>
A new team $safeTeamName has been created to support your entity.
</p>

<p>It has the following members:</p>

<table width='100%' cellpadding='8' cellspacing='0'
style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
<tbody>$rows</tbody>
</table>

<!-- BUTTON -->
<div style='text-align:center;margin:35px 0;'>
<a href='https://openlinks.co.za/index.php?page=productivity_pipeline'
style='background:#0f1f3d;color:#ffffff;padding:14px 30px;
text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
View Job Pipeline
</a>
</div>

<p>
Kind regards,<br>
<b>OpenLinks Operations System</b>
</p>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
<small>Automated Notification – Do not reply</small>
</td>
</tr>

</table>

</td>
</tr>
</table>
</body>
</html>
";
}

/**
 * Send welcome emails to EACH MEMBER + manager email
 * (members list comes from DB after insert)
 */
function sendEmailsAfterTeamCreate(mysqli $conn, string $team_id): void
{
    if (!function_exists('sendEmailNotification')) {
        error_log("sendEmailNotification() not found; emails skipped.");
        return;
    }

    $info = fetchTeamInfo($conn, $team_id);

    $teamName     = $info['team_name'];
    $managerName  = $info['manager_name'];
    $managerEmail = $info['manager_email'];
    $membersList  = $info['members'];

    if (empty($membersList)) {
        error_log("No members found for team_id: ".$team_id);
        return;
    }

    $rows = buildMembersRowsHtml($membersList);

    // ✅ 1) SEND TO MANAGER
    if (!empty($managerEmail) && filter_var($managerEmail, FILTER_VALIDATE_EMAIL)) {
        $subjectManager = "A Team Has Been created $teamName";
        $messageManager = buildManagerTeamCreatedEmailHtml($managerName, $teamName, $rows);
        sendEmailNotification($managerEmail, $subjectManager, $messageManager);
    }

    // ✅ 2) SEND TO EACH MEMBER
    $subjectMember = "Welcome to the {$teamName} Team";
    foreach ($membersList as $m) {
        $email = $m['email'] ?? '';
        $name  = $m['name'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $htmlMember = buildTeamWelcomeEmailHtml($name, $teamName, $managerName, $rows);
        sendEmailNotification($email, $subjectMember, $htmlMember);
    }
}

// -----------------------------------------------------------
// MAIN POST HANDLER
// -----------------------------------------------------------

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $isAjax = isAjaxRequest();
    if (isset($_POST['team_name'], $_POST['manager_id'], $_POST['user_ids'], $_POST['worktype_ids'], $_POST['op_ids'])) {

        $team_name     = (string)$_POST['team_name'];
        $manager_id    = (int)$_POST['manager_id'];

        // NOTE: keep as int because you had it as int here; if this is not numeric, change to (string)
        $op_ids        = (int)$_POST['op_ids'];

        $user_ids      = isset($_POST['user_ids']) && is_array($_POST['user_ids'])
            ? array_values(array_unique(array_map('intval', $_POST['user_ids'])))
            : [];
        $work_type_ids = isset($_POST['worktype_ids']) && is_array($_POST['worktype_ids'])
            ? array_values(array_unique(array_map('intval', $_POST['worktype_ids'])))
            : [];

        // Always include the selected entity/manager as a team member.
        if ($manager_id > 0 && !in_array($manager_id, $user_ids, true)) {
            $user_ids[] = $manager_id;
        }

        if (!empty($user_ids) && !empty($work_type_ids)) {

            $result = $conn->query("SELECT team_id FROM team_schedule");
            $existingTeamIds = $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'team_id') : [];

            do {
                $team_id = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            } while (in_array($team_id, $existingTeamIds, true));

            $stmt = $conn->prepare(
                "INSERT INTO team_schedule 
                 (team_id, pm_manager, op_ids, team_name, team_members, worktype_ids)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            foreach ($user_ids as $user_id) {
                $user_id = (int)$user_id;

                foreach ($work_type_ids as $work_type_id) {
                    $work_type_id = (int)$work_type_id;

                    // ✅ FIXED bind types: team_id string (s), manager int (i), op_ids int (i), team_name string (s), user_id int (i), worktype_id int (i)
                    $stmt->bind_param(
                        'siisii',
                        $team_id,
                        $manager_id,
                        $op_ids,
                        $team_name,
                        $user_id,
                        $work_type_id
                    );

                    if (!$stmt->execute()) {
                        die("Insert failed: " . $stmt->error);
                    }
                }

                // ✅ member_notifications ONCE per member
                $insert_notifications = "
                    INSERT INTO member_notifications 
                    (PM_ID, Member_ID, Team_id, Notification_Type)
                    VALUES ($manager_id, $user_id, '$team_id', 8)
                ";
                $conn->query($insert_notifications);
            }

            // ✅ pm_notifications ONCE per team
            $insert_pm_notifications = "
                INSERT INTO pm_notifications 
                (PM_ID, team_id, Notification_Type)
                VALUES ($manager_id, '$team_id', 9)
            ";
            $conn->query($insert_pm_notifications);

            $stmt->close();

            // ✅ SEND EMAILS AFTER INSERT (MANAGER + EACH MEMBER)
            if ($isAjax) {
                ignore_user_abort(true);
                if (session_status() === PHP_SESSION_ACTIVE) {
                    @session_write_close();
                }
                flushClientResponse('OK');
                sendEmailsAfterTeamCreate($conn, $team_id);
                exit;
            }

            sendEmailsAfterTeamCreate($conn, $team_id);

            echo '
            <p style="
                font-size:15px;
                font-weight:600;
                color:#14532d;
                margin-bottom:10px;
            ">
                ✔ Action completed successfully.
            </p>
            
            <a href="index.php?page=schedule_teams_lvl2"
               style="
                   display:inline-block;
                   font-size:14px;
                   font-weight:600;
                   color:#1d4ed8;
                   text-decoration:none;
                   border-bottom:2px solid transparent;
                   transition:all .2s ease;
               "
               onmouseover="this.style.borderBottomColor=\'#1d4ed8\'"
               onmouseout="this.style.borderBottomColor=\'transparent\'"
            >
                ← Go back to Schedule Teams
            </a>
            ';
        }
    }
}
?>
