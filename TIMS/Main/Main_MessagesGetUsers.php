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
    }elseif(isset($_SESSION['ADMIN_ID'])){
        $id=$_SESSION['ADMIN_ID'];
    }elseif(isset($_SESSION['CONSULTANT_ID'])){
        $id=$_SESSION['CONSULTANT_ID'];
    }else{
        exit();
    }
$temp = new messages;
$temp->getusers($id);
// }else{
//     exit();
// }