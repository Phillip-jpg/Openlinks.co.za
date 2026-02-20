<?php
use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';
require 'mail_body.class.php';

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../config/sql.notify.config.php');
include_once($filepath.'/../config/sql.notification.php');
include_once('notification_body.class.php');

class CRON {
  protected $master;
  protected $test = 0;
  function __construct(){
      $this->master=new Master(DB_NAME_5);
    }
    public function cron(){
        $fdw = $this->five_Day_wait_admin();
        $dns = $this->date_not_set_admin();
        $asd = $this->after_set_date_admin();
        if(!empty($fdw))$this->mail_multiple($fdw, 28);
        else echo "naughty1<br>";
        if(!empty($dns))$this->mail_multiple($dns, 33);
        else echo "naughty2<br>";
        if(!empty($asd))$this->mail_multiple($asd, 34);
        else echo "naughty3<br>";
    }

  private function five_Day_wait_admin(){#1
  // select to get the people we need to send messages to
  // call further communication notification for the people in the result set
  // send select result as argument
  $EVENT_ID=28;
  $sql = five_Day_wait_admin_SELECT[0];
  $types =five_Day_wait_admin_SELECT[1];
  $params = array(5, $EVENT_ID);//specify the event id
  $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, $params);
  if(!$query){
    header("location: ../");
    exit();
  }else{
    return $result=$this->master->getResult();
  }
}

private function date_not_set_admin(){#1
    // select to get the people we need to send messages to
    // call further communication notification for the people in the result set
    // send select result as argument
    $EVENT_ID=33;
    $sql = five_Day_wait_admin_SELECT[0];
    $types =five_Day_wait_admin_SELECT[1];
    $params = array(5, $EVENT_ID);//specify the event id
    $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, $params);
    if(!$query){
      header("location: ../");
      exit();
    }else{
      return $result=$this->master->getResult();
    }
  }

  private function after_set_date_admin(){#1
    // select to get the people we need to send messages to
    // call further communication notification for the people in the result set
    // send select result as argument
    $EVENT_ID=32;
    $sql = after_set_date_admin_SELECT[0];
    $types =after_set_date_admin_SELECT[1];
    $params = array($EVENT_ID);//specify the event id
    $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, $params);
    if(!$query){
      header("location: ../");
      exit();
    }else{
      return $result=$this->master->getResult();
    }
  }






  private function handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $progress){
    $this->test++;
    //inserts an event if it hasnt happened before or updates it if it has happened before
    
    //select iteration
    $params=array($EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
    $query=$this->master->select('smme_company_events', SMME_ITERATION_SELECT[0], SMME_ITERATION_SELECT[1], $params);

    if(!$query){
      echo "gentlemen1";
      exit();
    }else{
      $result1=$this->master->getResult();
      $xi=$this->master->numRows();
      //if present update
      if($xi!==0){
        echo "Has happened ".$this->test;
        // if it has happened before then update
        $increment=$result1["event_iteration"]+1;
        $params=array($increment, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
        $query=$this->master->update('smme_company_events', SMME_EVENT_UPDATE[0], SMME_EVENT_UPDATE[1], $params);
        if(!$query){
          header('"location: ../"');
          exit();
        }
        }else{
          echo "Has not happened ".$this->test;
          //else insert
          $params=array($progress, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
          $query=$this->master->Insert('smme_company_events', SMME_EVENT_INSERT[0], SMME_EVENT_INSERT[1], $params);
          if(!$query){
            header('"location: ../"');
            exit();
          }
        }
    }
    
    
  }






  function mail_multiple($result, $EVENT_ID){
    if($EVENT_ID == 28){
        $to_name = "SL";
        $to_email = "SE";
        $to_id = "SMME_ID";
        $to_who = "SW";
        $from_name = "CL";
        $from_id = "COMPANY_ID";
        $from_who = "CW";
        $next_event = 22;

    }elseif($EVENT_ID == 33){
        $to_name = "CL";
        $to_email = "CE";
        $to_id = "COMPANY_ID";
        $to_who = "CW";
        $from_name = "SL";
        $from_id = "SMME_ID";
        $from_who = "SW";
        $next_event = 30;
  }elseif($EVENT_ID == 34){
    $to_name = "SL";
    $to_email = "SE";
    $to_id = "SMME_ID";
    $to_who = "SW";
    $from_name = "CL";
    $from_id = "COMPANY_ID";
    $from_who = "CW";
    $next_event = 35;
  }else{
      echo "FLOP";
      exit();
  }

    $body = new mail_body;
    $temp = array();
    $msgHTML = array();
    $msgNAMES = array();
    $msgSUBJECT = array();
    foreach ($result as $row){
        $this->handle_reccuring_event($row["SMME_ID"], $row["COMPANY_ID"], $next_event, 3);
        array_push($temp, $row[$to_email]);
        $n_id = $this->chams($next_event, $row[$from_id], $row[$to_id]);
        if($EVENT_ID == 34)
        {
          array_push($msgHTML, $body->email_contents($next_event, $row[$from_name], $row[$to_name], $n_id, $row[$to_who], $row["event_date"]));
        }
        else 
        {
          array_push($msgHTML, $body->email_contents($next_event, $row[$from_name], $row[$to_name], $n_id, $row[$to_who]));
        }
        array_push($msgNAMES, $row[$to_name]);
        array_push($msgSUBJECT, $body->subject($next_event, $row[$from_name]));
        //$this->update_event_completed($row["SMME_ID"], $row["COMPANY_ID"], $next_event, 1);
    }

    try {
        //Instantiate your new class, making use of the new `$body` parameter 
        $mail = new Mailer(true);
        if($mail->send_multiple($temp, $msgNAMES, $msgSUBJECT, $msgHTML))
        return true;
        else return false;
    } catch (Exception $e) {
        //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
        echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
    }
  }



  private function update_event_completed($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, int $zero_or_one){
        //select
        $params=array($EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
        $query=$this->master->select('smme_company_events', SMME_ITERATION_SELECT[0], SMME_ITERATION_SELECT[1], $params);
        if(!$query){
          header('"location: ../"');
          exit();
        }else{
          $result1=$this->master->getResult();
          $xi=$this->master->numRows();
          //if present update
          if($zero_or_one==0){
            $yo=1;
          }else $yo=0;
          if($xi!==0 && $result1["event_Completed"]==$yo){
            // if it has happened before then update
            $params=array($zero_or_one, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
            $query=$this->master->update('smme_company_events', SMME_EVENT_COMPLETED_UPDATE[0], SMME_EVENT_COMPLETED_UPDATE[1], $params);
            if(!$query){
              echo "something weird happened";
              exit();
            }
            }
          }
  }


  private function chams($EVENT_ID, $FROM, $TO){
    $params=array($EVENT_ID, $FROM, $TO);
    $query=$this->master->Insert('notifications', NOTIFICATION_INSERT[0], NOTIFICATION_INSERT[1], $params);
    if(!$query){
      header('"location: ../this bastard 3"');
      exit();
    }
    return $this->master->getResult();
  }     
}