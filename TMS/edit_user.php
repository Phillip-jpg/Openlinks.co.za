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
$qry = $conn->query("SELECT * FROM users WHERE id = $id");
if (!$qry || $qry->num_rows === 0) {
    die('User not found');
}

$row = $qry->fetch_array();
foreach ($row as $k => $v) {
    $$k = $v;
}

include 'new_user.php';
