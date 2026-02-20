<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.class.php');
include_once($filepath.'/../classes/NPO.class.php');
include_once($filepath.'/../classes/COMPANY.class.php');
include_once($filepath.'/../classes/ADMIN.class.php');
include_once($filepath.'/../helpers/token.php');

switch (true) {
    //smmes
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYBBBEE_SMME_REQUESTED_YASC'):
        $temp= new SMME();
        $temp->ToView_entity_REQUESTED(1);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYBBBEE_COMPANY_REQUESTED_YASC'):
        $temp= new SMME();
        $temp->ToView_entity_REQUESTED(2);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYBBBEE_ALL_YASC'):
        $temp= new SMME();
        $temp->ToView_entity_ALL($_POST['page']);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'ADMIN_VIEW_SMME_YASC'):
        $temp= new Admin();
        $temp->ToView_smme_ALL($_POST['page']);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'ADMIN_VIEW_COMPANY_YASC'):
        $temp= new Admin();
        $temp->ToView_company_ALL($_POST['page']);
    break;
        case isset($_POST['tk']) && token::val($_POST['tk'], 'MYBBBEE_CHARTS_YASC'):
        $temp= new SMME();
        $temp->ToView_Charts();
    break;

    //companies
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYSMME_SMME_REQUESTED_YASC'):
        $temp= new COMPANY();
        $temp->ToView_entity_REQUESTED(1);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYSMME_COMPANY_REQUESTED_YASC'):
        $temp= new COMPANY();
        $temp->ToView_entity_REQUESTED(2);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYSMME_ALL_YASC'):
        $temp= new COMPANY();
        $temp->ToView_entity_ALL($_POST['page']);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MYSMME_CHARTS_YASC'):
        $temp= new COMPANY();
        $result = $temp->ToView_Charts();
        echo $result;
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'MY_CONSULTANTS_YASC'):
        $temp= new COMPANY();
        $result = $temp->MyConsultantSelect($_POST['page']);
        echo $result;
    break;

    //npos
    case isset($_POST['tk']) && token::val($_POST['tk'], 'NPO_MYBBBEE_NPO_REQUESTED_YASC'):
        $temp= new NPO();
        $temp->ToView_entity_REQUESTED(3);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'NPO_MYBBBEE_COMPANY_REQUESTED_YASC'):
        $temp= new NPO();
        $temp->ToView_entity_REQUESTED(2);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'NPO_MYBBBEE_ALL_YASC'):
        $temp= new NPO();
        $temp->ToView_entity_ALL($_POST['page']);
    break;
    case isset($_POST['tk']) && token::val($_POST['tk'], 'NPO_MYBBBEE_CHARTS_YASC'):
        $temp= new NPO();
        $temp->ToView_Charts();
    break;
    default:
    echo "is febby !";
    exit();
}

