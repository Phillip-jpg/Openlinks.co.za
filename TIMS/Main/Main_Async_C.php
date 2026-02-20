<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.class.php');

$temp= new SMME();
$temp->ToView1(0);