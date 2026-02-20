<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/COMPANY.class.php');

$temp= new COMPANY();
$temp->peek(2, 1);//2 means company and 1 means smme requested