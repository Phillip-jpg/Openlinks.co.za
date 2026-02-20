<?php

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/consultant.connection.php');

$temp= new consultant_connection();
$temp->val_control();