<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/COMPANY.class.php');

$temp= new COMPANY();
$temp->peek(2, 2);//2 means smme and 2 means company requested