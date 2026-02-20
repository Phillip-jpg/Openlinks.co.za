<?php
use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';
require 'mail_body.class.php';

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../config/sql.notify.config.php');
include_once($filepath.'/../config/sql.notification.php');


abstract class connection{

    protected $master;
    function __construct(){
        $this->id = $_SESSION[$this->idname];
        $this->master = new Master($this->db);
    }

    public function handle_connection($link){
        $result = $this->check_link($link); 
        if(empty($result)){
            echo "The link seems to have expired. Please request for a new link.";
            exit();
        }else{
            $new_link = $this->create_new_link();
            $COMPANY_ID = $result['COMPANY_ID'];
            $CONSULTANT_ID = $this->id;
            return $this->establish_connection($COMPANY_ID, $CONSULTANT_ID, $new_link);
        }
    }

    public function gen_link($id){
        $params= array($id);
        $query=$this->master->delete("consultant_links",$this->GEN_LINK_DELETE[0],$this->GEN_LINK_DELETE[1],$params);
        $x=0;
        while($x==0){
            $selector = bin2hex(random_bytes(32));
            $query=$this->master->select('', $this->GEN_LINK_SELECT[0], $this->GEN_LINK_SELECT[1], [$selector]);
            if(!$query){
            header('"location: ../This error in select"');
            exit();
            }else{
            $result=$this->master->getResult();
            if(empty($result)){
                $x=1;
                $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/TIMS/CONSULTANT/submit_link.php?url=".$selector;
            }
                }
            }
            $params=array($selector,$id);
            $query=$this->master->insert('', $this->GEN_LINK_INSERT[0], $this->GEN_LINK_INSERT[1], $params);
            if(!$query){
                header('"location: ../error with insert did not insert"');
                exit();
              }
              else{
                  echo $link;
              }
            
    }
private function create_new_link(){
    $x=0;
    while($x==0){
        $selector = bin2hex(random_bytes(32));
        $query=$this->master->select('',  $this->CREATE_NEW_LINK_SELECT[0],  $this->CREATE_NEW_LINK_SELECT[1], [$selector]);
        if(!$query){
            echo "Error_checking_connection_link 3";
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
    private function check_link($link){
        $this->master->changedb($this->db1);
        $query = $this->master->select('', $this->GEN_LINK_SELECT[0], $this->GEN_LINK_SELECT[1], array($link));
        if(!$query){
            echo "Error_checking_connection_link 1_".$link;
            // header('"location: ../Error_checking_connection_link"');
            exit();
        }else{
            $result = $this->master->getResult();
            return $result;
        }
    }
    
    private function establish_connection($COMPANY_ID, $CONSULTANT_ID, $link){
        $params = array(
            $COMPANY_ID,
            $CONSULTANT_ID, 
            1, 
            $link
        );
        $query = $this->master->insert('company_consultant_association', $this->CONNECTION_INSERT[0], $this->CONNECTION_INSERT[1], $params);
        if(!$query){
            return "You have already connected with ".$names["WHO"].". Please ask ".$names["WHO"]." to enable you to control their account.";
        }else{

            $names = $this->select_send_emails($CONSULTANT_ID, $COMPANY_ID, $this->who1, 45);

            return "You are now connected to ".$names["WHO"].". Please ask ".$names["WHO"]." to enable you to control their account.";

        }
    }

    public function enable_control($COMPANY_ID, $CONSULTANT_ID, $stage = 2){
        $params = array(
            $stage,
            $COMPANY_ID,
            $CONSULTANT_ID
        );
        $query = $this->master->update('company_consultant_association', ENABLE_CONTROL_UPDATE[0], ENABLE_CONTROL_UPDATE[1], $params);
        if(!$query){

            // header('"location: ../'.$this->who.'/notifications.php?flop"');

            echo "flop";

            exit();

        }else{

            $this->multiple_send_emails([$CONSULTANT_ID, $CONSULTANT_ID], [$COMPANY_ID, $COMPANY_ID], [$this->who1, $this->who], [46, 47], [$this->who1]);

            // header('"location: ../'.$this->who.'/notifications.php"');

            echo "<script>window.location.replace('../".$this->who."/notifications.php');</script>";

            exit();

        }
    }

    public function revoke_control($COMPANY_ID, $CONSULTANT_ID){
        $params = array(
            $COMPANY_ID,
            $CONSULTANT_ID
        );
        $query = $this->master->delete('company_consultant_association', ENABLE_CONTROL_DELETE[0], ENABLE_CONTROL_DELETE[1], $params);
        if(!$query){

            echo "flop";

            exit();

        }else{

            $params = array(
                $COMPANY_ID
            );

            $query = $this->master->delete('company_consultant_association', ENABLE_CONTROL_CONSULTANT_LINK_DELETE[0], ENABLE_CONTROL_CONSULTANT_LINK_DELETE[1], $params);
            if(!$query){
    
                echo "flop2";
    
                exit();
    
            }else{

                $this->multiple_send_emails([$CONSULTANT_ID, $CONSULTANT_ID], [$COMPANY_ID, $COMPANY_ID], [$this->who1, $this->who], [48, 49], [$this->who1]);
    
                echo "<script>window.location.replace('../".$this->who."/notifications.php');</script>";
    
                exit();
    
            }

        }
    }

    public function select_send_emails($CONSULTANT_ID, $COMPANY_ID, $who, $EVENT_ID, $bodyarray = [], $bodyarray1 = [], $bodyarray2 = [])
    {
        $params=array($CONSULTANT_ID, $COMPANY_ID);
        $query=$this->master->select('signup', CONSULTANTS_EMAIL_SELECT[0], CONSULTANTS_EMAIL_SELECT[1], $params);

        if(!$query){
          header('"location: ../This bastard 1"');
          exit();
        }else{
          $result2=$this->master->getResult();//send email to specific person
          if($who=="CONSULTANT"){
            $address=$result2['CONSULTANT EMAIL'];// ################### SQL MUST COINCIDE #################### //
            $name=$result2['COMPANY NAME'];
            $name2=$result2['CONSULTANT NAME'];
            $FROM=$this->id;
            $TO=$CONSULTANT_ID;
          }else{//company
            $address=$result2['COMPANY EMAIL'];
            $FROM=$this->id;
            $TO=$COMPANY_ID;
            $name=$result2['CONSULTANT NAME'];
            $name2=$result2['COMPANY NAME'];
          }
          
          
          if($EVENT_ID == 47 || $EVENT_ID == 49){
              $nameyyy = $result2[$bodyarray.' NAME'];
            $n_id = $this->chams($EVENT_ID, $FROM, $TO, $_SESSION["WHO"], $who, $nameyyy);//inserts the notification
          }else{
            $n_id = $this->chams($EVENT_ID, $FROM, $TO, $_SESSION["WHO"], $who);//inserts the notification
          }

          $body = new mail_body;

          //send mail
          try {
            //Instantiate your new class, making use of the new `$body` parameter 
            $mail = new Mailer(true);
            if($EVENT_ID == 47 || $EVENT_ID == 49){
                $mail->send_single($address, $name2, $body->subject($EVENT_ID, $nameyyy), $body->email_contents($EVENT_ID, $nameyyy, $name2, $n_id, $who, $bodyarray, $bodyarray1, $bodyarray2));
            }else{
                $mail->send_single($address, $name2, $body->subject($EVENT_ID, $name), $body->email_contents($EVENT_ID, $name, $name2, $n_id, $who, $bodyarray, $bodyarray1, $bodyarray2));
            }
            
        } catch (Exception $e) {
            //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
            echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
        }
        $names = array("WHO" => $name2, "ME" => $name);
        return $names;
      }

    }


    public function multiple_send_emails(array $CONSULTANT_ID, array $COMPANY_ID, array $who, array $EVENT_ID, $bodyarray = [], $bodyarray1 = [], $bodyarray2 = [])
    {
        $emails = array();
        $names = array();
        $subject = array();
        $htmlbody = array();
        $BAcount = 0;

        for ($x = 0; $x < count($CONSULTANT_ID); $x++){

            $params=array($CONSULTANT_ID[$x], $COMPANY_ID[$x]);
            $query=$this->master->select('signup', CONSULTANTS_EMAIL_SELECT[0], CONSULTANTS_EMAIL_SELECT[1], $params);
            if(!$query){
                header('"location: ../This bastard 1"');
                exit();
              }else{
                $result2=$this->master->getResult();//send email to specific person
                if($who[$x]=="CONSULTANT"){
                  $address=$result2['CONSULTANT EMAIL'];// ################### SQL MUST COINCIDE #################### //
                  $name=$result2['COMPANY NAME'];
                  $name2=$result2['CONSULTANT NAME'];
                  $FROM=$this->id;
                  $TO=$CONSULTANT_ID[$x];
                }else{//company
                  $address=$result2['COMPANY EMAIL'];
                  $FROM=$this->id;
                  $TO=$COMPANY_ID[$x];
                  $name=$result2['CONSULTANT NAME'];
                  $name2=$result2['COMPANY NAME'];
                }

                if($EVENT_ID[$x] == 47 || $EVENT_ID[$x] == 49){
                    $nameyyy = $result2[$bodyarray[$BAcount].' NAME'];
                  $n_id = $this->chams($EVENT_ID[$x], $FROM, $TO, $_SESSION["WHO"], $who, $nameyyy);//inserts the notification
                }else{
                  $n_id = $this->chams($EVENT_ID[$x], $FROM, $TO, $_SESSION["WHO"], $who);//inserts the notification
                }

                $body = new mail_body;
                if($EVENT_ID[$x] == 47 || $EVENT_ID[$x] == 49){
                array_push($emails, $address);
                array_push($names, $name2);
                array_push($subject, $body->subject($EVENT_ID[$x], $nameyyy));
                array_push($htmlbody, $body->email_contents($EVENT_ID[$x], $nameyyy, $name2, $n_id, $who[$x], $bodyarray[$BAcount], $bodyarray1, $bodyarray2));
                $BAcount++;
                }else{
                array_push($emails, $address);
                array_push($names, $name2);
                array_push($subject, $body->subject($EVENT_ID[$x], $name));
                array_push($htmlbody, $body->email_contents($EVENT_ID[$x], $name, $name2, $n_id, $who[$x], $bodyarray[$BAcount], $bodyarray1, $bodyarray2));
                }

        }
      }

    //   print_r($emails);
    //   echo "<br>";
    //   print_r($names);
    //   echo "<br>";
    //   print_r($subject);
    //   echo "<br>";
    //   print_r($htmlbody);
    //   exit();

        //send mail
        try {
        //Instantiate your new class, making use of the new `$body` parameter 
        $mail = new Mailer(true);

        $mail->send_multiple($emails, $names, $subject, $htmlbody);
        
    } catch (Exception $e) {
        //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
        echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
    }

    }


    private function chams($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO, $Description = Null){
        if($Description == Null){

            $params=array($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO);
            $query=$this->master->Insert('notifications', NOTIFICATION_INSERT[0], NOTIFICATION_INSERT[1], $params);
            if(!$query){
                echo "Server error";
    
                exit();
            }
            return $this->master->getResult();

        }else{
            $params=array($EVENT_ID, $FROM, $TO, $WHO_FROM, $WHO_TO, $Description);
            $query=$this->master->Insert('notifications', NOTIFICATION_INSERT_DESCRIPTION[0], NOTIFICATION_INSERT_DESCRIPTION[1], $params);
            if(!$query){
                echo "Server error";
    
                exit();
            }
            return $this->master->getResult();
        }
      }




    public function control($link){
        if($link==""){
            echo 0;
            exit();
        }
        $params = array($link, Session::get('CONSULTANT_ID'));
        $query = $this->master->select('', CONTROL_LINK_SELECT[0], CONTROL_LINK_SELECT[1], $params);
        if(!$query){
            echo 0;
            exit();
        }else{
            $result = $this->master->getResult();
            if(empty($result)){
                echo 0;
                exit();
            }
            Session::set("COMPANY_ID", $result['COMPANY_ID']);
            Session::set("P_COMPANY_ID", $result['COMPANY_ID']);
            Session::set("P_COMPANY_LINK", $link);
            Session::set("WHO", "P_COMPANY");
            Session::set("PSEUDO_ID", token::encode('10011010181'));
            Session::set("PSEUDO_TIME", date("H:i"));
            
            echo 1;
            exit();
        }
    }

    public function val_control(){
        $link= Session::get("P_COMPANY_LINK");
        $params = array($link, Session::get('CONSULTANT_ID'));
        $query = $this->master->select('', CONTROL_LINK_SELECT[0], CONTROL_LINK_SELECT[1], $params);
        if(!$query){
            echo "FALSE";
            exit();
        }else{
            $result = $this->master->getResult();
            if(empty($result)){
                echo "FALSE";
                exit();
            }
        }
    }
    
    public function get_controllable(){
        $params = array(2, $this->id);
        $query = $this->master->select_prepared_async(GET_CONTROLLABLE_SELECT[0], DB_NAME_5, GET_CONTROLLABLE_SELECT[1], $params);
        if(!$query){
            echo "connecting error!";
            exit();
        }else{
            $result = $this->master->getResult();
           
            if(empty($result)){
                echo '<li><a href="#">~none~</a></li>';
                exit();
            }
            foreach($result as $company){
                // $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/Project%20One/P_COMPANY/index.php?lk=".$company["link"];
                $link = $company["link"];
                echo '<li><a data-link-control="'.$link.'" data-credz="'.token::encode($company["COMPANY_ID"]).'" id="link_control">'.$company["Legal_name"].'</a></li>';
            }
        }
    }





}