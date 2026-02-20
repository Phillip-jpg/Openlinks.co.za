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
$status = (int)($_POST['status'] ?? -1);

if ($id <= 0 || !in_array($status, [0, 1], true)) {
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
    $stmt = $conn->prepare("UPDATE reminders SET status = ? WHERE id = ? AND who = ?");
    if (!$stmt) {
        http_response_code(500);
        echo 'prepare_failed';
        exit;
    }
    $stmt->bind_param('iii', $status, $id, $loginId);
} else {
    $stmt = $conn->prepare("UPDATE reminders SET status = ? WHERE id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo 'prepare_failed';
        exit;
    }
    $stmt->bind_param('ii', $status, $id);
}

if ($stmt->execute()) {
    echo '1';
} else {
    http_response_code(500);
    echo 'update_failed';
}

$stmt->close();
