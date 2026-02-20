<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.class.php');
include_once($filepath.'/../classes/NPO.class.php');
include_once($filepath.'/../classes/COMPANY.class.php');
include_once($filepath.'/../classes/CONSULTANT.class.php');
include_once($filepath.'/../classes/M_ADMIN.class.php');
include_once($filepath.'/../classes/G_ADMIN.class.php');
include_once($filepath.'/../helpers/token.php');

switch (true) {

    //company
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANY_ANALYTICS_HEADER" && token::val($_POST['tk'], 'COMPANY_ANALYTICS_YASC'):
        $temp= new COMPANY();
        $temp->analytics_head();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANY_MARKETPLACE_HEADER" && token::val($_POST['tk'], 'COMPANY_ANALYTICS_YASC'):
        $temp= new COMPANY();
        $temp->marketplace_head();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANY_KEYWORD_GRAPGH" && token::val($_POST['tk'], 'COMPANY_ANALYTICS_YASC'):
        $temp= new COMPANY();
        $temp->KEYWORD_PERFORMANCE();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANY_SEARCH_GRAPGH" && token::val($_POST['tk'], 'COMPANY_ANALYTICS_YASC'):
        $temp= new COMPANY();
        $temp->SEARCH_GRAPGH();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANY_CONNECTIONS_GRAPGH" && token::val($_POST['tk'], 'COMPANY_ANALYTICS_YASC'):
        $temp= new COMPANY();
        $temp->ENTITY_CONNECTIONS_GRAPGH();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANY_PROFILE_STATS" && token::val($_POST['tk'], 'COMPANY_ANALYTICS_YASC'):
        $temp= new COMPANY();
        $temp->ENTITY_PROFILE_STATS();
    break;
//smme
    case isset($_POST['identifier']) && $_POST['identifier']=="SMME_ANALYTICS_HEADER" && token::val($_POST['tk'], 'SMME_ANALYTICS_YASC'):
        $temp= new SMME();
        $temp->analytics_head();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="SMME_KEYWORD_GRAPGH" && token::val($_POST['tk'], 'SMME_ANALYTICS_YASC'):
        $temp= new SMME();
        $temp->KEYWORD_PERFORMANCE();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="SMME_MARKETPLACE_HEADER" && token::val($_POST['tk'], 'SMME_ANALYTICS_YASC'):
        $temp= new SMME();
        $temp->marketplace_head();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="SMME_SEARCH_GRAPGH" && token::val($_POST['tk'], 'SMME_ANALYTICS_YASC'):
        $temp= new SMME();
        $temp->SEARCH_GRAPGH();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="SMME_CONNECTIONS_GRAPGH" && token::val($_POST['tk'], 'SMME_ANALYTICS_YASC'):
        $temp= new SMME();
        $temp->ENTITY_CONNECTIONS_GRAPGH();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="SMME_PROFILE_STATS" && token::val($_POST['tk'], 'SMME_ANALYTICS_YASC'):
        $temp= new SMME();
        $temp->ENTITY_PROFILE_STATS();
    break;
    default: 
    echo "THIS IS WHERE ITS KICKING ME OUT";
    exit();

}