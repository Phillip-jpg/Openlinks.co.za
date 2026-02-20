<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.class.php');

$temp= new SMME();
$temp->peek(0 , 0);//0 means company and 0 means all