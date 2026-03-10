<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

header('Content-Type: text/plain; charset=UTF-8');

if (empty($_SESSION['login_id'])) {
    http_response_code(401);
    echo 'unauthorized';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo 'invalid_method';
    exit;
}

$csrf = (string)($_POST['csrf_token'] ?? '');
if (
    empty($_SESSION['csrf_token']) ||
    $csrf === '' ||
    !hash_equals($_SESSION['csrf_token'], $csrf)
) {
    http_response_code(403);
    echo 'csrf';
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(422);
    echo 'invalid_input';
    exit;
}

$loginType = (int)($_SESSION['login_type'] ?? 0);
$loginId = (int)$_SESSION['login_id'];

if (!in_array($loginType, [1, 2], true)) {
    http_response_code(401);
    echo 'unauthorized';
    exit;
}

if ($loginType === 2) {
    $stmt = $conn->prepare("DELETE FROM reminders WHERE id = ? AND who = ?");
    if (!$stmt) {
        http_response_code(500);
        echo 'prepare_failed';
        exit;
    }
    $stmt->bind_param('ii', $id, $loginId);
} else {
    $stmt = $conn->prepare("DELETE FROM reminders WHERE id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo 'prepare_failed';
        exit;
    }
    $stmt->bind_param('i', $id);
}

try {
    $conn->begin_transaction();

    $attachmentStoredNames = [];
    $attachmentTableCheck = $conn->query("SHOW TABLES LIKE 'reminder_attachments'");
    $hasAttachmentsTable = ($attachmentTableCheck && $attachmentTableCheck->num_rows > 0);
    if ($hasAttachmentsTable) {
        $attachmentSelectStmt = $conn->prepare("SELECT stored_name FROM reminder_attachments WHERE reminder_id = ?");
        if ($attachmentSelectStmt) {
            $attachmentSelectStmt->bind_param('i', $id);
            $attachmentSelectStmt->execute();
            $attachmentRes = $attachmentSelectStmt->get_result();
            $attachmentSelectStmt->close();
            while ($attachmentRow = $attachmentRes->fetch_assoc()) {
                $storedName = (string)($attachmentRow['stored_name'] ?? '');
                if ($storedName !== '') {
                    $attachmentStoredNames[] = $storedName;
                }
            }
        }
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('Failed to delete parent reminder.');
    }

    if ($stmt->affected_rows <= 0) {
        $conn->rollback();
        $stmt->close();
        echo $loginType === 2 ? 'unauthorized' : 'not_found';
        exit;
    }

    $tableCheck = $conn->query("SHOW TABLES LIKE 'reminder_interval'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $childStmt = $conn->prepare("DELETE FROM reminder_interval WHERE parent_reminder_id = ?");
        if (!$childStmt) {
            throw new RuntimeException('Failed to prepare child delete.');
        }
        $childStmt->bind_param('i', $id);
        if (!$childStmt->execute()) {
            $childStmt->close();
            throw new RuntimeException('Failed to delete child reminders.');
        }
        $childStmt->close();
    }

    if (!empty($hasAttachmentsTable)) {
        $attachmentDeleteStmt = $conn->prepare("DELETE FROM reminder_attachments WHERE reminder_id = ?");
        if ($attachmentDeleteStmt) {
            $attachmentDeleteStmt->bind_param('i', $id);
            if (!$attachmentDeleteStmt->execute()) {
                $attachmentDeleteStmt->close();
                throw new RuntimeException('Failed to delete reminder attachment metadata.');
            }
            $attachmentDeleteStmt->close();
        }
    }

    $conn->commit();

    foreach ($attachmentStoredNames as $attachmentStoredName) {
        $attachmentPath = __DIR__ . DIRECTORY_SEPARATOR . 'reminder_uploads' . DIRECTORY_SEPARATOR . basename((string)$attachmentStoredName);
        if (is_file($attachmentPath)) {
            @unlink($attachmentPath);
        }
    }

    echo '1';
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    echo 'delete_failed';
} finally {
    $stmt->close();
}
