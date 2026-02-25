<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit(0);
}

$logFile = __DIR__ . DIRECTORY_SEPARATOR . 'email_bg.log';
$log = static function (string $msg) use ($logFile): void {
    @file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND);
};

$memberId = 0;
$createdType = 0;
foreach ($argv as $arg) {
    if (preg_match('/^--member_id=(\d+)$/', $arg, $m)) {
        $memberId = (int)$m[1];
    } elseif (preg_match('/^--type=(\d+)$/', $arg, $m)) {
        $createdType = (int)$m[1];
    }
}

if ($memberId <= 0) {
    $log('exit: invalid member_id');
    exit(0);
}

require __DIR__ . '/db_connect.php';
require __DIR__ . '/send_email.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    $log('exit: db connection missing');
    exit(0);
}

$conn->set_charset('utf8mb4');
$log("start: member_id={$memberId}, type={$createdType}");

$userQuery = $conn->query("
    SELECT
        u.id,
        u.type,
        u.email AS member_email,
        u.number AS member_number,
        u.date_created,
        CONCAT(u.firstname, ' ', u.lastname) AS member_name,
        CONCAT(uc.firstname, ' ', uc.lastname) AS manager_name,
        uc.email AS manager_email,
        uc.number AS manager_number
    FROM users u
    LEFT JOIN users uc ON uc.id = u.creator_id
    WHERE u.id = {$memberId}
    LIMIT 1
");

if (!$userQuery || $userQuery->num_rows === 0) {
    $log("exit: user not found for member_id={$memberId}");
    $conn->close();
    exit(0);
}

$row = $userQuery->fetch_assoc();
$memberEmail = (string)($row['member_email'] ?? '');
$memberNumber = (string)($row['member_number'] ?? '');
$memberName = trim((string)($row['member_name'] ?? ''));
$managerEmail = (string)($row['manager_email'] ?? '');
$managerNumber = (string)($row['manager_number'] ?? '');
$managerName = trim((string)($row['manager_name'] ?? ''));
$dateCreated = (string)($row['date_created'] ?? '');
$roleType = (int)($row['type'] ?? 0);

if ($memberName === '') {
    $memberName = 'Member';
}
if ($managerName === '') {
    $managerName = 'Entity Manager';
}

$roleName = 'User';
if ($roleType === 1) {
    $roleName = 'Super Admin';
} elseif ($roleType === 2) {
    $roleName = 'Project Manager';
} elseif ($roleType === 3) {
    $roleName = 'Member';
} elseif ($roleType === 4) {
    $roleName = 'Admin Assistant';
}

$h = static function (?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

$subjectMember = 'Welcome Your Member Account Has Been Created';

$messageMember = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Welcome</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
<table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
<tr><td align='center'>
<table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
<tr><td style='padding:20px;background:#0f1f3d;color:white;'>
<b>OpenLinks Corporations (Pty) Ltd</b>
</td></tr>
<tr><td style='padding:30px;color:#333;font-size:15px;'>
<p>Hi <b>{$h($memberName)}</b>,</p>
<p>Your member account has been created successfully under entity <b>{$h($managerName)}</b>.</p>
<table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
<tr><td style='background:#f0f3f7;width:35%;'><b>Role</b></td><td>{$h($roleName)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Email</b></td><td>{$h($memberEmail)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Contact</b></td><td>{$h($memberNumber)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Start Date</b></td><td>{$h($dateCreated)}</td></tr>
</table>
<p style='margin-top:15px;'><b>Entity Manager:</b> {$h($managerName)}<br>Email: {$h($managerEmail)}<br>Contact Number: {$h($managerNumber)}</p>
<p>Kind regards,<br><b>OpenLinks Operations System</b></p>
</td></tr>
<tr><td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
<small>Automated Notification - Do not reply</small>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>";

$mainAdminName = 'Main Admin';
$mainAdminEmail = '';
$mainAdminNumber = '';

if ($createdType === 2) {
    $adminQuery = $conn->query("
        SELECT CONCAT(firstname, ' ', lastname) AS name, email, number
        FROM users
        WHERE type = 1
        ORDER BY id ASC
        LIMIT 1
    ");
    if ($adminQuery && $adminQuery->num_rows > 0) {
        $admin = $adminQuery->fetch_assoc();
        $mainAdminName = trim((string)($admin['name'] ?? 'Main Admin'));
        if ($mainAdminName === '') {
            $mainAdminName = 'Main Admin';
        }
        $mainAdminEmail = (string)($admin['email'] ?? '');
        $mainAdminNumber = (string)($admin['number'] ?? '');
    }

    $subjectMember = 'Welcome to OpenLinks - Entity Created';
    $messageMember = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Welcome to OpenLinks</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
<table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
<tr><td align='center'>
<table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
<tr><td style='padding:20px;background:#0f1f3d;color:white;'><b>OpenLinks Corporations (Pty) Ltd</b></td></tr>
<tr><td style='padding:30px;color:#333;font-size:15px;'>
<p>Hi <b>{$h($memberName)}</b>,</p>
<p>Welcome to OpenLinks. You have been created as an entity on the system.</p>
<p>You can use the platform to manage your teams and jobs.</p>
<p>Here are your details:</p>
<table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
<tr><td style='background:#f0f3f7;width:35%;'><b>Entity Name</b></td><td>{$h($memberName)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Role</b></td><td>Entity (Project Manager)</td></tr>
<tr><td style='background:#f0f3f7;'><b>Email</b></td><td>{$h($memberEmail)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Phone</b></td><td>{$h($memberNumber)}</td></tr>
</table>
<p style='margin-top:18px;'>If you need assistance, please contact the Main Admin:</p>
<table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
<tr><td style='background:#f0f3f7;width:35%;'><b>Main Admin</b></td><td>{$h($mainAdminName)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Email</b></td><td>{$h($mainAdminEmail)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Contact Number</b></td><td>{$h($mainAdminNumber)}</td></tr>
</table>
<p>Kind regards,<br><b>OpenLinks Operations System</b></p>
</td></tr>
<tr><td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'><small>Automated Notification - Do not reply</small></td></tr>
</table>
</td></tr>
</table>
</body>
</html>";
}

$subjectManager = 'Resource Has Been Created for your Entity';
$messageManager = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Resource Created</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
<table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
<tr><td align='center'>
<table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
<tr><td style='padding:20px;background:#0f1f3d;color:white;'><b>OpenLinks Corporations (Pty) Ltd</b></td></tr>
<tr><td style='padding:30px;color:#333;font-size:15px;'>
<p>Hi <b>{$h($managerName)}</b>,</p>
<p>A resource has been created and assigned to support your entity.</p>
<table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
<tr><td style='background:#f0f3f7;width:35%;'><b>Member</b></td><td>{$h($memberName)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Role</b></td><td>{$h($roleName)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Email</b></td><td>{$h($memberEmail)}</td></tr>
</table>
<p>Kind regards,<br><b>OpenLinks Operations System</b></p>
</td></tr>
<tr><td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
<small>Automated Notification - Do not reply</small>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>";

if ($memberEmail !== '') {
    $ok = sendEmailNotification($memberEmail, $subjectMember, $messageMember);
    $log('member email send to ' . $memberEmail . ' result=' . ($ok ? 'ok' : 'fail'));
}

if ($createdType === 2) {
    $mainAdminRecipient = $mainAdminEmail !== '' ? $mainAdminEmail : $managerEmail;
    if ($mainAdminRecipient !== '') {
        $subjectMainAdmin = 'New Entity Created in OpenLinks System';
        $messageMainAdmin = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>Entity Created</title></head>
<body style='margin:0;padding:0;background-color:#f4f6f8;'>
<table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
<tr><td align='center'>
<table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
<tr><td style='padding:20px;background:#0f1f3d;color:white;'><b>OpenLinks Corporations (Pty) Ltd</b></td></tr>
<tr><td style='padding:30px;color:#333;font-size:15px;'>
<p>Hi <b>{$h($mainAdminName)}</b>,</p>
<p>An entity has been created in the system.</p>
<table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
<tr><td style='background:#f0f3f7;width:35%;'><b>Entity Name</b></td><td>{$h($memberName)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Role</b></td><td>Entity (Project Manager)</td></tr>
<tr><td style='background:#f0f3f7;'><b>Email</b></td><td>{$h($memberEmail)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Phone</b></td><td>{$h($memberNumber)}</td></tr>
<tr><td style='background:#f0f3f7;'><b>Date Created</b></td><td>{$h($dateCreated)}</td></tr>
</table>
<p>Kind regards,<br><b>OpenLinks Operations System</b></p>
</td></tr>
<tr><td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'><small>Automated Notification - Do not reply</small></td></tr>
</table>
</td></tr>
</table>
</body>
</html>";
        $ok = sendEmailNotification($mainAdminRecipient, $subjectMainAdmin, $messageMainAdmin);
        $log('main admin email send to ' . $mainAdminRecipient . ' result=' . ($ok ? 'ok' : 'fail'));
    }
} elseif ($managerEmail !== '') {
    $ok = sendEmailNotification($managerEmail, $subjectManager, $messageManager);
    $log('manager email send to ' . $managerEmail . ' result=' . ($ok ? 'ok' : 'fail'));
}

$conn->close();
$log('done');
exit(0);
