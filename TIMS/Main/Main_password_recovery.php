<?php
ini_set('display_errors', 1);
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/Password_R.php');
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../helpers/token.php');

switch(true){
    case isset($_POST["mail"]) && isset($_GET["w"]) && token::val_unauth($_POST['tk'], 'mailYASC')://submit buttons?
        $temp = new pass_r($_GET["w"]);
        $temp->mail_tokens($_POST["email"]); 
        break;
    case isset($_POST["pass"]) && isset($_GET["w"]) && token::val_unauth($_POST['tk'], 'passYASC')://submit buttons?
        
        $temp = new pass_r($_GET["w"]);
        
        $temp->savepassword($_POST["password"], $_POST["passwordR"], $_GET["s"], $_GET["v"]);
        break;
        default:header("location: ../home.php");
        exit();
}
