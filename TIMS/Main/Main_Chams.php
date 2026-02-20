<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/chams.php');
include_once($filepath.'/../helpers/token.php');

switch (true) {
    //smmes
    case isset($_POST['tk']) && token::val($_POST['tk'], 'CHAMS_NOTIFICATIONS_ALL_YASC'):
        $temp= new chams();
        $temp->ALL();
    break;
    case isset($_POST['tk']) && isset($_POST['url']) && ($_POST['url'] !== '') && token::val($_POST['tk'], 'CHAMS_NOTIFICATIONS_SINGLE_YASC'):
        $temp= new chams();
        $temp->single($_POST['url']);
    break;
    default:
    echo $_POST['tk'];
    exit();
}