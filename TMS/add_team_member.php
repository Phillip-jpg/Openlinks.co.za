<?php
include 'db_connect.php';
include 'send_email.php'; // ✅ ensures sendEmailNotification() exists

$success = false;
$errorMessage = '';

// IMPORTANT: treat team_id as STRING (prevents losing leading zeros like "0042")
$team_id = ''; // define for the "Back" link even on error

// ------------------------------
// EMAIL HELPERS
// ------------------------------

function buildNewMemberAddedEmailHtml(string $memberName, string $teamName, string $managerName, array $teamMembers): string
{
    $safeMemberName = htmlspecialchars($memberName);
    $safeTeamName   = htmlspecialchars($teamName);
    $safeManager    = htmlspecialchars($managerName);

    $rows = '';
    foreach ($teamMembers as $m) {
        $n = htmlspecialchars($m['name']);
        $e = htmlspecialchars($m['email']);
        $rows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$n</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$e</td>
            </tr>
        ";
    }

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

            <p><small>Automated Notification – Do not reply</small></p>
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

function buildPmMembersAddedEmailHtml(string $pmName, string $teamName, string $teamId, array $addedMembers): string
{
    $safePm    = htmlspecialchars($pmName);
    $safeTeam  = htmlspecialchars($teamName);
    $safeTeamId = urlencode($teamId);

    $listRows = "";
    foreach ($addedMembers as $m) {
        $n = htmlspecialchars($m['name']);
        $e = htmlspecialchars($m['email']);
        $listRows .= "
            <tr>
                <td style='border:1px solid #d7dde5;padding:8px;'>$n</td>
                <td style='border:1px solid #d7dde5;padding:8px;'>$e</td>
            </tr>
        ";
    }

    return "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Team Updated</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
  <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0'
             style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
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
              This is to confirm that new member(s) have been added to the <b>$safeTeam</b> team.
            </p>

            <p>The following member(s) were added:</p>

            <table width='100%' cellpadding='0' cellspacing='0'
                   style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
              <thead>
                <tr>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Name</th>
                  <th align='left' style='border:1px solid #d7dde5;background:#f0f3f7;padding:8px;'>Email</th>
                </tr>
              </thead>
              <tbody>$listRows</tbody>
            </table>

            <div style='text-align:center;margin:35px 0;'>
              <a href='https://openlinks.co.za/index.php?page=team&team_id=$safeTeamId'
                 style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                View Team
              </a>
            </div>

            <p>
              Kind regards,<br>
              <b>OpenLinks Operations System</b>
            </p>

            <p><small>Automated Notification – Do not reply</small></p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>";
}

function fetchTeamMembersForEmail(mysqli $conn, string $team_id): array
{
    $stmt = $conn->prepare("
        SELECT DISTINCT
            mem.email AS member_email,
            CONCAT(mem.firstname, ' ', mem.lastname) AS member_name
        FROM team_schedule ts
        LEFT JOIN users mem ON mem.id = ts.team_members
        WHERE ts.team_id = ?
          AND ts.team_members IS NOT NULL
          AND ts.team_members <> ''
    ");
    if (!$stmt) return [];

    $stmt->bind_param("s", $team_id);
    $stmt->execute();
    $stmt->store_result();

    $email = $name = null;
    $stmt->bind_result($email, $name);

    $members = [];
    $seen = [];
    while ($stmt->fetch()) {
        if (!empty($email) && !empty($name) && empty($seen[$email])) {
            $members[] = ['email' => $email, 'name' => $name];
            $seen[$email] = true;
        }
    }
    $stmt->close();

    return $members;
}

function fetchManagerInfo(mysqli $conn, int $manager_id): array
{
    $stmt = $conn->prepare("SELECT email, CONCAT(firstname,' ',lastname) AS full_name FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) return ['email' => '', 'name' => 'Manager'];

    $stmt->bind_param("i", $manager_id);
    $stmt->execute();
    $stmt->store_result();

    $email = $full = null;
    $stmt->bind_result($email, $full);
    $stmt->fetch();
    $stmt->close();

    return [
        'email' => (string)$email,
        'name'  => $full ?: 'Manager',
    ];
}

function fetchUserEmailName(mysqli $conn, int $user_id): array
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

function sendNewMemberAddedEmail(mysqli $conn, string $team_id, int $new_member_id, int $manager_id, string $team_name): void
{
    if (!function_exists('sendEmailNotification')) return;

    $newUser = fetchUserEmailName($conn, $new_member_id);
    if (empty($newUser['email']) || !filter_var($newUser['email'], FILTER_VALIDATE_EMAIL)) return;

    $managerInfo = fetchManagerInfo($conn, $manager_id);
    $managerName = $managerInfo['name'];

    $members = fetchTeamMembersForEmail($conn, $team_id);

    $subject = "Welcome to the {$team_name} Team";
    $html = buildNewMemberAddedEmailHtml($newUser['name'], $team_name, $managerName, $members);

    sendEmailNotification($newUser['email'], $subject, $html);
}

function sendPmMembersAddedEmail(mysqli $conn, int $manager_id, string $team_name, string $team_id, array $addedMembers): void
{
    if (!function_exists('sendEmailNotification')) return;
    if (empty($addedMembers)) return;

    $pmInfo = fetchManagerInfo($conn, $manager_id);

    // ✅ If manager email is missing/invalid, log it so you can see why it didn't send
    if (empty($pmInfo['email']) || !filter_var($pmInfo['email'], FILTER_VALIDATE_EMAIL)) {
        error_log("PM email missing/invalid for manager_id={$manager_id}. Email found: ".$pmInfo['email']);
        return;
    }

    $subject = "Team Update: Member(s) added to {$team_name}";
    $html = buildPmMembersAddedEmailHtml($pmInfo['name'], $team_name, $team_id, $addedMembers);

    sendEmailNotification($pmInfo['email'], $subject, $html);
}


// ------------------------------
// MAIN LOGIC (same as yours + PM EMAIL + FIXES)
// ------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['team_id'], $_POST['manager_id'], $_POST['team_name'])) {

        // IMPORTANT: keep team_id as string (avoid losing leading zeros)
        $team_id = (string)trim($_POST['team_id']);

        $manager_id = intval($_POST['manager_id']);
        $team_name = trim($_POST['team_name']);
        $op_ids = trim($_POST['op_ids']);
        $worktype_id = intval($_POST['worktype_id']);
        $user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];

        $changed_op_ids = isset($_POST['changed_op_ids']) ? intval($_POST['changed_op_ids']) : 0;
        $changed_manager_id = isset($_POST['changed_manager_id']) ? intval($_POST['changed_manager_id']) : 0;

        // track all newly inserted members (for PM email)
        $addedMembersForPm = [];

        // Insert new team member
        if (!empty($user_ids)) {

            // normalize
            if (is_array($user_ids)) {
                $userIdsList = array_map('intval', $user_ids);
            } else {
                $userIdsList = [(int)$user_ids];
            }

            $insertQuery = $conn->prepare("
                INSERT INTO team_schedule (team_id, pm_manager, op_ids, team_name, team_members)
                VALUES (?, ?, ?, ?, ?)
            ");
            $checkQuery = $conn->prepare("
                SELECT COUNT(*) AS count
                FROM team_schedule
                WHERE team_id = ? AND team_members = ?
            ");

            if ($insertQuery && $checkQuery) {

                foreach ($userIdsList as $oneUserId) {

                    // ✅ avoid mysqlnd dependency (no get_result)
                    $checkQuery->bind_param('si', $team_id, $oneUserId);
                    $checkQuery->execute();
                    $checkQuery->store_result();

                    $count = 0;
                    $checkQuery->bind_result($count);
                    $checkQuery->fetch();

                    if ((int)$count === 0) {

                        // ✅ FIX bind types: team_id string, manager_id int, op_ids string, team_name string, team_members int
                        $insertQuery->bind_param('sissi', $team_id, $manager_id, $op_ids, $team_name, $oneUserId);

                        if (!$insertQuery->execute()) {
                            $errorMessage = "Insert Error: {$insertQuery->error}";
                            break;
                        } else {

                            // ✅ member_notifications ONCE per member
                            $insert_notifications = "
                                INSERT INTO member_notifications 
                                (PM_ID, Member_ID, Team_id, Notification_Type)
                                VALUES ($manager_id, $oneUserId, '".$conn->real_escape_string($team_id)."', 10)
                            ";
                            $conn->query($insert_notifications);

                            // ✅ collect added member for PM email
                            $u = fetchUserEmailName($conn, $oneUserId);
                            if (!empty($u['name']) || !empty($u['email'])) {
                                $addedMembersForPm[] = ['name' => $u['name'], 'email' => $u['email']];
                            }

                            // ✅ send email to the new member (after insert)
                            sendNewMemberAddedEmail($conn, $team_id, $oneUserId, $manager_id, $team_name);
                        }
                    }
                }

                $insertQuery->close();
                $checkQuery->close();

            } else {
                $errorMessage = "Prepare failed for insert/check query.";
            }
        }

        // ✅ pm_notifications ONCE per team (only if at least one member was added)
        if ($errorMessage === '' && !empty($addedMembersForPm)) {
            $teamIdSafe = $conn->real_escape_string($team_id);
            $insert_pm_notifications = "
                INSERT INTO pm_notifications 
                (PM_ID, team_id, Notification_Type)
                VALUES ($manager_id, '$teamIdSafe', 11)
            ";
            $conn->query($insert_pm_notifications);
        }

        // Update op_ids if changed (kept)
        if ($changed_op_ids !== 0) {
            $updateOpQuery = $conn->prepare("UPDATE team_schedule SET op_ids = ? WHERE team_id = ?");
            if ($updateOpQuery) {
                $changed_op_ids_int = (int)$changed_op_ids;
                $updateOpQuery->bind_param('is', $changed_op_ids_int, $team_id);
                $updateOpQuery->execute();
                $updateOpQuery->close();
            }
        }

        // Update manager_id (kept)
        if ($changed_manager_id !== 0) {
            $updateManagerQuery = $conn->prepare("UPDATE team_schedule SET pm_manager = ? WHERE team_id = ?");
            if ($updateManagerQuery) {
                $changed_manager_id_int = (int)$changed_manager_id;
                $updateManagerQuery->bind_param('is', $changed_manager_id_int, $team_id);
                $updateManagerQuery->execute();
                $updateManagerQuery->close();
            }
        }

        // Insert work type (your original logic, kept)
        if ($worktype_id !== 0) {
            $team_name_safe = $conn->real_escape_string($team_name);
            $op_ids_safe = $conn->real_escape_string($op_ids);
            $team_id_safe = $conn->real_escape_string($team_id);

            $sql = "
                INSERT INTO team_schedule (team_id, pm_manager, op_ids, team_name, worktype_ids)
                VALUES ('$team_id_safe', $manager_id, '$op_ids_safe', '$team_name_safe', $worktype_id)
            ";

            if (!$conn->query($sql)) {
                $errorMessage = "Insert Error (WorkType): {$conn->error}";
            }
        }

        // ✅ SEND PM EMAIL after DB operations (member inserts done)
        // This is the part you were missing in practice: team_id must match DB (string).
        if ($errorMessage === '' && !empty($addedMembersForPm)) {
            sendPmMembersAddedEmail($conn, $manager_id, $team_name, $team_id, $addedMembersForPm);
        }

        // ✅ If we reach here, consider it successful
        if ($errorMessage === '') {
            $success = true;
        }

    } else {
        $errorMessage = "Missing required fields.";
    }
} else {
    $errorMessage = "Invalid request method.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Save Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            padding: 40px;
        }
        .box {
            max-width: 600px;
            margin: auto;
            padding: 25px;
            border-radius: 6px;
        }
        .success {
            background: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #ef4444;
            color: #7f1d1d;
        }
        .btn {
            margin-top: 20px;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            font-size: 15px;
            background: #2563eb;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

<?php if ($success): ?>
    <div class="box success">
        <h2>✅ Success</h2>
        <p>Team details were saved successfully.</p>

        <a href="index.php?page=team&team_id=<?php echo htmlspecialchars($team_id); ?>" class="btn">
            ← Go Back to Team
        </a>
    </div>
<?php else: ?>
    <div class="box error">
        <h2>❌ Error</h2>
        <p><?php echo htmlspecialchars($errorMessage); ?></p>

        <a href="index.php?page=team&team_id=<?php echo htmlspecialchars($team_id); ?>" class="btn">
            ← Go Back to Team
        </a>
    </div>
<?php endif; ?>

</body>
</html>
