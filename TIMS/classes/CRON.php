<?php
 
    use PHPMailer\PHPMailer\Exception;

  require_once('mail.extend.php');
  require 'mail_body.class.php';
  
  $filepath = realpath(dirname(__FILE__));
  include_once($filepath.'/../lib/Session.php');
  include_once($filepath.'/../lib/master.php');
  include_once($filepath.'/../helpers/val.php');
  include_once($filepath.'/../config/sql.notify.config.php');
  include_once($filepath.'/../config/sql.notification.php');
  include_once('notification_body.class.php');

class CRON{

    protected $master;
  
        function __construct(){
        
            $this->master=new Master("openlink_association_db");
    
        }
        
        public function test(){
            
            $mail = new Mailer(true);
            $body = new mail_body;
            $mail->send_single("kgnokanda@gmail.com", "Phillip Sibs", $body->subject(10, "Phillip"), $body->verifyaccount("..", "Phillip", "Sibanda", "SMME","hie bro rather this"));
            
        }
  public  function five_Day_wait_admin(){#1
    // select to get the people we need to send messages to
    // call further communication notification for the people in the result set
    // send select result as argument
  
    $sql ="SELECT sr.Legal_name as SL, sr.Email as SE, cr.Legal_name as CL, cr.Email CE, ea.SMME_ID as SMME_ID, ea.COMPANY_ID as COMPANY_ID,
    ss.typeOfEntity SW, cs.typeOfEntity CW
        FROM openlink_association_db.smme_company_events ea, openlink_smmes.register sr, openlink_companies.register cr, openlink_smmes.signup ss, openlink_companies.signup cs
        WHERE ea.SMME_ID=sr.SMME_ID
        AND ea.COMPANY_ID=cr.COMPANY_ID
        AND ss.SMME_ID=sr.SMME_ID
        AND cs.COMPANY_ID=cr.COMPANY_ID
        AND TIMESTAMPDIFF(DAY, event_Start, CURRENT_TIMESTAMP)>=5
        AND event_Completed=0
        AND ea.EVENT_ID=17";
    $query = $this->master->select_multiple_async($sql, "openlink_association_db");
    if(!$query){
      
      exit();
    }else{
      
      $result=$this->master->getResult();
      // print_r($result);
      // exit();
      $body = new mail_body;
  
      //send mail 
    $mail = new Mailer(true);
    $emails = array();
    $names = array();
    $subjects = array();
    $bodys = array();
      for($i=0; $i<=count($result)-1; $i++){
        array_push($emails, $result[$i]["SE"]);
        array_push($names, $result[$i]["SL"]);
        array_push($subjects,"Further Communication");
        $n_id = $this->chams(22,$result[$i]["COMPANY_ID"],$result[$i]["SMME_ID"],"COMPANY", "SMME");
        array_push($bodys,$body->email_contents(22, $result[$i]["SL"], $result[$i]["CL"], $n_id, "SMME", [], [], []));
      }
      $email = $mail->send_multiple($emails, $names,$subjects,$bodys);
      exit();
    }
  }

  public  function meeting_occured(){#1
    // select to get the people we need to send messages to
    // call further communication notification for the people in the result set
    // send select result as argument
  
    $sql ="SELECT sr.Legal_name as SL, sr.Email as SE, cr.Legal_name as CL, cr.Email CE, ea.SMME_ID as SMME_ID, ea.COMPANY_ID as COMPANY_ID,
    ss.typeOfEntity SW, cs.typeOfEntity CW
        FROM openlink_association_db.smme_company_events ea, openlink_smmes.register sr, openlink_companies.register cr, openlink_smmes.signup ss, openlink_companies.signup cs
        WHERE ea.SMME_ID=sr.SMME_ID
        AND ea.COMPANY_ID=cr.COMPANY_ID
        AND ss.SMME_ID=sr.SMME_ID
        AND cs.COMPANY_ID=cr.COMPANY_ID
        AND TIMESTAMPDIFF(DAY, event_date, CURRENT_TIMESTAMP)>=1
        AND event_Completed=0
        AND ea.EVENT_ID=32";
    $query = $this->master->select_multiple_async($sql, "openlink_association_db");
    if(!$query){
      
      exit();
    }else{
      
      $result=$this->master->getResult();
      // print_r($result);
      // exit();
      $body = new mail_body;
  
      //send mail 
    $mail = new Mailer(true);
    $emails = array();
    $names = array();
    $subjects = array();
    $bodys = array();
      for($i=0; $i<=count($result)-1; $i++){
        array_push($emails, $result[$i]["SE"]);
        array_push($names, $result[$i]["SL"]);
        array_push($subjects,"Further Communication");
        $n_id = $this->chams(35,$result[$i]["COMPANY_ID"],$result[$i]["SMME_ID"],"COMPANY", "SMME");
        array_push($bodys,$body->email_contents(35, $result[$i]["SL"], $result[$i]["CL"], $n_id, "SMME", [], [], []));
      }
      $email = $mail->send_multiple($emails, $names,$subjects,$bodys);
      exit();
    }
  }

  
   function update_event_completed($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $zero_or_one){
    //select
    $params=array($SMME_NPO_ID,$COMPANY_ID,$EVENT_ID );
  $sql = "SELECT event_iteration, event_Completed
  FROM openlink_association_db.smme_company_events
  WHERE SMME_ID=?
  AND COMPANY_ID=?
  AND EVENT_ID=?;
  ";
    $query=$$this->master->select('smme_company_events', $sql, "iii", $params);
    if(!$query){
      echo "naughty naughty";
      exit();
    }else{
      $result1=$this->master->getResult();
      $xi=$this->master->numRows();
      //if present update
      if($zero_or_one==0) $yo=1;
      else $yo=0;
      if($xi!==0 && $result1["event_Completed"]==$yo){
        // if it has happened before then update
        
        $params=array($zero_or_one,$SMME_NPO_ID, $COMPANY_ID, $EVENT_ID );
       $sql2 = "UPDATE openlink_association_db.smme_company_events
       SET 
       event_Start=CURRENT_TIMESTAMP(),
       event_Completed=?
       WHERE SMME_ID=?
       AND COMPANY_ID=?
       AND EVENT_ID=?;";
        $query=$this->master->update('smme_company_events', $sql2, "iiii", $params);
        if(!$query){
          echo "something weird happened";
          exit();
        }
      }
        
      }
}


 function chams($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO){
    $params=array($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO);
    $query=$this->master->Insert('notifications', NOTIFICATION_INSERT[0], NOTIFICATION_INSERT[1], $params);
    if(!$query){
      header('"location: ../this bastard 3"');
      exit();
    }
    return $this->master->getResult();
  }
}