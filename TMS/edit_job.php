<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$secret = 'my_app_secret_key';

/* -----------------------------
   Decode encoded ID (robust)
------------------------------ */
if (!isset($_GET['id']) || $_GET['id'] === '') {
    die("Invalid request");
}

$decoded = base64_decode($_GET['id'], true);
if ($decoded === false) {
    die("Invalid job reference");
}

$id = null;

/* -------- LONG: id|rand|sig -------- */
$parts = explode('|', $decoded);
if (count($parts) === 3 && ctype_digit($parts[0])) {
    [$pid, $rand, $sig] = $parts;
    $expected = hash_hmac('sha256', $pid.'|'.$rand, $secret);
    if (hash_equals($expected, $sig)) {
        $id = (int)$pid;
    }
}

/* -------- SHORT: id|rand -------- */
if ($id === null && count($parts) === 2 && ctype_digit($parts[0])) {
    $id = (int)$parts[0];
}

/* -------- COLON: id:sig -------- */
if ($id === null) {
    $partsColon = explode(':', $decoded, 2);
    if (count($partsColon) === 2 && ctype_digit($partsColon[0])) {
        [$pid, $sig] = $partsColon;
        $expected = hash_hmac('sha256', $pid, $secret);
        if (hash_equals($expected, $sig)) {
            $id = (int)$pid;
        }
    }
}

if ($id === null || $id <= 0) {
    die("Invalid job reference");
}

/* -----------------------------
   Existing logic (UNCHANGED)
------------------------------ */
$_GET['id'] = $id;

$qry = $conn->query("SELECT * FROM project_list WHERE id = ".$_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
    $$k = $v;
}

include 'new_job.php';
