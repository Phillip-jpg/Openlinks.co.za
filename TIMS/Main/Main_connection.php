
<?php
    session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../helpers/token.php');
include_once($filepath.'/../classes/consultant.connection.php');
include_once($filepath.'/../classes/company.connection.php');
include_once($filepath.'/../helpers/token.php');
switch (true){
    case isset($_POST['tk']) && token::val($_POST['tk'], 'COMPANY_CREATE_LINK_YASC'):
        $temp= new company_connection();
        $temp->gen_link($_SESSION['COMPANY_ID']);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'GET_CONTROLLABLE_YASC'):
        $temp= new consultant_connection();
        $temp->get_controllable();
        break;
    case isset($_POST['establish_connection_yes']) && token::val($_POST['tk'], 'establish_connection_YASC'):
        $id = token::decode($_GET['id']);
        $temp = new company_connection($id);
        $temp ->enable_control($_SESSION['COMPANY_ID'], $id);
    break;
    
    case isset($_POST['establish_connection_no']) && token::val($_POST['tk'], 'establish_connection_YASC'):
        $id = token::decode($_GET['id']);
        $temp = new company_connection($id);
        $temp ->revoke_control($_SESSION['COMPANY_ID'], $id);
    break;

    case isset($_POST['P_COMPANY_LINK']) && token::val($_POST['tk'], 'P_COMPANY_CONTROL_YASC'):
        $id = token::decode($_POST['id']);
        $temp = new consultant_connection($id);
        $temp ->control($_POST['P_COMPANY_LINK']);
    break;
    default:
        echo "Oops you seem to be unauthorised.";
        exit();
}

