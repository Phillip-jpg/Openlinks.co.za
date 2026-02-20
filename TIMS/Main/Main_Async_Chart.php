<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/COMPANY.class.php');

$temp= new COMPANY();
$results = $temp->chart();
echo $results;