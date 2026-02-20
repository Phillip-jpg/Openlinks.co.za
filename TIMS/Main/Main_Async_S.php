<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/COMPANY.class.php');

$temp= new COMPANY();
//$temp->ToView1(2);
$temp->ToView_entity_ALL();