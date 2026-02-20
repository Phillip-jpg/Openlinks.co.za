<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/SMME.notify.class.php');
include_once($filepath.'/../classes/NPO.notify.class.php');
include_once($filepath.'/../classes/COMPANY.notify.class.php');
include_once($filepath.'/../classes/ADMIN.notify.class.php');
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../helpers/token.php');


switch (true) {

    //smme
    case isset($_POST['SMME_request_notification']) && token::val($_POST['tk'], 'SMME_request_notification_YASC'):
      
        $temp= new SMME_notify(token::decode($_GET['id']));
        $id = token::decode($_GET['id']);
        $temp->send_request_notification($id);
    break;
    case isset($_POST['smme_requestAccept']) && token::val($_POST['tk'], 'smme_request_YASC'):
      $id = token::decode($_GET['id']);
        $temp = new SMME_notify($id);
        $temp->send_request_form($id, 1, $_POST['notify_id']);
        break;
    case isset($_POST['smme_requestReject']) && token::val($_POST['tk'], 'smme_request_YASC'):
        $temp = new SMME_notify(token::decode($_GET['id']));
        $temp->send_request_form(token::decode($_GET['id']), 0, $_POST['notify_id']);
      break;

      case isset($_POST['smme_readAccept']) && token::val($_POST['tk'], 'smme_read_YASC'):
        
        $id = token::decode($_GET['id']);
          $temp = new SMME_notify($id);
          $temp->read_form($id, 1, $_POST['notify_id']);
          break;
          case isset($_POST['company_readAccept'])  && token::val($_POST['tk'], 'company_read_YASC'):
            $id = token::decode($_GET['id']);
            $temp = new COMPANY_notify($id);
            $temp ->read_form($id, 1, $_POST['notify_id']);

          case isset($_POST['company_readReject'])  && token::val($_POST['tk'], 'company_read_YASC'):
            $id = token::decode($_GET['id']);
            
              $temp = new COMPANY_notify($id);
              $temp ->read_form($id, 0, $_POST['notify_id']);
            
      case isset($_POST['smme_readReject']) && token::val($_POST['tk'], 'smme_read_YASC'):
          $temp = new SMME_notify(token::decode($_GET['id']));
          $temp->read_form(token::decode($_GET['id']), 0, $_POST['notify_id']);
        break;
        case isset($_POST['smme_setDate']) && token::val($_POST['tk'], 'smme_setDate_YASC'):
          $id = token::decode($_GET['id']);
            $temp = new COMPANY_notify($id);
            $temp->Set_a_date_form($id, 1, $_POST['notify_id'], $_POST['date']);
            break;
        case isset($_POST['smme_setDateReject']) && token::val($_POST['tk'], 'smme_setDate_YASC'):
            $id = token::decode($_GET['id']);
            $temp = new COMPANY_notify(token::decode($_GET['id']));
            $temp->Set_a_date_form($id, 0, $_POST['notify_id']);
          break;
          case isset($_POST['connectFurther']) && token::val($_POST['tk'], 'connectFurther_YASC'):
            $id = token::decode($_GET['id']);
              $temp = new COMPANY_notify($id);
              $temp->Wish_to_connect_form($id, 1, $_POST['notify_id']);
              break;
          case isset($_POST['notConnectFurther']) && token::val($_POST['tk'], 'connectFurther_YASC'):
              $temp = new COMPANY_notify(token::decode($_GET['id']));
              $temp->Wish_to_connect_form(token::decode($_GET['id']), 0, $_POST['notify_id']);
            break;
            case isset($_POST['finalized']) && token::val($_POST['tk'], 'finalized_YASC')://
              $id = token::decode($_GET['id']);
                $temp = new COMPANY_notify($id);
                $temp->is_finalized_form($id, 1, $_POST['notify_id']);
                break;
            case isset($_POST['Notfinalized']) && token::val($_POST['tk'], 'finalized_YASC')://
                $temp = new COMPANY_notify(token::decode($_GET['id']));
                $temp->is_finalized_form(token::decode($_GET['id']), 2, $_POST['notify_id']);
              break;
              case isset($_POST['finalizedENDprocess']) && token::val($_POST['tk'], 'finalized_YASC')://end process
                $temp = new COMPANY_notify(token::decode($_GET['id']));
                $temp->is_finalized_form(token::decode($_GET['id']), 0, $_POST['notify_id']);
              break;

          
            








            case isset($_POST['meeting_happened']) && token::val($_POST['tk'], 'meeting_happened_YASC'):
              $temp = new SMME_notify(token::decode($_GET['id']));
              $temp->has_meeting_happened_form(1, token::decode($_GET['id']));
            break;
            case isset($_POST['meeting_not_happened']) && token::val($_POST['tk'], 'meeting_happened_YASC'):
              $temp = new SMME_notify(token::decode($_GET['id']));
              $temp->has_meeting_happened_form(0, token::decode($_GET['id']));
            break;












        //smme
        case isset($_POST['smme_receivedCommunication']) && token::val($_POST['tk'], 'smme_receivedCommunication_YASC'):
          $id = token::decode($_GET['id']);
            $temp = new SMME_notify($id);
            $temp->Further_communication_form($id, 1);
            break;
        case isset($_POST['smme_notReceivedCommunication']) && token::val($_POST['tk'], 'smme_receivedCommunication_YASC'):
            $temp = new SMME_notify(token::decode($_GET['id']));
            $temp->Further_communication_form(token::decode($_GET['id']), 0);
          break;


    //company

    case isset($_POST['company_requestAccept']) && token::val($_POST['tk'], 'company_request_YASC'):
      $id = token::decode($_GET['id']);
      $temp = new COMPANY_notify($id);
      $temp ->send_request_form($id, 1, $_POST['notify_id']);
  case isset($_POST['company_requestReject'])  && token::val($_POST['tk'], 'company_request_YASC'):
    $id = token::decode($_GET['id']);
      $temp = new COMPANY_notify($id);
      $temp ->send_request_form($id, 0, $_POST['notify_id']);

    case isset($_POST['COMPANY_request_notification']) && token::val($_POST['tk'], 'COMPANY_request_notification_YASC'):
      $temp= new COMPANY_notify(token::decode($_GET['id']));
      $id = token::decode($_GET['id']);
      $temp->send_request_notification($id);
    break;
    case isset($_POST['COMPANY_request_form']) && token::val($_POST['tk'], 'COMPANY_request_form_YASC'):
        $temp= new COMPANY_notify("COMPANY");
        $temp->send_request_form($COMPANY_COMPANY_ID, $COMPANY_ID, $EVENT_ID, $progress);
    break;
    case isset($_POST['company_requestAccept']):
        $temp = new COMPANY_notify("COMPANY");
          $id = token::decode($_GET['id']);
        $temp ->send_request_form($id, 1);
    case isset($_POST['company_requestReject']):
        $temp = new COMPANY_notify("COMPANY");
          $id = token::decode($_GET['id']);
        $temp ->send_request_form($id, 0);
    case isset($_POST['COMPANY_get_read']) && token::val($_POST['tk'], 'COMPANY_get_read_YASC'):
        $temp= new COMPANY_notify("COMPANY");
        $temp->get_read_notification($COMPANY_COMPANY_ID, $COMPANY_ID, $EVENT_ID);
    break;
    case isset($_POST['COMPANY_read_form']) && token::val($_POST['tk'], 'COMPANY_read_form_YASC'):
        $temp= new COMPANY_notify("COMPANY");
        $temp->read_form($COMPANY_COMPANY_ID, $COMPANY_ID, $EVENT_ID, $button_rejected_clicked);
    break;
    // case isset($_POST['COMPANY_Wish_to_connect_form']):
    //     $temp= new COMPANY_notify("COMPANY");
    //     $temp->Wish_to_connect_form($state);
    // break;
    // case isset($_POST['company_approval_yes']):
    //     $temp = new COMPANY_notify("COMPANY");
      //     $id = token::decode($_GET['id']);
    //     $temp ->Wish_to_connect_form($id, 1 );
    // case isset($_POST['company_approval_no']):
    //     $temp = new COMPANY_notify("COMPANY");
    //     $id = token::decode($_GET['id']);
    //     $temp ->Wish_to_connect_form($id, 0);
    case isset($_POST['COMPANY_Set_a_date_form']) && token::val($_POST['tk'], 'COMPANY_Set_a_date_form_YASC'):
        $temp= new COMPANY_notify("COMPANY");
        $temp->Set_a_date_form($id, $reject, $date);
    break;
    // case isset($_POST['approveDate']):
    //     $temp = new COMPANY_notify("COMPANY");
    //     $temp ->Set_a_date_form($id, 1, $_POST['date'] );
    //     $id = token::decode($_GET['id']);
    // case isset($_POST['endProcess']):
    //     $temp = new COMPANY_notify("COMPANY");
    //     $id = token::decode($_GET['id']);
    //     $temp ->Set_a_date_form($id, 0, $_POST['date']);
     // case isset($_POST['meeting_finalized_yes']):
    //     $temp = new COMPANY_notify("COMPANY");
    //     $id = token::decode($_GET['id']);
    //     $temp ->is_finalized_form($id, 1);
    // case isset($_POST['meeting_finalized_no']):
    //     $temp = new COMPANY_notify("COMPANY");
    //     $id = token::decode($_GET['id']);
    //     $temp ->is_finalized_form($id, 0, 10);
    // case isset($_POST['endProcess']):
    //     $temp = new COMPANY_notify("COMPANY");
    //     $id = token::decode($_GET['id']);
    //     $temp ->is_finalized_form($id, 0);
    





    //Admin

    case isset($_POST['ADMIN_five_Day_wait']) && token::val($_POST['tk'], 'ADMIN_five_Day_wait_YASC'):
      $id = $_POST['id'];
        $temp= new ADMIN_notify($id);
        $temp->five_Day_wait_admin();
    break;
    case isset($_POST['ADMIN_Set_a_date_five_day_wait']) && token::val($_POST['tk'], 'ADMIN_Set_a_date_five_day_wait_YASC'):
      $id = token::decode($_GET['id']);
        $temp= new ADMIN_notify($id);
        $temp->Set_a_date_five_day_wait_admin($id);
    break;
    case isset($_POST['ADMIN_after_set_date']) && token::val($_POST['tk'], 'ADMIN_after_set_date_YASC'):
      $id = token::decode($_GET['id']);
        $temp= new ADMIN_notify($id);
        $temp->after_set_date_admin($id);
        break;

        default:
        echo $_POST['tk'];
        echo "<br>";
        echo "=";
        echo "<br>";
        echo token::get("SMME_request_notification_YASC");
}
