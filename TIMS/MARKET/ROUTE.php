<?php
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.class.php');
include_once($filepath.'/../classes/NPO.class.php');
include_once($filepath.'/../classes/COMPANY.class.php');
include_once($filepath.'/../classes/CONSULTANT.class.php');
include_once($filepath.'/../classes/ADMIN.class.php');
include_once($filepath.'/../helpers/token.php');
include_once('USER.php');

switch (true) {

    //create scorecard
    case isset($_POST['SMME_SCORECARD_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'SCORECARD_CREATION_OPENLINKS'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createScoreCard($_POST['Title'],$_POST['Other'],$_POST['Date']);
    break;
    case isset($_POST['COMPANY_SCORECARD_CREATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'SCORECARD_CREATION_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createScoreCard($_POST['Title'],$_POST['Other'],$_POST['Date']);
    break;
    case isset($_POST['ADMIN_SCORECARD_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'SCORECARD_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createScoreCard($_POST['Title'],$_POST['Other'],$_POST['Date']);
    break;

    //create criteria
    case isset($_POST['SMME_CRITERIA_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CRITERIA_CREATION_OPENLINKS'):
        
         $temp = new SMME();
         $id = session::get($temp->id);
         $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createCriteria($_POST['Name'],$_POST['Description'],$_POST['Documents'], $_GET['dest']);
    break;
    case isset($_POST['COMPANY_CRITERIA_CREATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'CRITERIA_CREATION_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createCriteria($_POST['Name'],$_POST['Description'],$_POST['Documents'], $_GET['dest']);
    break;

    case isset($_POST['ADMIN_CRITERIA_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CRITERIA_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createCriteria($_POST['Name'],$_POST['Description'],$_POST['Documents'], $_GET['dest']);
    break;

    //create questions wizard
    case isset($_POST['SMME_QUESTION_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_CREATION_OPENLINKS'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createQuestionWizard($_POST['questions'],$_POST['weights']);
    break;
    case isset($_POST['COMPANY_QUESTION_CREATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'QUESTION_CREATION_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createQuestionWizard($_POST['questions'],$_POST['weights']);
    break;

    case isset($_POST['ADMIN_QUESTION_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createQuestionWizard($_POST['questions'],$_POST['weights']);
    break;

    //create questions edit
    case isset($_POST['SMME_QUESTION_CREATE_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_CREATION_OPENLINKS'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createQuestionEdit($_POST['questions'],$_POST['weights'],$_POST['criteria_id']);
    break;
    case isset($_POST['COMPANY_QUESTION_CREATE_EDIT']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'QUESTION_CREATION_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createQuestionEdit($_POST['questions'],$_POST['weights'],$_POST['criteria_id']);
    break;

    case isset($_POST['ADMIN_QUESTION_CREATE_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createQuestionEdit($_POST['questions'],$_POST['weights'],$_POST['criteria_id']);
    break;
    //create options
    case isset($_POST['SMME_OPTION_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'OPTION_CREATION_OPENLINKS'):
        
  
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createOptions($_POST['choiceText'],$_POST['choiceWeight'],$_POST['question_id'], $_GET['d'],$_POST["scorecard_id"]);
    break;
    case isset($_POST['COMPANY_OPTION_CREATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'OPTION_CREATION_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createOptions($_POST['choiceText'],$_POST['choiceWeight'],$_POST['question_id'], $_GET['d'],$_POST["scorecard_id"]);
    break;

    case isset($_POST['ADMIN_OPTION_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'OPTION_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createOptions($_POST['choiceText'],$_POST['choiceWeight'],$_POST['question_id'], $_GET['d'],$_POST["scorecard_id"]);
    break;
    //create post
    case isset($_POST['SMME_POST_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'POST_CREATION_OPENLINKS'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createPost($_POST['office'],$_POST['title'],$id,$_POST['description'],$_POST['StartDate'],$_POST['EndDate'],$_POST['work_type'],$_POST['scorecard_id'],$_POST['jobOrderType'], $_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error'], "",$_POST['expenses']);
    break;
    // case isset($_POST['COMPANY_POST_CREATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'POST_CREATION_OPENLINKS'):
    //     $temp = new COMPANY();
    //     $id = session::get($temp->id);
    //     $type= $temp->classname; 
    //     $temp= new USER($id, $type);
    //     $temp->createPost($_POST['title'],$id,$_POST['Description'],$_POST['StartDate'],$_POST['EndDate'],$_POST['work_type'],$_POST['scorecard_id'],$_POST['jobOrderType'], $_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error']);
    // break;

    case isset($_POST['ADMIN_POST_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'POST_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createPost($_POST['office'],$_POST['title'],$_POST['client'],$_POST['Description'],$_POST['StartDate'],$_POST['EndDate'],$_POST['work_type'],$_POST['scorecard_id'],$_POST['jobOrderType'], $_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error'],$_POST['client_rep']);
    break;

    //create client
    case isset($_POST['ADMIN_CLIENT_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CLIENT_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createClient($_POST['Name'],$_POST['City'],$_POST['Province'], $_POST['offices'],$_POST['industries']);
    break;
 //create representative
 case isset($_POST['CREATEREPRESENTATIVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CREATEREP_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->createRep($_POST['Name'],$_POST['Surname'],$_POST['Email'], $_POST['contact'],$_POST['role'], $_POST['client_id']);
break;
    //verify job order
    case isset($_POST['ADMIN_VERIFY_JOBORDER']) && isset($_POST['tk']) && token::val($_POST['tk'], 'VERIFY_JOBORDER_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->verifyJobOrder($_POST['post_id'], $_POST['admin']);
    break;

//smme job order expenses
case isset($_POST['SMME_EXPENSE_JOBORDER']) && isset($_POST['tk']) && token::val($_POST['tk'], 'SMME_EXPENSE_JOBORDER_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveJobOrderExpenses($_POST['selected_expenses'],$_POST['post_id']);
break;

    //RESPONSE
    case isset($_POST['SMME_RESPONSE_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'RESPONSE_CREATION_OPENLINKS'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $responses = [];

        foreach ($_POST as $key => $value) {
            
            if (strpos($key, 'choice_') === 0) {
                
                // Extract question ID from the key
                $question_id = intval(substr($key, strlen('choice_')));
                $choice_id = intval($value);
                
                // Store the response in the responses array
                $responses[$question_id] = $choice_id;
            }
        }
         $temp->createResponse($_POST['question_id'],$responses, $_POST['SCORECARD_ID'], $_POST['POST_ID'], $_POST['company']);
    break;
    
     case isset($_POST['JOB_SMME_LINK']) && isset($_POST['tk']) && token::val($_POST['tk'], 'LINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
         $temp->createlink($_POST['smme_ids'], $_POST['post_ids']);
    break;
    
    case isset($_POST['COMPANY_RESPONSE_CREATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'RESPONSE_CREATION_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->createResponse($_POST['question_id'],$_POST['choice'], $_POST['SCORECARD_ID'], $_POST['POST_ID']);
    break;

    case isset($_POST['ADMIN_RESPONSE_CREATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'RESPONSE_CREATION_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        // Process each question and its selected choice
        $responses = [];

        foreach ($_POST as $key => $value) {
            
            if (strpos($key, 'choice_') === 0) {
                
                // Extract question ID from the key
                $question_id = intval(substr($key, strlen('choice_')));
                $choice_id = intval($value);

                // Store the response in the responses array
                $responses[$question_id] = $choice_id;
            }
        }
        
        // Handle or store the responses as needed
        // $responses will now contain the selected choices for each question
            // $count = count($_POST['question_id']);
            // $choices = array();
            // for($i = 0; $i < $count; $i++){
            //     if(isset( $_POST['choice'.($i+1).''])){
            //         $choice = $_POST['choice'.($i+1).''];
            //         array_push($choices, $choice);
            //     }
            // }
        $temp->createResponse($_POST['question_id'],$responses, $_POST['SCORECARD_ID'], $_POST['POST_ID'], $_POST['company']);
    break;

    //print pdfs
    case isset($_POST['SMME_PRINT_JOBORDER_INFO']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'PRINT_RESPONSES_INFO'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->PrintResponseInfo($_GET['p']);
    break;
    case isset($_POST['COMPANY_PRINT_JOBORDER_INFO']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'PRINT_RESPONSES_INFO'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->PrintResponseInfo($_GET['p']);
    break;
    case isset($_POST['ADMIN_PRINT_JOBORDER_INFO']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'PRINT_RESPONSES_INFO'):
        $temp = new ADMIN();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->PrintResponseInfo($_GET['p']);
    break;
    case isset($_POST['ADMIN_PRINT_INDJOBORDER_INFO']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'PRINT_RESPONSES_INFO'):
        $temp = new ADMIN();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->PrintIndResponseInfo($_GET['p'], $_GET['u']); 
    break;
    
    //RESPONSE
    case isset($_FILES) && isset($_GET['e']) && isset($_GET['p'])&& ($_GET['e']==1) && isset($_GET['c']):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->saveResponseFile($_GET['c'],$_GET['p'],$_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error']);
    break;
    //RESPONSE
    // case isset($_FILES) && isset($_GET['e']) && isset($_GET['p'])&& ($_GET['e']==3):
    //     $temp = new Admin();
    //     $id = session::get($temp->id);
    //     $type= $temp->classname; 
    //     $temp= new USER($id, $type);
    //     $temp->saveResponseFile($_GET['p'],$_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error']);
    // break;
    
    case isset($_FILES) && isset($_GET['e']) && isset($_GET['p']) && ($_GET['e'] == 3) && isset($_GET['c']):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type = $temp->classname; 
        $temp = new USER($id, $type);
        $temp->saveResponseFileADMIN($_GET['c'], $_GET['p'], $_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['size'], $_FILES['file']['error']);
        break;
    
    
    //edit scorecard
    case isset($_POST['SMME_SCORECARD_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'SCORECARD_EDIT_OPENLINKS'):
        $temp = new SMME();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->saveUpdate($_POST['Title'],$_POST['Other'],$_POST['Date'], $_GET['w'], $_POST['Criteria'], $_POST['weight']);
    break;
    case isset($_POST['COMPANY_SCORECARD_EDIT']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'SCORECARD_EDIT_OPENLINKS'):
        $temp = new COMPANY();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        $temp->saveUpdate($_POST['Title'],$_POST['Other'],$_POST['Date'], $_GET['w'], $_POST['Criteria'], $_POST['weight']);
    break;
    case isset($_POST['ADMIN_SCORECARD_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'SCORECARD_EDIT_OPENLINKS'):
        $temp = new Admin();
        $id = session::get($temp->id);
        $type= $temp->classname; 
        $temp= new USER($id, $type);
        if(isset($_POST['Criteria'])){
            $temp->saveUpdate($_POST['Title'],$_POST['Other'],$_POST['Date'], $_GET['w'], $_POST['Criteria'], $_POST['weight']);
        }else{
            $temp->saveUpdate($_POST['Title'],$_POST['Other'],$_POST['Date'], $_GET['w']);
        }
        
    break;
   

  //edit criteria
  case isset($_POST['SMME_CRITERIA_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CRITERIA_EDIT_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveCriteriaUpdate($_POST['Name'],$_POST['Description'],$_POST['Documents'], $_GET['w']);
    
break;
case isset($_POST['COMPANY_CRITERIA_EDIT']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'CRITERIA_EDIT_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveCriteriaUpdate($_POST['Name'],$_POST['Description'],$_POST['Documents'], $_GET['w']);
break;
case isset($_POST['ADMIN_CRITERIA_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CRITERIA_EDIT_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveCriteriaUpdate($_POST['Name'],$_POST['Description'],$_POST['Documents'], $_GET['w']);
break;
 //edit weights
 case isset($_POST['SMME_WEIGHTADJUST_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'WEIGHTADJUST_EDIT_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveWeightAdjust($_POST['weight'],$_POST['scorecard'],$_POST['criteria']);
    
break;
case isset($_POST['COMPANY_WEIGHTADJUST_EDIT']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'WEIGHTADJUST_EDIT_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveWeightAdjust($_POST['weight'],$_POST['scorecard'],$_POST['criteria']);
break;
case isset($_POST['ADMIN_WEIGHTADJUST_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'WEIGHTADJUST_EDIT_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveWeightAdjust($_POST['weight'],$_POST['scorecard'],$_POST['criteria']);
break;
//edit question
case isset($_POST['SMME_QUESTION_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_EDIT_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveQuestionUpdate($_POST['Question'],$_POST['Weight'], $_GET['w']);
    
break;
case isset($_POST['COMPANY_QUESTION_EDIT']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'QUESTION_EDIT_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveQuestionUpdate($_POST['Question'],$_POST['Weight'], $_GET['w']);
break;
case isset($_POST['ADMIN_QUESTION_EDIT']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_EDIT_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveQuestionUpdate($_POST['Question'],$_POST['Weight'], $_GET['w']);
break;

//update option
case isset($_POST['SMME_OPTION_UPDATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'OPTION_UPDATE_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveOptionUpdate($_POST['choiceText'],$_POST['choiceWeight'], $_GET['w'], $_GET['s'], $_GET['d']);
break;
case isset($_POST['COMPANY_OPTION_UPDATE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'OPTION_UPDATE_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveOptionUpdate($_POST['choiceText'],$_POST['choiceWeight'], $_GET['w'], $_GET['s'], $_GET['d']);
break;
case isset($_POST['ADMIN_OPTIONS_UPDATE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'OPTION_UPDATE_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->saveOptionUpdate($_POST['choiceText'],$_POST['choiceWeight'], $_GET['w'], $_GET['s'], $_GET['d']);
break;
//remove criteria
case isset($_POST['SMME_CRITERIA_REMOVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CRITERIA_REMOVE_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeCriteria($_GET['s'], $_GET['w']);
break;
case isset($_POST['COMPANY_CRITERIA_REMOVE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'CRITERIA_REMOVE_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeCriteria($_GET['s'], $_GET['w']);
break;
case isset($_POST['ADMIN_CRITERIA_REMOVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'CRITERIA_REMOVE_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeCriteria($_GET['s'], $_GET['w']);
break;
//remove question
case isset($_POST['SMME_QUESTION_REMOVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_REMOVE_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeQuestion($_GET['q'], $_GET['w']);
break;
case isset($_POST['COMPANY_QUESTION_REMOVE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'QUESTION_REMOVE_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeQuestion($_GET['q'], $_GET['w']);
break;
case isset($_POST['ADMIN_QUESTION_REMOVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'QUESTION_REMOVE_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeQuestion($_GET['q'], $_GET['w']);
break;
//remove option
case isset($_POST['SMME_OPTION_REMOVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'OPTION_REMOVE_OPENLINKS'):
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeOption($_GET['q'], $_GET['c'], $_GET['s']);
break;
case isset($_POST['COMPANY_OPTION_REMOVE']) && isset($_POST['tk']) &&token::val($_POST['tk'], 'OPTION_REMOVE_OPENLINKS'):
    $temp = new COMPANY();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeOption($_GET['q'], $_GET['c'], $_GET['s']);
break;
case isset($_POST['ADMIN_OPTION_REMOVE']) && isset($_POST['tk']) && token::val($_POST['tk'], 'OPTION_REMOVE_OPENLINKS'):
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->removeOption($_GET['q'], $_GET['c'], $_GET['s']);
break;
case isset($_POST['ACTION']) && isset($_POST['CLIENT_ID']) && $_POST['ACTION'] == "CLIENT_REP_LIST":
    $temp = new Admin();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->clientReps($_POST['CLIENT_ID']);
break;
case isset($_POST['ACTION']) && isset($_POST['OFFICE_ID']) && $_POST['ACTION'] == "INDUSTRY_LIST":
    $temp = new SMME();
    $id = session::get($temp->id);
    $type= $temp->classname; 
    $temp= new USER($id, $type);
    $temp->Indus($_POST['OFFICE_ID']);
break;
}
