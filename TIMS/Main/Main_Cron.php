<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/notifications_cron.php');

    $temp= new CRON();
    $temp->cron();
    