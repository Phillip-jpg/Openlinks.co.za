<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include 'send_email.php';

function isAjaxRequest(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function flushClientResponse(string $body): void {
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

function handle_error(string $error_message): void {
    if (isAjaxRequest()) {
        if (!headers_sent()) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo "ERROR: " . $error_message;
        return;
    }

    echo "<p style='color:red;font-size:18px;font-weight:bold'>Error: " . htmlspecialchars($error_message) . "</p>";
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    handle_error("Invalid request method!");
    exit;
}

$isAjax = isAjaxRequest();

$pm_id     = (int)($_POST['pm_id'] ?? 0);
$member_id = (int)($_POST['member_id'] ?? 0);
$worktypes = $_POST['worktype_ids'] ?? [];
$orbiter   = (int)($_SESSION['login_id'] ?? 0);

if ($pm_id <= 0 || $member_id <= 0 || $orbiter <= 0) {
    echo "<p style='color:red;font-size:18px;font-weight:bold'>Invalid PM, Member, or Orbiter.</p>";
    exit;
}

if (!is_array($worktypes)) $worktypes = [$worktypes];
$worktypes = array_values(array_unique(array_filter(array_map('intval', $worktypes), fn($v) => $v > 0)));
$task_ids_csv = implode(',', $worktypes);

// Check if member already exists under this PM (your original check)
$checkSql = "SELECT 1 FROM users WHERE id = ? AND creator_id = ? LIMIT 1";
$check = $conn->prepare($checkSql);
$check->bind_param("ii", $member_id, $pm_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "
        <p style='color:orange;font-size:18px;font-weight:bold'>
            Member already exists under this PM.
        </p>
        <a href='index.php?page=orbit_member' class='btn btn-info btn-lg'>⬅ Back</a>
    ";
    exit;
}

$conn->begin_transaction();

try {
    // Insert orbit "branch" user row (your existing logic)
    $insertSql = "
        INSERT INTO users (
            id,
            creator_id,
            firstname,
            lastname,
            email,
            number,
            password,
            type,
            task_ids,
            avatar,
            date_created,
            orbit,
            orbiter_id
        )
        SELECT
            id,
            ?,
            firstname,
            lastname,
            email,
            number,
            password,
            type,
            ?,
            avatar,
            NOW(),
            1,
            ?
        FROM users
        WHERE id = ?
        LIMIT 1
    ";
    $insert = $conn->prepare($insertSql);
    $insert->bind_param("isii", $pm_id, $task_ids_csv, $orbiter, $member_id);

    if (!$insert->execute()) {
        throw new RuntimeException("User insert failed: " . $conn->error);
    }

    // Insert each worktype mapping
    $mwStmt = $conn->prepare("
        INSERT INTO members_and_worktypes (member_id, work_type_id)
        VALUES (?, ?)
    ");

    foreach ($worktypes as $workTypeId) {
        $mwStmt->bind_param("ii", $member_id, $workTypeId);
        if (!$mwStmt->execute()) {
            throw new RuntimeException("members_and_worktypes insert failed: " . $conn->error);
        }
    }

    $conn->commit();

    // ----------------------------
    // ✅ EMAILS AFTER SUCCESS
    // ----------------------------
    // Fetch PM + Member email + names
    $pmStmt = $conn->prepare("SELECT email, CONCAT(firstname,' ',lastname) AS name, number FROM users WHERE id = ? LIMIT 1");
    $pmStmt->bind_param("i", $pm_id);
    $pmStmt->execute();
    $pmData = $pmStmt->get_result()->fetch_assoc();

    $memStmt = $conn->prepare("SELECT email, CONCAT(firstname,' ',lastname) AS name, number FROM users WHERE id = ? LIMIT 1");
    $memStmt->bind_param("i", $member_id);
    $memStmt->execute();
    $memData = $memStmt->get_result()->fetch_assoc();

    $member_email   = $memData['email'] ?? '';
    $member_name    = $memData['name'] ?? 'Member';
    $member_number  = $memData['number'] ?? '';

    $manager_email  = $pmData['email'] ?? '';
    $manager_name   = $pmData['name'] ?? 'Manager';
    $manager_number = $pmData['number'] ?? '';

    $effective_date = date('Y-m-d');

    $subject_member = "You Have Been Assigned to Support a New Entity";
    $subject_pm     = "Resource Orbit  Member Assigned to Your Entity";

    $message_member = "
    <!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'><title>Resource Orbit</title></head>
    <body style='margin:0;padding:0;background-color:#f4f6f8;'>
      <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
        <tr><td align='center'>
          <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
            <tr>
              <td style='padding:20px;background:#0f1f3d;color:white;'>
                <table width='100%'><tr>
                  <td align='left'><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'></td>
                  <td align='right' style='font-size:13px;line-height:18px;'>
                    <b>OpenLinks Corporations (Pty) Ltd</b><br>
                    314 Cape Road, Newton Park<br>
                    Port Elizabeth, Eastern Cape 6070
                  </td>
                </tr></table>
              </td>
            </tr>
            <tr>
              <td style='padding:30px;color:#333;font-size:15px;'>
                <p>Hi <b>$member_name</b>,</p>
                <p>
                  This message is to let you know that you have been orbited to support a new entity as part of your role.
                </p>

                <p><b>Effective Date:</b> $effective_date</p>
                
                 <p>You will now also support: <b>$manager_name</b></p>

                <p><b>Thier Contact</b><br>
                  Email: $manager_email<br>
                  Contact Number: $manager_number
                </p>
                
                
                <p>This means your scope of work has been expanded to include tasks and responsibilities related to this entity.</p>
                
                <p>We are sending this message so that:
                    You know about the change. You know who to contact. You can ask questions if you are unsure about anything
                    .</p>

                <p>
                 Go to Openlinks
                </p>

                <div style='text-align:center;margin:35px 0;'>
                  <a href='https://openlinks.co.za/'
                     style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                    Go to Openlinks
                  </a>
                </div>

                <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
              </td>
            </tr>
            <tr>
              <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                <small>Automated Notification – Do not reply</small>
              </td>
            </tr>
          </table>
        </td></tr>
      </table>
    </body>
    </html>
    ";

    $message_pm = "
    <!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'><title>Resource Orbit</title></head>
    <body style='margin:0;padding:0;background-color:#f4f6f8;'>
      <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
        <tr><td align='center'>
          <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
            <tr>
              <td style='padding:20px;background:#0f1f3d;color:white;'>
                <table width='100%'><tr>
                  <td align='left'><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'></td>
                  <td align='right' style='font-size:13px;line-height:18px;'>
                    <b>OpenLinks Corporations (Pty) Ltd</b><br>
                    314 Cape Road, Newton Park<br>
                    Port Elizabeth, Eastern Cape 6070
                  </td>
                </tr></table>
              </td>
            </tr>
            <tr>
              <td style='padding:30px;color:#333;font-size:15px;'>
                <p>Hi <b>$manager_name</b>,</p>

                <p>
                  This message is to inform you that a resource has been orbited from another entity and assigned to support your entity.
                </p>

                <p><b>Member:</b> $member_name<br>
                   <b>Member Email:</b> $member_email<br>
                   <b>Member Number:</b> $member_number
                </p>

                <p><b>Effective Date:</b> $effective_date</p>
                
                
                 <p>
                  This orbiting means the member’s scope of work has been expanded to include responsibilities related to your entity.
                </p>
        
                <p>
                 We are sending this communication to ensure that: You are aware of the resource assignment. You can plan and allocate work accordingly. The member is managed within your entity’s operational processes
                </p>

                <div style='text-align:center;margin:35px 0;'>
                  <a href='https://openlinks.co.za/'
                     style='background:#0f1f3d;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                    Go to Openlinks
                  </a>
                </div>

                <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
              </td>
            </tr>
            <tr>
              <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                <small>Automated Notification – Do not reply</small>
              </td>
            </tr>
          </table>
        </td></tr>
      </table>
    </body>
    </html>
    ";

    if (!empty($member_email)) {
        sendEmailNotification("$member_email", $subject_member, $message_member);
    }
    if (!empty($manager_email)) {
        sendEmailNotification("$manager_email", $subject_pm, $message_pm);
    }

    echo "
        <p style='color:green;font-size:18px;font-weight:bold'>
            Member successfully assigned to PM!
        </p>
        <a href='index.php?page=orbit_member' class='btn btn-info btn-lg'>⬅ Back</a>
    ";

} catch (Throwable $e) {
    $conn->rollback();
    echo "
        <p style='color:red;font-size:18px;font-weight:bold'>
            Insert failed: " . htmlspecialchars($e->getMessage()) . "
        </p>
        <a href='index.php?page=orbit_member' class='btn btn-info btn-lg'>⬅ Back</a>
    ";
}
?>
