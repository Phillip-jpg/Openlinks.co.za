<?php
ini_set('display_errors', 1);
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/comp_posting.class.php');
include_once($filepath.'/../classes/cons_posting.class.php');
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../helpers/token.php');

switch(true){
    case isset($_POST["comp_post"]) && token::val($_POST['tk'], 'comp_post'):
        $temp = new comp_post();
        $temp->post($_POST["description"]); 
        break;
        
    case isset($_POST["comp_view"]) && token::val($_POST['tk'], 'comp_view'):
        $temp = new comp_post();
        $temp->viewpostings();
        break;

        case isset($_POST["cons_post"]) && token::val($_POST['tk'], 'cons_post'):
            $temp = new cons_post();
            $temp->post($_POST["description"]); 
            break;

        case isset($_POST["cons_view"]) && token::val($_POST['tk'], 'cons_view'):
            $temp = new cons_post();
            $temp->viewpostings();
            break;

        default:header("location: ../home.php");
        exit();
}