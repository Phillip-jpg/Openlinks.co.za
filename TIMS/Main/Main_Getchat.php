<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/messages.class.php');

// if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
// {
$temp = new messages;
$temp->getchat($_POST['To']);
// }else{
//     exit();
// }