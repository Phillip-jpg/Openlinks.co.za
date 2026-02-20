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

private function data($result){
    $output = '';
    foreach($result as $row){

    $output .= '<div class="card col-lg-4 col-xl-3 col-md-6 col-sm-12" style="margin:1em; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
                <div class="card-body">
                <div class="team-thumbnail">
                <img src="'. $row['ext'] .'" alt="" class="img-circle img-responsive">
                </div>
                <div class="team-meta-info">
                    <h4>'.$row['Legal_name'].'</h4>
                    <span>Entity: '.$row['typeOfEntity'].'</span>
                    <p><b>Industry: </b>'.$row['title'].'</p>
                </div>

                </div>
                <button type="submit" name="VIEW_MORE" class="btn " data-toggle="modal"
                data-target="#DeleteModal"><i class="fa fa-address-card"></i></button>
            </div>';
            $output .= ' <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div style="width: 800px !important" class="modal-dialog col-lg-12 col-md-9 col-sm-9" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="text-center">Login</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                 
                    <div class="modal-body">
                    <h5 class="modal-title text-capitalize" id="exampleModalLabel">You have to Login to View more information about this entity</h5><br>
                    <div class="row">
                        <a href="SMME/login.php" class="btn mosh-btn col-lg-3" style="">SMME</a>
                        <a href="COMPANY/login.php" class="btn mosh-btn col-lg-3">Company</a>
                        <a href="NPO/login.php" class="btn mosh-btn col-lg-3">NPO</a>
                        <a href="CONSULTANT/login.php" class="btn mosh-btn col-lg-3">Consultant</a>
                    </div>
                    </div>
                </div>
            </div>
        </div>';
    
}

return $output;

}

public function contact_form($name, $email, $subject, $message){
    $to_address = "info@openlinks.co.za";
    if(empty($subject)){
        $subject = "Contact Form Query";
    }
    try {
        //Instantiate your new class, making use of the new `$body` parameter 
        $mail = new Mailer(true);
        if($mail->send_single($to_address, $name, $subject, $message ))
        return true;
        else return false;
    } catch (Exception $e) {
        //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
        echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
    }
    
}

}