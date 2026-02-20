<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * FROM configure_rate where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
include 'configure_rate_discount.php';
?>