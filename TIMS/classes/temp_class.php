<?php

class CONSULTANT_MODE{

    public function send_request_notification($id)#3
    {
      $EVENT_ID = 1;
      $ids=$this->correct_ids($id);
      $CONSULTANT_ID=$ids[0];
      $COMPANY_ID=$ids[1];
      //$who?
      $this->send_emails($CONSULTANT_ID, $COMPANY_ID,$who, $EVENT_ID);
    }
  
  public function get_all($id){
     $stage=1;
     $ids=$this->correct_ids($id);
     $CONSULTANT_ID=$ids[0];
     $COMPANY_ID=$ids[1];
     $params = array($CONSULTANT_ID,$COMPANY_ID,$stage);
            $query=$this->master->select('', $this->GET_ALL_SELECT[0], $this->GET_ALL_SELECT[1], $params);
            if(!$query){
                header('"location: ../This bastard didnt select"');
                exit();
            }else{
              $xi=$this->master->result();
              if($xi==1){
                  data($xi);
              }else{
                  return true;
              }
  }}
  public function get_indivual($id){
    $ids=$this->correct_ids($id);
     $CONSULTANT_ID=$ids[0];
     $COMPANY_ID=$ids[1];
     $params = array($CONSULTANT_ID,$COMPANY_ID,$stage);
     $query=$this->master->select('', $this->GET_INDIVUAL[0], $this->GET_INDVIDUAL[1], $params);
            if(!$query){
                header('"location: ../This bastard didnt select"');
                exit();
            }else{
              $xi=$this->master->numRows();
              if($xi>0){
                  data($xi);
              }
  }}



  public function bookmark($id){
    $ids=$this->correct_ids($id);
    $CONSULTANT_ID=$ids[0];
    $COMPANY_ID=$ids[1];
    $params = array($CONSULTANT_ID,$COMPANY_ID,$stage);
     $query=$this->master->insert('', $this->BOOKMARK[0], $this->BOOKMARK[1], $params);
     if(!$query){
      header('"location: ../This bastard didnt insert"');
      exit();
  }else{
    header("location: ../".$this->classname."_userProfile.php?success");
    exit();
    }
  }
    public function send_request_form($id, $state){ 
        $ids=$this->correct_ids($id);
        $CONSULTANT_ID=$ids[0];
        $COMPANY_ID=$ids[1];
        //if yes insert finalized//message smme
        if($state == 0){//no
            $EVENT_ID = 0;
            $this->send_emails($CONSULTANT_ID, $COMPANY_ID, $who, $EVENT_ID);
        }
        else{//yes
            if($this->classname == "COMPANY"){
                $this->send_emails($CONSULTANT_ID, $COMPANY_ID, $who, 4);//who here needs to always be consultant
            }
            $EVENT_ID = 1;
            $params=array($CONSULTANT_ID, $COMPANY_ID, $EVENT_ID);
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
      $body = new Cons_mail_body;
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

      public function display_searches(){
        $query = $this->master->select_multiple_async($this->ADMIN_SEARCHES[0], $this->ADMIN_SEARCHES[1], );
        if(!$query){

        }else{

        }
      }
}

  
      
