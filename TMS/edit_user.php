<?php
include 'db_connect.php';

$secret = 'my_app_secret_key';

if (!isset($_GET['id'])) {
    die('Invalid request');
}

$decoded = base64_decode($_GET['id'], true);
if ($decoded === false) {
    die('Invalid ID');
}

list($id, $hash) = explode(':', $decoded, 2);

// validate signature
$expected = hash_hmac('sha256', $id, $secret);
if (!hash_equals($expected, $hash)) {
    die('Tampered ID');
}

$id = (int)$id;

// now safe to query
$qrySql = "SELECT * FROM users WHERE id = $id";
if ((int)($_SESSION['login_type'] ?? 0) === 2 && !empty($_SESSION['login_id'])) {
    $loginId = (int)$_SESSION['login_id'];
    $qrySql .= " AND (creator_id = $loginId OR id = $loginId)";
    $qrySql .= " ORDER BY CASE WHEN creator_id = $loginId THEN 0 ELSE 1 END, orbit DESC, date_created DESC LIMIT 1";
} else {
    $qrySql .= " ORDER BY orbit DESC, date_created DESC LIMIT 1";
}
$qry = $conn->query($qrySql);
if (!$qry || $qry->num_rows === 0) {
    die('User not found');
}

$row = $qry->fetch_array();
foreach ($row as $k => $v) {
    $$k = $v;
}

include 'new_user.php';
