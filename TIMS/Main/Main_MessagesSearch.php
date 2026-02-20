<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/messages.class.php');

// if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
// {
        if(isset($_SESSION['SMME_ID'])){
    $id=$_SESSION['SMME_ID'];
}elseif(isset($_SESSION['COMPANY_ID'])){
    $id=$_SESSION['COMPANY_ID'];
}else{
    exit();
}
$temp = new messages;
$temp->search($id, $_POST['search']);
// }else{
//     exit();
// }