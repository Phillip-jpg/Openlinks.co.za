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

    //admin


    case isset($_POST['identifier']) && $_POST['identifier']=="EMAIL_VERIFICATION_ADMIN" && token::val_unauth($_POST['tk'], 'EMAIL_VERIFICATION_YASC'):
        $temp= new Admin();
        $temp->verify_account($_POST['email'], $_POST['link']);
    break;
    case isset($_POST['ADMINLOGIN']) && token::val_unauth($_POST['tk'], 'ADMINLOGINYASC'):
        $temp= new Admin();
        $temp->login($_POST['Username'], $_POST['pwd'], $_GET['r']);
    break;



    //smme insert
    case isset($_POST['SMMESIGNUP']) && token::val_unauth($_POST['tk'], 'EMAIL_VERIFICATION_YASC'):
        $temp= new SMME();
        $temp->signup($_POST['Name'], $_POST['Surname'], $_POST['Username'], $_POST['email'], $_POST['pwd'], $_POST['pwd-repeat'], 1, $_GET['r']);
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=='EMAIL_VERIFICATION_SMME' && token::val_unauth($_POST['tk'], 'EMAIL_VERIFICATION_YASC'):
        $temp= new SMME();
        $temp->verify_account($_POST['email'], $_POST['link']);
    break;
    case isset($_POST['SMMELOGIN']) && token::val_unauth($_POST['tk'], 'SMMELOGINYASC'):
        $temp= new SMME();
        $temp->login($_POST['Username'], $_POST['pwd'], $_GET['r']);
        exit();
    break;
    case isset($_POST['SMMEREGISTER']) && token::val($_POST['tk'], 'SMMEREGISTER'):
        $temp= new SMME();
        $temp->register($_POST['tradename'],$_POST['legalname'], $_POST['regnum'], $_POST['address'], $_POST['postal'], $_POST['city'], $_POST['province'], $_POST['contact'], $_POST['email'],$_POST['foo'], $_POST['offices'], $_POST['industries'],$_POST['financial']);
    break;
    case isset($_POST['SMMEADMIN']) && token::val($_POST['tk'], 'SMMEADMIN'):
        $temp= new SMME();
        $temp->admin($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Email'], $_POST['Race']);
    break;
    case isset($_POST['CompanyData']) && token::val($_POST['tk'], 'CompanyData'):
        $temp= new SMME();
        $temp->registerCompanyData($_POST['LegalName'], $_POST['TradingName'], $_POST['CC'], $_POST['FinancialYear']);
    break;
    case isset($_POST['DE']) && token::val($_POST['tk'], 'DE'):
        $temp= new SMME();
        $temp->expensesummary($_POST['serviceprovider'], $_POST['productname'], $_POST['productspecification'], $_POST['randvalue'], $_POST['frequency'], 0);
    break;
    case isset($_POST['NDE']) && token::val($_POST['tk'], 'NDE'):
        $temp= new SMME();
        $temp->expensesummary($_POST['serviceprovider'], $_POST['productname'], $_POST['productspecification'], $_POST['randvalue'], $_POST['frequency'], 1);
    break;
    case isset($_POST['Company_DOCUMENTS_YASC']) && token::val($_POST['tk'], 'Company_DOCUMENTS_YASC'):
        $temp= new SMME();
     
        $temp->addCompanyDocuments($_POST['TotalNoShareholders'], $_POST['NoBlackShareholders'], $_POST['WhiteShareholders'], $_POST['BlackOwnershipP'], $_POST['BlackFemaleP'], $_POST['WhiteOwnershipP'], $_POST['BBBEEStatus'], $_POST['DOI'], $_POST['ED'], $_FILES['filebbbee']['name'], $_FILES['filebbbee']['tmp_name'], $_FILES['filebbbee']['size'], $_FILES['filebbbee']['error'], $_FILES['filereg']['name'], $_FILES['filereg']['tmp_name'], $_FILES['filereg']['size'], $_FILES['filereg']['error']);
    break;
    case isset($_POST['Company_directors_YASC']) && token::val($_POST['tk'], 'Company_directors_YASC'):
        $temp= new SMME();
        $temp->Directors($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Race'], $_FILES['IDcopy']['name'], $_FILES['IDcopy']['tmp_name'], $_FILES['IDcopy']['size'], $_FILES['IDcopy']['error']);
    break;
    case isset($_POST['UploadProfilePic']) && token::val($_POST['tk'], 'UploadProfilePic'):
        $temp= new SMME();
        $temp->UploadProfilePic($_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error']);
    break;

    // SMME update
    case isset($_POST['SMMEADMINUPDATE']) && token::val($_POST['tk'], 'SMMEADMINUPDATE'):
        $temp= new SMME();
        $temp->adminSMMEUpdate($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Email'], $_POST['Race']);
    break;
    case isset($_POST['SMMEREGISTERUPDATE']) && token::val($_POST['tk'], 'SMMEREGISTERUPDATE'):
        $temp= new SMME();
        $temp->REGISTERSMMEUpdate($_POST['Tradename'],$_POST['legalname'], $_POST['regnum'], $_POST['address'], $_POST['postal'], $_POST['city'], $_POST['province'], $_POST['contact'], $_POST['email'],$_POST['foo'], $_POST['industries'],$_POST['financial']);
    break;
    case isset($_POST['SMMEREGISTERUPDATE']) && token::val($_POST['tk'], 'SMMEREGISTERUPDATE'):
        $temp= new SMME();
        $temp->SMMEDIRECTORUPDATE($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Race'], $_FILES['IDcopy']['name'], $_FILES['IDcopy']['tmp_name'], $_FILES['IDcopy']['size'], $_FILES['IDcopy']['error']);
    break;
    case isset($_POST['SMMESTATEMENTUPDATE']) && token::val($_POST['tk'], 'SMMESTATEMENTUPDATE'):
        $temp= new SMME();
        $temp->SMMEStatementUPDATE($_POST['Introduction'], $_POST['Vision'], $_POST['Mission'], $_POST['Values'], $_POST['Goals_Objectives']);
    break;
    case isset($_POST['SMMEDOCUPDATE']) && token::val($_POST['tk'], 'SMMEDOCUPDATE'):
        $temp= new SMME();
        $temp->SMMESDocUPDATE($_POST['TotalNoShareholders'], $_POST['NoBlackShareholders'], $_POST['WhiteShareholders'], $_POST['BlackOwnershipP'], $_POST['BlackFemaleP'], $_POST['WhiteOwnershipP'], $_POST['BBBEEStatus'], $_POST['DOI'], $_POST['ED'], $_FILES['filebbbee']['name'], $_FILES['filebbbee']['tmp_name'], $_FILES['filebbbee']['size'], $_FILES['filebbbee']['error'], $_FILES['filereg']['name'], $_FILES['filereg']['tmp_name'], $_FILES['filereg']['size'], $_FILES['filereg']['error']);
    break;
    case isset($_POST['submit_statements']) && token::val($_POST['tk'], 'submit_statements'):
        $temp= new SMME();
        $temp->addCompanyStatements($_POST['Introduction'], $_POST['Vision'], $_POST['Mission'], $_POST['Values'], $_POST['Goals_Objectives']);
    break;
    case isset($_POST['Products']) && token::val($_POST['tk'], 'submit_products'):
        $temp= new SMME();
        $temp->products_services($_POST['productname'], $_POST['productdes'],$_POST['productprice'],$_FILES['productimg']['name'], $_FILES['productimg']['tmp_name'], $_FILES['productimg']['size'], $_FILES['productimg']['error'] );
    break;
    case isset($_POST['SMMEPRODUCTUPDATE']) && token::val($_POST['tk'], 'SMMEPRODUCTUPDATE'):
        $temp= new SMME();
        $temp->products_update($_POST['productname'], $_POST['productdes'], $_POST['productprice'],$_POST['productIDS']);
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="LINKVISITS" && token::val($_POST['tk2'], 'SMME_LINK_VISITS_YASC'):
        $temp= new SMME();
        $temp->insert_Websiteviews($_POST['link'], $_POST['id']);
    break;
    case isset($_POST['smme_business_links']) && token::val($_POST['tk'], 'smme_business_links'):
        $temp= new SMME();
        $temp->smme_links($_POST['links'], $_POST['ids']);
    break;
    case isset($_POST['SMMEKEYWORDUPDATE']) && token::val($_POST['tk'], 'SMMEKEYWORDUPDATE'):
        $temp= new SMME();
        $temp->smmekeywordsUpdate($_POST['keywords'], $_POST['ids']);
    break;
    case isset($_POST['SMMEKEYWORDS']) && token::val($_POST['tk'], 'SMMEKEYWORDSYASC'):
        $temp= new SMME();
        $temp->smmekeywords($_POST['keywords']);
    break;

case isset($_POST['identifier']) && $_POST['identifier']=="ADMINSDISPLAY" && token::val($_POST['tk'], 'ADMINSDISPLAY'):
        $temp= new SMME();
        $temp->ADMINS_FILTERED($_POST['type'], $_POST['data']);
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="ADMINS" && token::val($_POST['tk'], 'ADMINSDISPLAY'):
        $temp= new SMME();
        $temp->displayAdmins();
    break;
        //delete SMME 
    case isset($_POST['SMMEADMINDELETE']) && token::val($_POST['tk'], 'SMMEADMINDELETE'):
        $temp= new SMME();
        $temp->smmeDeleteAdmin();
    break;
    case isset($_POST['SMMEREGISTERDELETE']) && token::val($_POST['tk'], 'SMMEREGISTERDELETE'):
        $temp= new SMME();
        $temp->smmeDeleteRegister();
    break;
    case isset($_POST['COMPANYDIRDELETE']) && token::val($_POST['tk'], 'COMPANYDIRDELETE'):
        $temp= new SMME();
        $temp->smmeDeleteDir();
    break;
    case isset($_POST['COMPANYSTATEDELETE']) && token::val($_POST['tk'], 'COMPANYSTATEDELETE'):
        $temp= new SMME();
        $temp->smmeDeleteState();
    break;
    case isset($_POST['COMPANYDOCDELETE']) && token::val($_POST['tk'], 'COMPANYDOCDELETE'):
        $temp= new SMME();
        $temp->smmeDeleteDoc();
    break;
 
    case isset($_POST['COMPANYLINKSDELETE']) && token::val($_POST['tk'], 'COMPANYLINKSDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteLinks();
    break;
    case isset($_POST['COMPANYKEYWORDSDELETE']) && token::val($_POST['tk'], 'COMPANYKEYWORDSDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteKeywords();
    break;
    case isset($_POST['COMPANYREGISTERDELETE']) && token::val($_POST['tk'], 'COMPANYREGISTERDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteRegister();
    break;
    case isset($_POST['SMMELOGOUPDATE']) && token::val($_POST['tk'], 'SMMELOGOYASC'):
        $temp= new SMME();
        $temp->UploadProfilePic($_FILES['logo']['name'], $_FILES['logo']['tmp_name'], $_FILES['logo']['size'], $_FILES['logo']['error']);
    break;
    case isset($_POST['SMMEDELETEPRODUCT']) && token::val($_POST['tk'], 'SMMEDELETEYASC'):
        $temp= new SMME();
        $temp->deleteproduct($_GET['id']);
    break;
    
case isset($_POST['tk']) && isset($_POST['id']) && token::val($_POST['tk'], 'SMME_UPDATE_NOTIFICICATIONS_CHAMS'):
        
        $temp= new SMME();
        
        $temp->updateNotification($_POST['id']);
      break;
    

    
    //company
    case isset($_POST['COMPANYSIGNUP']) && token::val_unauth($_POST['tk'], 'COMPANYSIGNUPYASC'):
        $temp= new COMPANY();
        $temp->signup($_POST['Name'], $_POST['Surname'], $_POST['Username'], $_POST['email'], $_POST['pwd'], $_POST['pwd-repeat'], 1, $_GET['r']);
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="EMAIL_VERIFICATION_COMPANY" && token::val_unauth($_POST['tk'], 'EMAIL_VERIFICATION_YASC'):
        $temp= new COMPANY();
        $temp->verify_account($_POST['email'], $_POST['link']);
    break;
    case isset($_POST['COMPANYLOGIN']) && token::val_unauth($_POST['tk'], 'COMPANYLOGINYASC'):
        $temp= new COMPANY();
        $temp->login($_POST['Username'], $_POST['pwd'], $_GET['r']);
    break;
    case isset($_POST['COMPANYREGISTER']) && token::val($_POST['tk'], 'COMPANYREGISTER'):
        $temp= new COMPANY();
        $temp->registerCompany($_POST['tradename'],$_POST['legalname'], $_POST['regnum'], $_POST['address'], $_POST['postal'], $_POST['city'], $_POST['province'], $_POST['contact'], $_POST['email'],$_POST['foo'], $_POST['industries'],$_POST['financial']);
    //needs editing
    break;
    case isset($_POST['COMPANYADMIN']) && token::val($_POST['tk'], 'COMPANYADMIN'):
        $temp= new COMPANY();
        $temp->adminCompany($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Email'], $_POST['Race']);
    break;
     case isset($_POST['tk']) && isset($_POST['id']) && token::val($_POST['tk'], 'COMPANY_UPDATE_NOTIFICICATIONS_CHAMS'):
        $temp= new COMPANY();
        $temp->updateNotification($_POST['id']);
      break;
    case isset($_POST['company_business_links']) && token::val($_POST['tk'], 'company_business_links'):
        $temp= new COMPANY();
        $temp->business_links($_POST['links'], $_POST['ids']);
    break;
    case isset($_POST['COMPANYKEYWORDS']) && token::val($_POST['tk'], 'COMPANYKEYWORDSYASC'):
        $temp= new COMPANY();
        $temp->keywords($_POST['keywords']);
    break;
    case isset($_POST['COMPANYSTATEMENTS']) && token::val($_POST['tk'], 'COMPANYSTATEMENTSYASC'):
        $temp= new COMPANY();
        $temp->addCompanyStatementsCompany($_POST['Introduction'], $_POST['Vision'], $_POST['Mission'], $_POST['Values'], $_POST['Goals_Objectives']);
    break;
    case isset($_POST['COMPANYPRODUCTS']) && token::val($_POST['tk'], 'COMPANYPRODUCTSYASC'):
        $temp= new COMPANY();
        $temp->products_services($_POST['productname'], $_POST['productdes'],$_POST['productprice'],$_FILES['productimg']['name'], $_FILES['productimg']['tmp_name'], $_FILES['productimg']['size'], $_FILES['productimg']['error'] );
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="LINKVISITS" && token::val($_POST['tk2'], 'LINK_VISITS_YASC'):
        $temp= new COMPANY();
        $temp->insert_Websiteviews($_POST['link'], $_POST['id']);
    break;
    //Update
    case isset($_POST['COMPANYADMINUPDATE']) && token::val($_POST['tk'], 'COMPANYADMINUPDATEYASC'):
        $temp= new COMPANY();
        $temp->adminUpdate($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Email'], $_POST['Race']);
    break;
    case isset($_POST['COMPANYREGISTERUPDATE']) && token::val($_POST['tk'], 'COMPANYREGISTERUPDATEYASC'):
        $temp= new COMPANY();
        $temp->registerUpdate($_POST['tradename'],$_POST['legalname'], $_POST['regnum'], $_POST['address'], $_POST['postal'], $_POST['city'], $_POST['province'], $_POST['contact'], $_POST['email'], $_POST['foo'], $_POST['industries'],$_POST['financial']);
    break;

    case isset($_POST['COMPANYSTATEMENTUPDATE']) && token::val($_POST['tk'], 'COMPANYSTATEMENTUPDATEYASC'):
        $temp= new COMPANY();
        $temp->COMPANYStatementUPDATE($_POST['Introduction'], $_POST['Vision'], $_POST['Mission'], $_POST['Values'], $_POST['Goals_Objectives']); 
    break;
 case isset($_POST['identifier']) && $_POST['identifier']=="ADMINSDISPLAY" && token::val($_POST['tk'], 'ADMINSDISPLAY'):
        $temp= new COMPANY();
        $temp->ADMINS_FILTERED($_POST['type'], $_POST['data']);
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="ADMINS" && token::val($_POST['tk'], 'ADMINSDISPLAY'):
        $temp= new COMPANY();
        $temp->displayAdmins();
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="COMPANYLINKSUPDATE" && token::val($_POST['tk'], 'COMPANYLINKSUPDATEYASC'):
        $temp= new COMPANY();
        
        $temp->linksUpdate($_POST['links'], $_POST['ids'], $_POST['linkIDS']);
    break;
    case isset($_POST['COMPANYKEYWORDSUPDATE']) && token::val($_POST['tk'], 'COMPANYKEYWORDSUPDATEYASC'):
        $temp= new COMPANY();
        $temp->keywordsUpdate($_POST['keywords'], $_POST['ids']);
    break;
    case isset($_POST['COMPANYLOGOUPDATE']) && token::val($_POST['tk'], 'COMPANYLOGOYASC'):
        $temp= new COMPANY();
        $temp->UploadProfilePic($_FILES['logo']['name'], $_FILES['logo']['tmp_name'], $_FILES['logo']['size'], $_FILES['logo']['error']);
    break;
case isset($_POST['COMPANYPRODUCTUPDATE']) && token::val($_POST['tk'], 'COMPANYPRODUCTUPDATEYASC'):
        $temp= new COMPANY();
        $temp->products_update($_POST['productname'], $_POST['productdes'], $_POST['productprice'],$_POST['productIDS']);
    break;
    //delete
    case isset($_POST['COMPANYADMINDELETE']) && token::val($_POST['tk'], 'COMPANYADMINDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteAdmin();
    break;
    case isset($_POST['COMPANYLINKSDELETE']) && token::val($_POST['tk'], 'COMPANYLINKSDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteLinks();
    break;
    case isset($_POST['COMPANYSTATEMENTDELETE']) && token::val($_POST['tk'], 'COMPANYSTATEMENTDELETEYASC'):
        $temp= new COMPANY();
        $temp->companyDeleteState();
    break;
    case isset($_POST['COMPANYKEYWORDSDELETE']) && token::val($_POST['tk'], 'COMPANYKEYWORDSDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteKeywords();
    break;
    case isset($_POST['COMPANYREGISTERDELETE']) && token::val($_POST['tk'], 'COMPANYREGISTERDELETEYASC'):
        $temp= new COMPANY();
        $temp->DeleteRegister();
    break;
    
    case isset($_POST['COMPANYPRODUCTUPDATE']) && token::val($_POST['tk'], 'COMPANYPRODUCTUPDATE'):
        $temp= new COMPANY();
        $temp->com_products_update($_POST['productname'], $_POST['productdes'], $_POST['productprice'],$_POST['productIDS']); 
    break;
    
    case isset($_POST['COMDELETEPRODUCT']) && token::val($_POST['tk'], 'COMDELETEYASC'):
        $temp= new COMPANY();
        $temp->deletecomproduct($_GET['id']);
    break;






   

    //consultant
    case isset($_POST['CONSULTANTSIGNUP']) && token::val_unauth($_POST['tk'], 'CONSULTANTSIGNUPYASC'):
        $temp= new CONSULTANT();
        $temp->SignUp($_POST['Name'], $_POST['Surname'], $_POST['Username'], $_POST['email'], $_POST['pwd'], $_POST['pwd-repeat'], 1, $_GET['r']);
    break;
    case isset($_POST['identifier']) && $_POST['identifier']=="EMAIL_VERIFICATION_CONSULTANT" && token::val_unauth($_POST['tk'], 'EMAIL_VERIFICATION_YASC'):
        $temp= new CONSULTANT();
        $temp->verify_account($_POST['email'], $_POST['link']);
    break;
    case isset($_POST['CONSULTANTLOGIN']) && token::val_unauth($_POST['tk'], 'CONSULTANTLOGINYASC'):
        $temp= new CONSULTANT();
        $temp->login($_POST['Username'], $_POST['pwd'], $_GET['r']);
    break;
    case isset($_POST['CONSULTANTREGISTER']) && token::val($_POST['tk'], 'CONSULTANTREGISTER'):
        $temp= new CONSULTANT();
        $temp->consultant_register($_POST["Race"], $_POST["idtype"], $_POST["IDNumber"], $_POST["Gender"]);
    break;
    case isset($_POST['ConsUploadProfilePic']) && token::val($_POST['tk'], 'ConsUploadProfilePic'):
        $temp= new CONSULTANT();
        $temp->UploadProfilePic($_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error']);
    break;
    case isset($_POST['CONSULTANTREGISTER']) && token::val($_POST['tk'], 'CONSULTANTREGISTER'):
        $temp= new CONSULTANT();
        $temp->consultant_register($_POST["Race"], $_POST["idtype"], $_POST["IDNumber"], $_POST["Gender"]);
    break;
    case isset($_POST['CONSULTANTLOGOUPDATE']) && token::val($_POST['tk'], 'CONSULTANTLOGOYASC'):
        $temp= new CONSULTANT();
        $temp->UploadProfilePic($_FILES['logo']['name'], $_FILES['logo']['tmp_name'], $_FILES['logo']['size'], $_FILES['logo']['error']);
    break;
    case isset($_POST['CONSULTANTPASSWORDUPDATE']) && token::val($_POST['tk'], 'CONSULTANTPASSWORDUPDATEYASC'):
        $temp= new CONSULTANT();
        $temp->updatepwd($_POST['old_pwd'], $_POST['new_pwd'], $_POST['pwd_repeat']);
    break;

    case isset($_POST['CONSULTANTADMINUPDATE']) && token::val($_POST['tk'], 'CONSULTANTADMINUPDATEYASC'):
        $temp= new CONSULTANT();
        $temp->ConsultantadminUpdate($_POST['Name'], $_POST['Surname'], $_POST['IDType'], $_POST['IDNumber'], $_POST['Gender'], $_POST['Email'], $_POST['Race']);
    break;

    case isset($_POST['CONSULTANTADMINDELETE']) && token::val($_POST['tk'], 'CONSULTANTADMINDELETEYASC'):
        $temp= new CONSULTANT();
        $temp->DeleteAdmin();
    break;



    //Master admin
    // case isset($_POST['ADMINLOGIN']) && token::val_unauth($_POST['tk'], 'ADMINLOGINYASC'):
    //     $temp= new MAdmin();
    //     $temp->login($_POST['Username'], $_POST['pwd'], $_GET['r']);
    // break;
   
    //General admin
    // case isset($_POST['ADMINLOGIN']) && token::val_unauth($_POST['tk'], 'ADMINLOGINYASC'):
    //     $temp= new GAdmin();
    //     $temp->login($_POST['Username'], $_POST['pwd'], $_GET['r']);
    // break;




    //send user back for illegitamate entrance of php file
    // default:header("location: ../home.php");
    default: echo "val is    ->".token::val($_POST['tk'], 'SMMEADMIN');
    echo "<br> Token get is  ->";
    token::get("SMMEADMIN");
    echo "<br> Token POST is ->".$_POST['tk'];
    echo "<br> Token key is  ->".$_SESSION['tokeny'];
    exit();

}