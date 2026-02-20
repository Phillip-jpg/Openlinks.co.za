<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/company.mode.php');
include_once($filepath.'/../classes/consultant.mode.php');
include_once($filepath.'/../helpers/token.php');

switch (true) {
    //company
    case isset($_POST['COMPANY_BOOKMARK']) && token::val($_POST['tk'], 'COMPANY_BOOKMARK'):
        $temp= new company_mode();
        $temp->bookmark($_POST['id']);
    break;
    case isset($_POST['COMPANY_INITIATE_NOTIFICATION']) && token::val($_POST['tk'], 'COMPANY_INITIATE_NOTIFICATION'):
        $temp= new company_mode();
        $temp->initiate_notification($_POST['id']);
    break;
    case isset($_POST['COMPANY_INITIATE_FORM']) && token::val($_POST['tk'], 'COMPANY_INITIATE_FORM'):
        $temp= new company_mode();
        if(isset($_POST['COMPANY_YES'])){
        $temp->initiate_form($_POST['id'], 1);
    }   elseif(isset($_POST['COMPANY_NO'])){
        $temp->initiate_form($_POST['id'], 0);
    }else{
        //default
    }
    break;
    case isset($_POST['GET_ALL']) && token::val($_POST['tk'], 'GET_ALL'):
        $temp= new company_mode();
        $temp->get_all();
    break;
    case isset($_POST['GET_INDIVIDUAL']) && token::val($_POST['tk'], 'GET_INDIVIDUAL'):
        $temp= new company_mode();
        $temp->get_individual($_POST['id']);
    break;
    case isset($_POST['CANDIDATE']) && token::val($_POST['tk'], 'CANDIDATE'):
        $temp= new company_mode();
        $temp->candidate($_POST['id']);
    break;
    case isset($_POST['REJECT']) && token::val($_POST['tk'], 'REJECT'):
        $temp= new company_mode();
        $temp->reject($_POST['id']);
    break;


    //consultant
    case isset($_POST['CONSULTANT_INITIATE_NOTIFICATION']) && token::val($_POST['tk'], 'CONSULTANT_INITIATE_NOTIFICATION'):
        $temp= new consultant_mode();
        $temp->initiate_notification($_POST['id']);
    break;
    case isset($_POST['CONSULTANT_INITIATE_FORM']) && token::val($_POST['tk'], 'CONSULTANT_INITIATE_FORM'):
        $temp= new consultant_mode();
        if(isset($_POST['CONSULTANT_YES'])){
        $temp->initiate_form($_POST['id'], 1);
    }   elseif(isset($_POST['CONSULTANT_NO'])){
        $temp->initiate_form($_POST['id'], 0);
    }else{
        //default
    }

}