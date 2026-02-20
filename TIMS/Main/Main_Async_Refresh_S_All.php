<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/COMPANY.class.php');

$temp= new COMPANY();
$temp->peek(2, 0);//2 means smme and 0 means all