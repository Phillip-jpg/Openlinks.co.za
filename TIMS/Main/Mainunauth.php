<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/Unauth.class.php');
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../helpers/token.php');


switch (true) {


case (isset($_POST['tk']) && isset($_POST['unauthsearchTerm'])) && token::val_unauth($_POST['tk'], 'OUTSIDESEARCHPAGEUNAUTH218621786YASC'):
    $temp= new unauth();
    $id = token::ip();
    $temp->search($_POST['unauthsearchTerm'], $id);
break;
case (isset($_POST['tk']) && isset($_POST['CONTACTFORM'])) && token::val_unauth($_POST['tk'], 'CONTACTFORMUNAUTH218621786YASC'):
    $temp= new unauth();
    $temp->contact_form($_POST['unauth_name'], $_POST['unauth_email'],$_POST['unauth_subject'], $_POST['unauth_message']);
break;

}