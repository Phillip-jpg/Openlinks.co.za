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

$qry = $conn->query("SELECT * FROM client_rep where REP_ID = ".$id)->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
include 'new_client_rep.php';
?>