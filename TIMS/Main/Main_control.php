<?php

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/consultant.connection.php');

$link=(isset($_GET['lk']))? $_GET['lk'] : "";
$temp= new consultant_connection();
$temp->control($link);
