<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/COMPANY.class.php');
include_once($filepath.'/../classes/M_ADMIN.class.php');
include_once($filepath.'/../helpers/token.php');

if(isset($_POST['tk']) && token::val($_POST['tk'], 'VIEW_MORE_CHARTS_YASC')){
    $temp = new COMPANY();
    $data = $temp->view_more_chart($_POST['id']);
    if(isset($data)){
       print_r($data);
    }
    return $data;
}else if(isset($_POST['tk']) && token::val($_POST['tk'], 'ADMIN_VIEW_MORE_CHARTS_YASC')){
    $temp = new MADMIN();
    $data = $temp->view_more_chart($_POST['id']);
    if(isset($data)){
       print_r($data);
    }
    return $data;
}
else{
    header("location: index.php?error=".$_POST['tk']);
}