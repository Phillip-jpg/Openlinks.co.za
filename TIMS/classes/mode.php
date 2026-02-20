<?php
use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.mode.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../classes/mode_mail_body.class.php');

abstract class mode {
protected $master;
function __construct(){
    $this->id = $_SESSION[$this->idname];
    $this->master = new Master($this->db);
}

public function bookmark(){}



public function get_all(){}
public function get_individual(){}

public function candidate($id){
    $params = array($id, 2);
    $query=$this->master->update('', $this->CANDIDATE_UPDATE[0], $this->CANDIDATE_UPDATE[1], $params);
    if(!$query){
        echo "flop";
        exit();
    }
    $params = array($this->id);
    $query=$this->master->select_prepared_async($this->NON_CANDIDATE_SELECT[0], $this->db1 , $this->NON_CANDIDATE_SELECT[1], $params);
    if(!$query){
        echo "flop2";
        exit();
    }
    else{
     $result=$this->master->getResult();
     $mail = new Mailer(true);
      if(empty($result)){

      }else{
        
        try {
            $mail->send_multiple($result , '', '', '');
        } catch (Exception $e) {
            //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
            echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
        }
    }
}
$query=$this->master->delete('', $this->NON_CANDIDATE_DELETE[0], $this->NON_CANDIDATE_DELETE[1], $params);
if(!$query){
    echo "flop3";
    exit();
}
$params = array($this->id);
$query=$this->master->select("", $this->CANDIDATE_SELECT[0], $this->CANDIDATE_SELECT[1], $params);
if(!$query){
    echo "flop4";
    exit();
}
$result=$this->master->getResult();
try {
    $mail = new Mailer(true);
    $mail->send_single($result['email'], $result['First_Name'], '', '');
} catch (Exception $e) {
    //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
    echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
}

}
public function reject($id){
    $params = array($id, 3);
    $query=$this->master->update('', $this->CANDIDATE_UPDATE[0], $this->CANDIDATE_UPDATE[1], $params);
    if(!$query){
        echo "flop";
        exit();
    }
    $params = array($this->id);
$query=$this->master->select("", $this->CANDIDATE_SELECT[0], $this->CANDIDATE_SELECT[1], $params);
if(!$query){
    echo "flop4";
    exit();
}
$result=$this->master->getResult();
try {
    $mail = new Mailer(true);
    $mail->send_single($result['email'], $result['First_Name'], '', '');
} catch (Exception $e) {
    //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
    echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
}
}


private function data($result){
    $output = '';
    for($i=0; $i<=count($result)-1; $i++){
        $output .= '<a href="profile.php?id='. $result[$i]['ID'] .'">
        <div class="content">
        <img src="'. $result[$i]['ext'] .'" alt="">
        <div class="details">
            <span>'. $result[$i]['First_Name'].'</span>
        </div>
        </div>
    </a>';
    }
}
public function initiate_notification($id)#3
{
  $EVENT_ID = 1;
  $ids=$this->correct_ids($id);
  $CONSULTANT_ID=$ids[0];
  $COMPANY_ID=$ids[1];

  $this->send_emails($CONSULTANT_ID, $COMPANY_ID,$this->who1, $EVENT_ID);
}


public function initiate_form($id, $state){ 
    $ids=$this->correct_ids($id);
    $CONSULTANT_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    //if yes insert finalized//message smme
    if($state == 0){//no
        $EVENT_ID = 0;
        $this->send_emails($CONSULTANT_ID, $COMPANY_ID, $this->who1, $EVENT_ID);
    }
    else{//yes
        if($this->classname == "COMPANY"){
            $this->send_emails($CONSULTANT_ID, $COMPANY_ID, $this->who1, 4);//who here needs to always be consultant
        }else if($this->classname == "CONSULTANT"){
            $this->send_emails($CONSULTANT_ID, $COMPANY_ID, $this->who, 4);
            $this->send_emails($CONSULTANT_ID, $COMPANY_ID, $this->who1, 5);
        }
        $EVENT_ID = 1;
        $link = $this->create_link();
        $params=array($CONSULTANT_ID, $COMPANY_ID, $EVENT_ID, $link);
        $query=$this->master->insert('', $this->STAGE_INSERT[0], $this->STAGE_INSERT[1], $params);
        if(!$query){
            header('"location: ../This bastard didnt insert"');
            exit();
        }else{
            header("location: ../".$this->classname."_userProfile.php?success");
            exit();
        }
    }
}

private function gen_token($id){
    $x=0;
    while($x==0){
        $selector = bin2hex(random_bytes(32));
        $query=$this->master->select('', $this->LINK_SELECT[0], $this->LINK_SELECT[1], [$selector]);
        if(!$query){
        header('"location: ../This bastard 1"');
        exit();
        }else{
        $result=$this->master->getResult();
        if(empty($result)){
            $x=1;
            $link ="www.openlinks.co.za/linkforconsultant=".$selector."design";
        }
            }
        }
        $params=array($id, $link);
        $query=$this->master->insert('', $this->TOKEN_INSERT[0], $this->TOKEN_INSERT[1], $params);
        if(!$query){
            header('"location: ../Eish it did not insert thats what she said !"');
            exit();
          }
          else{
              echo $link;
          }
        
}


private function create_link(){
    $x=0;
    while($x==0){
        $selector = bin2hex(random_bytes(32));
        $query=$this->master->select('', $this->LINK_SELECT[0], $this->LINK_SELECT[1], [$selector]);
        if(!$query){
        header('"location: ../This bastard 1"');
        exit();
        }else{
        $result=$this->master->getResult();
        if(empty($result)){
            $x=1;
        }
            }
        }
        return $selector;
}


private function send_emails($CONSULTANT_ID, $COMPANY_ID, $who, $EVENT_ID){
    //select email
$params=array($CONSULTANT_ID, $COMPANY_ID);
$query=$this->master->select('', $this->EMAIL_SELECT[0], $this->EMAIL_SELECT[1], $params);
if(!$query){
  header('"location: ../This bastard 1"');
  exit();
}else{
  $result2=$this->master->getResult();//send email to specific person
  if($who=="CONSULTANT"){
    $address=$result2['CONSULTANT EMAIL'];// ################### SQL MUST COINCIDE #################### //
    $name=$result2['COMPANY NAME'];
    $name2=$result2['CONSULTANT NAME'];
    $FROM=$COMPANY_ID;
    $TO=$CONSULTANT_ID;
  }else{//company
    $address=$result2['COMPANY EMAIL'];
    $FROM=$CONSULTANT_ID;
    $TO=$COMPANY_ID;
    $name=$result2['SMME NAME'];
    $name2=$result2['COMPANY NAME'];
    }
    
  }
  //send mail
  $this->chams($EVENT_ID, $FROM, $TO);
  $body = new mode_mail_body;
  try {
    $mail = new Mailer(true);
    if($EVENT_ID == 3){
        if($mail->send_multiple($result2, "Openlinks email", $body->email_body($EVENT_ID, $name)))
        return true;
        else return false;
    }
    if($mail->send_single($address, $name2, "Openlinks email", $body->email_body($EVENT_ID, $name)))
    return true;
    else return false;
} 
catch (Exception $e) {
    //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
    echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
}
}

private function chams($EVENT_ID, $FROM, $TO){
$params=array($EVENT_ID, $FROM, $TO);
$query=$this->master->insert('notifications', $this->NOTIFICATION_INSERT[0], $this->NOTIFICATION_INSERT[1], $params);
if(!$query){
  header('"location: ../this bastard 3"');
  exit();
}
}
private function correct_ids($id){
    $ids = array();
    if($this->classname=="CONSULTANT"){
      $ids[0]=session::get($this->id);
      $ids[1]=$id;
        }else{
      $ids[0]=$id;
      $ids[1]=session::get($this->id);
        }
        return $ids;
  }

}