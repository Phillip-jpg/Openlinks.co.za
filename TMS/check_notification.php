<?php
// check_notifications.php
session_start();
include('db_connect.php');

$response = ['count' => 0];
$login_id = isset($_SESSION['login_id']) ? intval($_SESSION['login_id']) : 0;
$login_type = isset($_SESSION['login_type']) ? intval($_SESSION['login_type']) : 0;

if ($login_id > 0) {
    if ($login_type == 2) {
        $qry = $conn->query("SELECT COUNT(*) as count FROM pm_notifications WHERE PM_ID = $login_id");
        $row = $qry->fetch_assoc();
        $response['count'] = $row['count'];
    } elseif ($login_type == 3) {
        $qry = $conn->query("SELECT COUNT(*) as count FROM member_notifications WHERE Member_ID = $login_id");
        $row = $qry->fetch_assoc();
        $response['count'] = $row['count'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>