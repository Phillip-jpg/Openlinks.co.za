<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.unauth.php');
include_once($filepath.'/../config/config.php');

use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';
require 'mail_body.class.php';
class unauth {

protected $master;
protected $SEARCH_INSERT;
function __construct(){
    $this->master = new Master(DB_NAME_7);
}

public function search($term, $id){
if (!empty($term)){

    $result = $this -> simple_search($term);

$params=array(count($result), $id, $term);
$this->master->changedb(DB_NAME_7);
$query=$this->master->Insert('search', UNAUTH_SEARCH_INSERT[0], UNAUTH_SEARCH_INSERT[1], $params);

if(!$query){

    echo "Internal Error, Please Try again. 1";

    exit();

  }

  if(empty($result)){

    $output =  "No results found, try again.";

}else{

    $output =  $this->data($result);

}
}else{

    $output = "No Search term";

}

echo $output;


}

private function simple_search($term){

    $params = array($term, $term);

    $query=$this->master->select_prepared_async(UNAUTH_SEARCH_SELECT[0], DB_NAME_1, UNAUTH_SEARCH_SELECT[1], $params);

    if(!$query){

    echo "Internal Error, Please Try again. 2";

    exit();

    }else{

    return $this->master->getResult();

    }
}



public function insert_form_data($name, $surname, $email, $contact, $subject, $message){
    $params=array($name,$surname, $email, $contact, $subject, $message);
    
    $query=$this->master->Insert('contactForm', UNAUTH_CONTACT_FORM_INSERT[0], UNAUTH_CONTACT_FORM_INSERT[1], $params);
    if(!$query){
        
        echo "Internal Error, Please Try again. 2";
        
        exit();
    
    }else{
        $emailBody = new mail_body();
        $body = $emailBody->email_contents(1, $subject, $name, $surname, $email, $contact, $message);
        $mail = $this->contact_form($name, $email, $subject, $body);
        if($mail){
            echo '<section class="mosh-clients-area clearfix" id="searchanchor" style="padding-top:5em">
            <div class="modal" tabindex="-1" role="dialog" style="z-index:1 !important">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message Sucess</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Message sent successfully </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div></section>';
            exit(); 
        }
        else{
            header("location: ../contact.php?message=failed");
            exit();
        }
        
    
    }
}

private function contact_form($name, $email, $subject, $message){
    $to_address = $email;
    if(empty($subject)){
        $subject = "Contact Form Query";
    }
    try {
        //Instantiate your new class, making use of the new `$body` parameter 
        $mail = new Mailer(true);
        if($mail->send_single($to_address, $name, $subject, $message )){
            return true;
        }
        else {return false;}
    } catch (Exception $e) {
        //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
        echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
    }
    
}

}