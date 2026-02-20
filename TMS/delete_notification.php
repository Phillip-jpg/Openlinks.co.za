<?php
include 'db_connect.php';

$id = intval($_POST['id']);
$type = intval($_POST['type']);

if ($type == 2) {
    $conn->query("DELETE FROM pm_notifications WHERE id = $id");
} else {
    $conn->query("DELETE FROM member_notifications WHERE id = $id");
}

echo "OK";
?>
