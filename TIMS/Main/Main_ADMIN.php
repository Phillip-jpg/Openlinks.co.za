<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/ADMIN.class.php');
include_once($filepath.'/../classes/admin.php');
include_once($filepath.'/../classes/M_ADMIN.class.php');
include_once($filepath.'/../helpers/token.php');

switch (true) {
    // case isset($_POST['ADMINSIGNUP'] )&& token::val($_POST['tk'], 'VIEW_MORE_CHARTS_YASC') :
    //     $temp= new Admin();
    //     $temp->SignUpAdmin($_POST['Name'], $_POST['Surname'], $_POST['email'], $_POST['pwd'], $_POST['pwd-repeat']);
    // break;
    // case isset($_POST['ADMINLOGIN']) && token::val($_POST['tk'], 'VIEW_MORE_CHARTS_YASC'):
    //     $temp= new Admin();
    //     $temp->LoginAdmin($_POST['email'], $_POST['pwd']);
    // break;
    // 
    
    case isset($_POST['identifier'])&& $_POST['identifier']=="SUMMARY_ANALYTICS_HEADER"  && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        $temp= new Admin();
        $temp->display_user_statistics();
    break;
     case isset($_POST['identifier'])&& $_POST['identifier']=="PROGRESS_PROCESS"  && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        $temp= new Admin();
        $temp->PROGRESS_PROCESS_SELECT();
    break;
    case isset($_POST['ADMINLOGOUPDATE']) && token::val($_POST['tk'], 'ADMINLOGOUPDATEYASC'):
        $temp= new Admin();
        $temp->UploadProfilePic($_FILES['logo']['name'], $_FILES['logo']['tmp_name'], $_FILES['logo']['size'], $_FILES['logo']['error']);
    break;
    case isset($_POST['ADMINPASSWORDUPDATE']) && token::val($_POST['tk'], 'ADMINPASSWORDYASC'):
        $temp= new Admin();
        $temp->updatepwd($_POST['old_pwd'], $_POST['new_pwd'], $_POST['pwd_repeat']);
    break;
     case isset($_POST['identifier']) && $_POST['identifier']=="PROCESS_AVERAGE_TIME" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        $temp= new Admin();
        $temp->PROCESS_AVERAGE_TIME_SELECT();
    break;

     case isset($_POST['identifier']) && $_POST['identifier']=="PAGE_VISITS_GRAPGH" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        $temp= new Admin();
        $temp->PAGE_VISITS_GRAPGH();
    break;

    case isset($_POST['identifier']) && $_POST['identifier']=="PAGE_VISITS" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        
        $temp= new Admin();
        
        $temp->page_visits();
    break;

    case isset($_POST['tk']) && token::val($_POST['tk'], 'MY_CONSULTANTS_YASC'):
        $temp= new Admin();
        $result = $temp->AllConsultants($_POST['page']);
        echo $result;
    break;
     case isset($_POST['identifier']) && $_POST['identifier']=="SEARCH_GRAPGH" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        $temp= new Admin();
        $temp->SEARCH_GRAPGH_SELECT();
    break;

     case isset($_POST['identifier']) && $_POST['identifier']=="SEARCH_SUMMARY" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
        $temp= new Admin();
        $temp->search_terms();
    break;

    case isset($_POST['CREATEADMIN']) && token::val_unauth($_POST['tk'], 'CREATEADMINYASC'):
        $temp= new Admin();
        $temp->AdminSignUp($_POST['Name'], $_POST['Surname'], $_POST['username'], $_POST['Email'], $_POST['pwd'], $_POST['pwd'], 1, $_POST['role'],$_POST['city'],$_POST['province'],$_POST['industries']);
    break;

    case isset($_POST['ADMIN_VERIFY'])://ADD token validation
        $temp= new Admin();
        $temp->verify_entity($_POST['userID'], $_POST['type']);
    break;
    case isset($_POST['PRINT_SMME_EXCEL'])://ADD token validation
        $temp= new Admin();
        $temp->printSMMEExcelData();
    break;

    //  case isset($_POST['identifier']) && $_POST['identifier']=="CURRENT_DAY_SEARCHES" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
    //     $temp= new MAdmin();
    //     $temp->CURRENT_DAY_SEARCHES_SELECT();
    // break;

    // case isset($_POST['identifier']) && $_POST['identifier']=="ALL_SENT_EMAILS" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
    //     $temp= new MAdmin();
    //     $temp->ALL_EMAILS_SENT_SELECT();
    // break;

    // case isset($_POST['identifier']) && $_POST['identifier']=="ALL_CLICKED_EMAILS" && token::val($_POST['tk'], 'ADMIN_ANALYTICS_YASC'):
    //     $temp= new MAdmin();
    //     $temp->ALL_CLICKED_EMAILS_SELECT();
    // break;
    default:
        echo $_POST['identifier'];
        echo "</br>".$_POST['tk'];
}

// if(isset($_POST['tk']) && token::val($_POST['tk'], 'VIEW_MORE_CHARTS_YASC')){
//     $temp = new COMPANY();
//     $data = $temp->view_more_chart($_POST['id']);
//     if(isset($data)){
//        print_r($data);
//     }
//     return $data;
// }
// else{
//     header("location: ../index.php?error=".$_POST['tk']);
// }