<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/extra.class.php');

        
$temp= new extra();
if(isset($_POST['notrequired'])){
$temp->officesnr($_POST['OFFICE_ID']);
}
else{
$temp->offices($_POST['OFFICE_ID']);
}
