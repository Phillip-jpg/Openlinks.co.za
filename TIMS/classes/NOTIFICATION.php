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

abstract class NOTIFICATION {
  protected $master;
  protected $whodis;
  function __construct($who){
    ($_SESSION["WHO"] == "P_COMPANY")? $this->id="P_COMPANY": $this->id = $this->id;
    $this->master=new Master($this->var4);
    $this->whodis=$this->who($who);//entity that is being communicated with
    
      if($this->whodis=="NPO"){
        $this->EMAIL_SELECT = NPO_EMAIL_SELECT;
        $this->ITERATION_SELECT = NPO_ITERATION_SELECT;
        $this->DATE_UPDATE = NPO_DATE_UPDATE;
        $this->DATE_INSERT = NPO_DATE_INSERT;
        $this->NONRECCURING_INSERT = NPO_NONRECCURING_INSERT;
        $this->EVENT_INSERT = NPO_EVENT_INSERT;
        $this->EVENT_UPDATE = NPO_EVENT_UPDATE;
        $this->PROGRESS_UPDATE = NPO_PROGRESS_UPDATE;
        //$this->PROGRESS_SELECT = NPO_PROGRESS_SELECT;
      }
    }
 
    public function Login($userName, $password){
      val::checkempty(array($userName,$password));
      $query=$this->master->select("signup", $this->LOGIN_SELECT[0], $this->LOGIN_SELECT[1], array($userName, $this->classname));
      if(!$query) {
        header("location: ../Home.php?error=databaseerror");
        exit();
      }
      $result=$this->master->getResult();
      $xi=$this->master->numRows();
      if($xi==0){
        header("location: ../home.php?error=InvalidUserNameOrPassword");
        exit();
      }
      $pwdcheck= password_verify($password, $result['Pwd']);
      if ($pwdcheck == false){
        header("location: ../home.php?error=InvalidUserNameOrPassword");
        exit();
      }
      elseif($pwdcheck == true) {
      Session::init();
      Session::set($this->id, $result[$this->id]);
      $array =$this->pimg($result[$this->id]);
      if($array=='error'){
        Session::set('Status',1);
        Session::set('profileerror', 'error');
        }
        else{
        Session::set('Status',$array['Status']);
        Session::set('Name',$result['First_Name']);
        if($array['ext']!==null && $_SESSION['Status']==0){
          Session::set('ext', $array['ext']);
        }
    }  
    header("location: ../".$this->classname."/notifications.php?login=success");
    exit();
  } else {
    header("location: ../Home.php?error");
    exit();
  }
  }

  public function pimg($id){
    $query=$this->master->select("pimg", $this->PIMG_SELECT[0], $this->PIMG_SELECT[1], array($id));
    if($query)
      return $this->master->getResult();
      return 'error';
  }
private function checkExists($SMME_NPO_ID, $COMPANY_ID){
  $query=$this->master->select("smme_company_events", $this->EVENT_SELECT[0], $this->EVENT_SELECT[1], array($SMME_NPO_ID, $COMPANY_ID, 1,$SMME_NPO_ID, $COMPANY_ID, 2));
  $result = $this->master->getResult();
  if(!empty($result)){
    if($result[1] == 1){
      return true;
    }else{
      return false;
    }
  }else{
    return false;
  }
  
}
  public function send_request_notification($id)#3
  {
    
    $EVENT_ID = $this->assign_event_id($this->classname, 1, 3, 2);
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    //check if event doesnt already exist in the database
    $test = $this->checkExists($SMME_NPO_ID, $COMPANY_ID);
    if($test){
      header("location: ../".$this->classname."/notifications.php?info=exists");
    exit();
    }else{
      $email = $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 1, $this->whodis);
      // if($EVENT_ID == 1 || $EVENT_ID==3){
      //   $this->chams($EVENT_ID,$SMME_NPO_ID, $COMPANY_ID, "SMME", "COMPANY");
      // }else{
      //   $this->chams($EVENT_ID,$COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
      // }
      if($email){
        header("location: ../".$this->classname."/index.php?info=success");
    exit();
      }else{
        header("location: ../".$this->classname."/index.php?info=failed");
    exit();
      }
    
    }
    
  }


  public function send_request_form($id, $state,$notify_id)#6
  {

      $EVENT_ID_REJECT = $this->assign_event_id($this->whodis, 7, 9, 8);
      $EVENT_ID_ACCEPT = $this->assign_event_id($this->whodis, 5, 6, 4);
      $progress=1;
      $ids=$this->correct_ids($id);
      $SMME_NPO_ID=$ids[0];
      $COMPANY_ID=$ids[1];
      //echo "SMME ID = $SMME_NPO_ID";
      if($state == 0){//if yes insert finalized//message smme
      //echo "<b>LOOK LOOOKKK  LOOOOOOKKKKKKKKK</b>";

      
        
        $email = $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_REJECT, $progress, $this->whodis);
        if(!$email){
          header("location: ../".$this->classname."/notifications.php?info=success");
          exit();
        }else{
          header("location: ../".$this->classname."/notifications.php?info=failed");
          exit();
        }
      
    }
    else{
      $this->insert_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_ACCEPT, 1);

      $this->update_progress($EVENT_ID_ACCEPT,2, $COMPANY_ID, $SMME_NPO_ID);

       $this->get_read_notification($id);
        $this->updateNotification($notify_id);
       header("location: ../".$this->classname."/notifications.php?info=success");
       exit();
  }
}

public function get_read_notification($id)#3
{
  $ids=$this->correct_ids($id);
  $SMME_NPO_ID=$ids[0];
  $COMPANY_ID=$ids[1];
  $EVENT_ID = $this->assign_event_id($this->whodis, 11, 12, 10);

  $sql = $this->READ_SELECT[0];
  $types =$this->READ_SELECT[1];
  $params = array();
  if($this->classname== "SMME" || $this->classname== "NPO"){
    array_push($params, $COMPANY_ID);
    $which = DB_NAME_3;
    $this->master->changedb($which);
  
    $query = $this->master->select("register", $sql, $types, $params);
    
    if(!$query){
      echo "flop1";
      exit();
    }
    $dogecoin=$this->master->getResult();
    $this->master->changedb(DB_NAME_5);
    $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID , 2, $this->whodis, $dogecoin);
   // $this->chams($EVENT_ID, $COMPANY_ID,$SMME_NPO_ID, "COMPANY", "SMME");
  }
  else{
    array_push($params, $SMME_NPO_ID);
    $which = DB_NAME_1;
    $this->master->changedb($which);
    $query = $this->master->select("register", $sql, $types, $params);
    if(!$query){
      echo "flop2";
      exit();
    }
    $dogecoin=$this->master->getResult();
    $params1 = array($SMME_NPO_ID);
    $query1 = $this->master->select_prepared_async(SMME_COMPANY_DIRECTOR_SELECT[0], DB_NAME_1, SMME_COMPANY_DIRECTOR_SELECT[1], $params1);
    if(!$query1){
      echo "flop3";
      exit();
    }
    $dogecoin2=$this->master->getResult();


    $this->master->changedb(DB_NAME_5);
    $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID , 2, $this->whodis, $dogecoin, $dogecoin2);
    //$this->chams($EVENT_ID, $SMME_NPO_ID, $COMPANY_ID , "SMME", "COMPANY");
  }
  

}


public function read_form($id, $state, $notify_id){#6
    $EVENT_ID_REJECT = $this->assign_event_id($this->classname, 15, 16, 18);
    $EVENT_ID_ACCEPT = $this->assign_event_id($this->classname, 13, 14, 17);
//if reject insert reject
$ids=$this->correct_ids($id);

$SMME_NPO_ID=$ids[0];
$COMPANY_ID=$ids[1];


  if($state == 1)//accept
  {
    $this->insert_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_ACCEPT, 3);

    $this->update_progress($EVENT_ID_ACCEPT, 3, $COMPANY_ID, $SMME_NPO_ID);
    $this->updateNotification($notify_id);
    if($this->classname== "SMME" || $this->classname== "NPO")
    {
      $this->Wish_to_connect_notification($COMPANY_ID);
      header("location: ../".$this->classname."/notifications.php?info=success");
          exit();
    }else {

      // $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, 28, 3);//for system wait 5 days
      $this->Further_communication_notification($SMME_NPO_ID);

      header("location: ../".$this->classname."/notifications.php?info=success");
          exit();
    }
  }
  else//reject
  {
    // if($this->classname== "SMME" || $this->classname== "NPO"){
    //   $this->chams($EVENT_ID_REJECT, $SMME_NPO_ID, $COMPANY_ID, "SMME", "COMPANY");
    // }else{
    //   $this->chams($EVENT_ID_REJECT, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
    // }
    $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_REJECT, 2, $this->whodis);
    $this->update_progress($EVENT_ID_REJECT, 5, $COMPANY_ID, $SMME_NPO_ID);
    $this->updateNotification($notify_id);
    header("location: ../".$this->classname."/notifications.php?info=success");
    exit();
  }
//call Wish to connect notification if company initiated
//by checking if this->classname is == smme(reading)
}

  public function five_Day_wait_admin(){#1
  // select to get the people we need to send messages to
  // call further communication notification for the people in the result set
  // send select result as argument

  $EVENT_ID=28;
  $sql = $this->FIVE_DAY_WAIT_ADMIN_SELECT[0];
  $types =$this->FIVE_DAY_WAIT_ADMIN_SELECT[1];
  $params = array(5, $EVENT_ID);//specify the event id
  $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, $params);
  if(!$query){
    header("location: ../");
    exit();
  }else{
    
    $result=$this->master->getResult();
    // print_r($result);
    // exit();
    for($i=0; $i<=count($result)-1; $i++){
      $SMME_ID = $result[$i]["SMME_ID"];
      $COMPANY_ID = $result[$i]["COMPANY_ID"];
      $this->Admin_Further_communication_notification($COMPANY_ID, $SMME_ID);
    }
    
  }
}
public function Admin_Further_communication_notification($company, $smme){#2
  $EVENT_ID = 22;
 $this->update_event_completed($smme, $company, 28, 1);
  $this->mail_reccuring_event($smme, $company, $EVENT_ID, 3, "SMME");
  
}

  public function Further_communication_notification($id){#2
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = $this->assign_event_id($this->whodis, 22, 25);
    $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->whodis);
}


  public function Further_communication_form($id, $state){#4
    $ids=$this->correct_ids($id);
    $COMPANY_ID=$ids[1];
//if yes call set a date email

    $this->update_event_completed($ids[0], $COMPANY_ID, 28, 1);//fixed the 5 day wait anomaly

    if($state == 1){
      $this->handle_reccuring_event($ids[0], $ids[1], 33, 3);//for system wait 5 days after set a date
      $this->Set_a_date_notification($COMPANY_ID);
    }else{
      //if no call Wish to connect notification
      $this->Wish_to_connect_notification($COMPANY_ID);
    }

    header("location: ../".$this->classname."/notifications.php?info=success");
       exit();
}

  public function Wish_to_connect_notification($id){#1
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = 19;
    $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 2, $this->whodis);
   
  }
  public function insert_five_day($id){#1
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = 28;
    $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->whodis);
  }

  public function Wish_to_connect_form($id, $state, $notify_id){#2
    $EVENT_ID = 20;
  //if yes 
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    if($state == 1){
      $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3);
      
      $this->insert_five_day($id);
      $this->shoot_your_shot_notification($id);
      $this->updateNotification($notify_id);
    }else{
      $EVENT_ID = 21;
      //if no insert reject
      // $this->chams($EVENT_ID, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
      
      $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->whodis);
      $this->update_progress($EVENT_ID, 5, $COMPANY_ID, $SMME_NPO_ID);
      $this->updateNotification($notify_id);
  }
}
  public function shoot_your_shot_notification($id){#1
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = 29;
    
    $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->classname);
    // $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, 28, 3);//for system wait 5 days
  }


  private function handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $progress){
    //inserts an event if it hasnt happened before or updates it if it has happened before
    
    //select iteration
    $params=array( $SMME_NPO_ID, $COMPANY_ID,$EVENT_ID);
    $query=$this->master->select('smme_company_events', $this->ITERATION_SELECT[0], $this->ITERATION_SELECT[1], $params);

    if(!$query){
      echo "gentlemen1";
      exit();
    }else{
      $result1=$this->master->getResult();
      $xi=$this->master->numRows();
      //if present update
      
      if($xi!==0){
        // if it has happened before then update
        $increment=$result1["event_iteration"]+1;
        $params=array($increment, $SMME_NPO_ID, $COMPANY_ID,$EVENT_ID);
        $query=$this->master->update('smme_company_events', $this->EVENT_UPDATE[0], $this->EVENT_UPDATE[1], $params);

        if(!$query){
          echo "naughty naughty";
          exit();
        }
        }else{
          //else insert
          
          $params=array($progress, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
          $query=$this->master->Insert('smme_company_events', $this->EVENT_INSERT[0], $this->EVENT_INSERT[1], $params);
          if(!$query){
            echo "naughty naughty";
            exit();
          }
        }
    }
    
    
  }

  private function handle_reccuring_date_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $progress, $date){
    //inserts an event if it hasnt happened before or updates it if it has happened before
    
    //select iteration
    $params=array($EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
    $query=$this->master->select('smme_company_events', $this->ITERATION_SELECT[0], $this->ITERATION_SELECT[1], $params);
    if(!$query){
      echo "naughty naughty";
      exit();
    }else{
      $result1=$this->master->getResult();
      $xi=$this->master->numRows();
      //if present update
      if(!$xi==0){
        $increment=$result1["event_iteration"];
        $increment++;
        // if it has happened before then update
        $params=array($increment, $date, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
        $query=$this->master->update('smme_company_events', $this->DATE_UPDATE[0], $this->DATE_UPDATE[1], $params);
        if(!$query){
          echo "naughty naughty";
          exit();
        }
        }else{
          //else insert
          $params=array($date, $progress, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
          $query=$this->master->insert('smme_company_events', $this->DATE_INSERT[0], $this->DATE_INSERT[1], $params);
          if(!$query){
            echo "naughty naughty";
            exit();
          }
        }
    }
    
    
  }



  private function mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $progress, $who){
    $params=array($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID);
    $query=$this->master->select('smme_company_events', $this->ITERATION_SELECT[0], $this->ITERATION_SELECT[1], $params);
    if(!$query){
      echo "The Yabs1";
      exit();
    }else{
      $result1=$this->master->getResult();
      $xi=$this->master->numRows();
      //select email
      //send email
      if($this->classname == "M_ADMIN"){
        $who = "M_ADMIN";
      }
      $email= $this->select_send_emails($SMME_NPO_ID, $COMPANY_ID, $who, $EVENT_ID);
      
      if($email){//on success
        if($xi!==0){
          $increment=$result1["event_iteration"];
          $increment++;

          
          // if it has happened before then update
          $params=array($increment, $SMME_NPO_ID, $COMPANY_ID, $EVENT_ID);

          $query=$this->master->update('smme_company_events', $this->EVENT_UPDATE[0], $this->EVENT_UPDATE[1], $params);
          if(!$query){
            echo "The Yabs2";
            exit();
          }
        }else{
            // else insert
            $params=array($progress, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
            // print_r($this->EVENT_INSERT[0]);
            // print_r($params);
            // exit();
            $query=$this->master->Insert('smme_company_events', $this->EVENT_INSERT[0], $this->EVENT_INSERT[1], $params);
            if(!$query){
              echo "The Yabs3";
              exit();
            }
          }

      }
    }
  }

  private function mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $progress, $who, $bodyarray = [], $bodyarray1 = [], $bodyarray2 = []){
//$this->select_send_emails($SMME_NPO_ID, $COMPANY_ID, $who, $EVENT_ID, $bodyarray, $bodyarray1, $bodyarray2)
    $email= $this->select_send_emails($SMME_NPO_ID, $COMPANY_ID, $who, $EVENT_ID, $bodyarray, $bodyarray1, $bodyarray2);
  if($email){//on success
  // else insert
  $params=array($progress, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
  $query=$this->master->Insert('smme_company_events', $this->EVENT_INSERT[0], $this->EVENT_INSERT[1], $params);
  $InsertResult = $this->master->getResult();
  return $email;

  }
}

  private function select_send_emails($SMME_NPO_ID, $COMPANY_ID, $who, $EVENT_ID, $bodyarray = [], $bodyarray1 = [], $bodyarray2 = []){
      //select email
  $params=array($SMME_NPO_ID, $COMPANY_ID);
  $query=$this->master->select('smme_company_events', $this->EMAIL_SELECT[0], $this->EMAIL_SELECT[1], $params);
  if(!$query){
    header('"location: ../This bastard 1"');
    exit();
  }else{
    $result2=$this->master->getResult();//send email to specific person
    
 if(($this->classname== "SMME" || $this->classname== "NPO" )&& $EVENT_ID == 10){
      $n_id= $this->chams($EVENT_ID, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
      $address=$result2['SMME EMAIL'];// ################### SQL MUST COINCIDE #################### //
      $name=$result2['COMPANY NAME'];
      $name2=$result2['SMME NAME'];
      $FROM=$COMPANY_ID;
      $TO=$SMME_NPO_ID;
     }else if(($this->classname== "COMPANY" || $this->classname == "P_COMPANY" ) && $EVENT_ID == 11){
        $n_id= $this->chams($EVENT_ID, $SMME_NPO_ID, $COMPANY_ID, "SMME", "COMPANY");
        $address=$result2['COMPANY EMAIL'];// ################### SQL MUST COINCIDE #################### //
        $name2=$result2['COMPANY NAME'];
        $name=$result2['SMME NAME'];
        $TO=$COMPANY_ID;
        $FROM=$SMME_NPO_ID;
     }else if(($this->classname== "SMME" || $this->classname== "NPO" )){
      
      $n_id= $this->chams($EVENT_ID, $SMME_NPO_ID, $COMPANY_ID, "SMME", "COMPANY");
      $address=$result2['COMPANY EMAIL'];// ################### SQL MUST COINCIDE #################### //
      $name2=$result2['COMPANY NAME'];
      $name=$result2['SMME NAME'];
      $FROM=$COMPANY_ID;
      $TO=$SMME_NPO_ID;
     }else if((($this->classname== "COMPANY")||($this->classname== "P_COMPANY"))&& $EVENT_ID != 29){
    
      $n_id= $this->chams($EVENT_ID, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
      $address=$result2['SMME EMAIL'];// ################### SQL MUST COINCIDE #################### //
      $name2=$result2['COMPANY NAME'];
      $name=$result2['SMME NAME'];
      $FROM=$COMPANY_ID;
      $TO=$SMME_NPO_ID;
     }
     else if((($this->classname== "COMPANY")||($this->classname== "P_COMPANY"))&& $EVENT_ID == 29){
      // print_r($this->classname);
      // print_r($EVENT_ID);
      // exit();
      $n_id= $this->chams($EVENT_ID, $SMME_NPO_ID, $COMPANY_ID, "SMME", "COMPANY");
      $address=$result2['COMPANY EMAIL'];// ################### SQL MUST COINCIDE #################### //
      $name2=$result2['COMPANY NAME'];
      $name=$result2['SMME NAME'];
      $TO=$COMPANY_ID;
      $FROM=$SMME_NPO_ID;
     }
   

    // $address=$who=="SMME"?$result2['SMME EMAIL']:($who=="NPO"?$result2['NPO EMAIL']:$result2['COMPANY EMAIL']);
    $body = new mail_body;
    
    //send mail
    try {
      //Instantiate your new class, making use of the new `$body` parameter 
      
      $mail = new Mailer(true);
      
      if($mail->send_single($address, $name2, $body->subject($EVENT_ID, $name), $body->email_contents($EVENT_ID, $name, $name2, $n_id, $who, $bodyarray, $bodyarray1, $bodyarray2)))
      return true;
      else return false;
  } catch (Exception $e) {
      //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
      echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
  }
}
  }

  private function insert_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $progress){
    $params=array($progress, $EVENT_ID, $COMPANY_ID, $SMME_NPO_ID);
    $query=$this->master->Insert('smme_company_events', $this->EVENT_INSERT[0], $this->EVENT_INSERT[1], $params);

    if(!$query){
      if(isset($_GET['id']))
      {
        echo "<br><b>  no.1 -> ".$_GET['id']."</b>";
        exit();
      }else{
        echo 'nex';
        exit();
      }
      // header('"location: ../this bastard 2"');
      exit();
    }
  }

  private function update_progress($EVENT, $Progress, $COMPANY_ID, $SMME_NPO_ID){
    // $params=array($COMPANY_ID, $SMME_NPO_ID);
    // $query=$this->master->select('smme_company_events', $this->PROGRESS_SELECT[0], $this->PROGRESS_SELECT[1], $params);
    // if(!$query){
    //   echo "naughty naughty";
    //   exit();
    // }else{
    //   $result1=$this->master->getResult();
    //   $params=array($Progress, $SMME_NPO_ID, $COMPANY_ID, $EVENT);
    //   $query=$this->master->update('smme_company_events', $this->PROGRESS_UPDATE[0], $this->PROGRESS_UPDATE[1], $params);
    //   if(!$query){
    //     echo "something weird happened";
    //     exit();
    //   }
    //   }
      $params=array($Progress, $SMME_NPO_ID, $COMPANY_ID);
      $query=$this->master->update('smme_company_events', $this->PROGRESS_UPDATE[0], $this->PROGRESS_UPDATE[1], $params);
      if(!$query){
        echo "something weird happened";
        exit();
      }


  }

  private function update_event_completed($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, $zero_or_one){
        //select
        $params=array($SMME_NPO_ID,$COMPANY_ID,$EVENT_ID );
      
        $query=$this->master->select('smme_company_events', $this->ITERATION_SELECT[0], $this->ITERATION_SELECT[1], $params);
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
           
            $query=$this->master->update('smme_company_events', $this->EVENT_COMPLETED_UPDATE[0], $this->EVENT_COMPLETED_UPDATE[1], $params);
            if(!$query){
              echo "something weird happened";
              exit();
            }
          }
            
          }
  }


  private function chams($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO){
    $params=array($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO);
    $query=$this->master->Insert('notifications', NOTIFICATION_INSERT[0], NOTIFICATION_INSERT[1], $params);
    if(!$query){
      header('"location: ../this bastard 3"');
      exit();
    }
    return $this->master->getResult();
  }


  private function correct_ids($id){
    $ids = array();
    
    if($this->classname=="SMME" || $this->classname=="NPO"){
          $ids[0]=session::get($this->id);
          $ids[1]=$id;
        }else{
          $ids[0]=$id;
          $ids[1]=session::get($this->id);
        }
        return $ids;
  }

  private function assign_event_id($id, $smme=NULL, $npo=NULL, $company=NULL){
    $assign=NULL;
    if($id =="SMME"){//smme
      $assign=$smme;
    }
    elseif($id =="COMPANY"){//company
      $assign=$company;
    }
    elseif($id =="NPO"){//npo
      $assign=$npo;
    }
    else{
      header("location: ../id=".$id."");
      exit();
    }
    if($assign==NULL){
      header("location: ../this bastard 5");
      exit();
    }else{
      return $assign;
    }
  }
  
  private function who($id){
    
    $query = $this->master->select_prepared_async(NOTIFICATION_SELECT_WHO[0], DB_NAME_1, NOTIFICATION_SELECT_WHO[1], [$id,$id,$id,$id]);
    if(!$query){
      echo "its this one 1";
      exit();
    }else{
        return $this->master->getResult()[0]["typeOfEntity"];
    }
  }

  public function Set_a_date_notification($id, $CP=null){#1
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = 30;
    if($CP!==null){
      $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->id);
    }else
    $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->whodis);

    header("location: ../".$this->classname."/notifications.php?info=success");
       exit();
  }


   public function Set_a_date_form($id, $state,$notify_id, $date=null){#2
    $EVENT_ID_REJECT = 31;
    $EVENT_ID_ACCEPT = 32;
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
//insert reject
if($state == 0){
  $this->insert_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_REJECT, 3);
  $n_id = $this->chams($EVENT_ID_REJECT, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
  $this->update_progress($EVENT_ID_REJECT, 5, $COMPANY_ID, $SMME_NPO_ID);
  $this->updateNotification($notify_id);
}elseif($date!==null){
//set date
  //select event iteration
  // if it has happened before then update date
  // else insert date
  $this->update_event_completed($SMME_NPO_ID, $COMPANY_ID, 30, 1);
  $this->handle_reccuring_date_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_ACCEPT, 3, $date);
  // select email stuff to send a email to smme with date
  $this->select_send_emails($SMME_NPO_ID, $COMPANY_ID, $this->whodis, $EVENT_ID_ACCEPT);
  $this->updateNotification($notify_id);
  $this->has_meeting_happened_notification($id, $notify_id);
   }

   header("location: ../".$this->classname."/notifications.php?info=success");
       exit();
}

  public function Set_a_date_five_day_wait_admin($id){#1
  // select all companies that meet criteria
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = 33;
    $sql = $this->SET_A_DATE_FIVE_DAY_WAIT_ADMIN_SELECT[0];
    $types =$this->SET_A_DATE_FIVE_DAY_WAIT_ADMIN_SELECT[1];
    $params = array($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID);//specify the event id
    $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, $params);
    if(!$query){
      echo "its this one 1";
      exit();
    }else{
        $result=$this->master->getResult();
        for($i=0; $i<=count($result)-1; $i++){
          $email = $this->Further_communication_notification($COMPANY_ID);
          if(!$email){
            echo "Email failed to send.";
          }
          else{
            $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3);
          }
        }
      }
    }

  public function after_set_date_admin($id, $notify_id){#1
    // select all companies that meet criteria
    //send smme has meeting happened message
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = 34;
    $sql = $this->AFTER_SET_DATE_ADMIN_SELECT[0];
    $types =$this->AFTER_SET_DATE_ADMIN_SELECT[1];
    $params = array($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID);//specify the event id
    $query = $this->master->select_prepared_async($sql, 'smme_company_events', $types, $params);
    if(!$query){
      header("location: ../");
      exit();
    }
    else{
        $result=$this->master->getResult();
        for($i=0; $i<=count($result)-1; $i++){
          $email = $this->has_meeting_happened_notification($SMME_NPO_ID,$notify_id);//not sure
          if(!$email){
            echo "Email failed to send.";
          }
          else{
            $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3);
          }
        }
      }
    }


  public function has_meeting_happened_notification($id,$notify_id){#2
    //button click
    //select event iteration
    //select email
    //send mail
    //on success

    // if it has happened before then update
    // else insert
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID = $this->assign_event_id($this->whodis, 35, 38);
    $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->whodis);
    $this->updateNotification($notify_id);
  }
  public function has_meeting_happened_form($state, $id){#4
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID_REJECT = $this->assign_event_id($this->classname, 37, 40);
    $EVENT_ID_ACCEPT = $this->assign_event_id($this->classname, 36, 39);
  if($state==1){
    // if yes
    // call finalized notification
    $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_ACCEPT, 3, $this->whodis);
    $this->is_finalized_notification($COMPANY_ID);
  }
  else{
    // if no 
    // call set a date notification
    $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_REJECT, 3, $this->whodis);
    $this->update_event_completed($SMME_NPO_ID, $COMPANY_ID, 30, 0);// not working
    $this->Set_a_date_notification($COMPANY_ID);
  }
}
  
  public function is_finalized_notification($id){#1
  $ids=$this->correct_ids($id);
  $SMME_NPO_ID=$ids[0];
  $COMPANY_ID=$ids[1];
  $EVENT_ID = 41;
  //button click
  //select event iteration
  //select email
  //send mail
  //on success
  // if it has happened before then update
  // else insert


  
  $this->mail_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID, 3, $this->whodis);

  header("location: ../".$this->classname."/notifications.php?info=success");
       exit();
  }

private function updateNotification($id){#1
    
    $sql = $this->NOTIFICATION_VIEWED[0];
    $types = $this->NOTIFICATION_VIEWED[1];
    $params = array($id);
    $this->master->update('openlink_association_db.notifications', $sql, $types, $params, 1);
  
    return true;
       
  }

 public function is_finalized_form($id, $state, $notify_id){#3
    $ids=$this->correct_ids($id);
    $SMME_NPO_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $EVENT_ID_REJECT = 44;
    $EVENT_ID_NOT_FINALIZED = 43;
    $EVENT_ID_ACCEPT = 42;
    if($state == 1){//if yes insert finalized//message smme
    
    $this->update_progress($EVENT_ID_ACCEPT, 4, $COMPANY_ID, $SMME_NPO_ID);
    $this->updateNotification($notify_id);

    $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_ACCEPT, 4, $this->whodis);
    $this->chams($EVENT_ID_ACCEPT, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
  }
  elseif($state == 2){
    $this->handle_reccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_NOT_FINALIZED , 3);
    $this->updateNotification($notify_id);
    $this->update_event_completed($SMME_NPO_ID, $COMPANY_ID, 30, 0);
    $this->Set_a_date_notification($SMME_NPO_ID, "any value");
  }else{
      $this->mail_nonreccuring_event($SMME_NPO_ID, $COMPANY_ID, $EVENT_ID_REJECT , 3, $this->whodis);
      $this->update_progress($EVENT_ID_REJECT, 5, $COMPANY_ID, $SMME_NPO_ID);
      $this->updateNotification($notify_id);
      $this->chams($EVENT_ID_REJECT, $COMPANY_ID, $SMME_NPO_ID, "COMPANY", "SMME");
  }
  header("location: ../".$this->classname."/notifications.php?info=success");
       exit();
    
  //if no
      //call set a date
      //or
      // insert reject
  }
      
}
