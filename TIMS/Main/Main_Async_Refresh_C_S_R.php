<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.class.php');

$temp= new SMME();
$temp->peek(0, 1);//0 means company and 1 means smme requested