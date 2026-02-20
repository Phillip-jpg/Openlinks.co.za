<?php
use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.pass_r.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../helpers/val.php');

class pass_r {
protected $master;
protected $w;
function __construct($db){
    $database = '';
    if($db == 's'){
        $database = DB_NAME_1;
    }elseif($db == 'n'){
        $database = DB_NAME_2;
    }elseif($db == 'c'){
        $database = DB_NAME_3;
    }elseif($db == 'cc'){
        $database = DB_NAME_4;
    }else{
        header("location: index.php");
        exit();
    }
    $this->w = $db;
    $this->master = new Master($database);
}

public function mail_tokens($email){

    
    $params = array($email);
    if($this->w == 'c'){
        $query=$this->master->select('', CHECK_F_EMAIL_SELECT_C[0], CHECK_F_EMAIL_SELECT_C[1], $params);
    }else if($this->w == 'cc'){
        $query=$this->master->select('', CHECK_F_EMAIL_SELECT_CC[0], CHECK_F_EMAIL_SELECT_CC[1], $params);
    }else{
        $query=$this->master->select('', CHECK_F_EMAIL_SELECT[0], CHECK_F_EMAIL_SELECT[1], $params);
    }
   
    if(!$query){
        echo"Databaserror";
        exit();
    }
    else{
     $result=$this->master->getResult();
      if(empty($result)){
        echo("email does not exist");
        exit();
      }else{
        $gen_token =$this->gen_tokens();
        $hashed_token = password_hash($gen_token[0], PASSWORD_DEFAULT);
        $time = time();
        $time = $time + 6900;
        $time = date("Y-m-d h:m:s", $time);
        if($this->w == 'c'){
            $params = array($gen_token[1],$hashed_token,$time,$result['COMPANY_ID']);
            
            $query=$this->master->insert('pass_r', INSERT_TOKEN_C[0], INSERT_TOKEN_C[1], $params);
        }else if($this->w == 'cc'){
            $params = array($gen_token[1],$hashed_token,$time,$result['CONSULTANT_ID']);
            
            $query=$this->master->insert('pass_r', INSERT_TOKEN_CC[0], INSERT_TOKEN_CC[1], $params);
        }else{
            $params = array($gen_token[1],$hashed_token,$time,$result['SMME_ID']);
            $query=$this->master->insert('', INSERT_TOKEN[0], INSERT_TOKEN[1], $params);
        }
        
    }
     $to = $email;
    
     $url="https://openlinks.co.za/TIMS/Password/Password_Recovery.php?s=".$gen_token[1]."&v=".$hashed_token."&w=".$this->w;
     $subject ="Reset password for OpenLinks";
     $message ="<p>We recieved a password request link.</p>";
     $message.="<p>Reset your password here</br> .$url.</p>";
     $header="From Openlinks <openlinks@info.com>";
     $ENTITY = "";
    switch($this->w){
        case "c":
            $ENTITY = "COMPANY";
            break;
        case "cc":
            $ENTITY = "CONSULTANT";
            break;
        case "s":
            $ENTITY = "SMME";
            break;
        case "a":
            $ENTITY = "ADMIN";
            break;
        
            
    }
     try {
        $mail = new Mailer(true);
        $mail->send_single($to, $result['name'], $subject, $message);
    } catch (Exception $e) {
        //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
        echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
    }
    header("location: ../".$ENTITY."/login.php?error=emailpsent");
    exit();
}
}
private function gen_tokens(){
    $selector = bin2hex(random_bytes(8));
    $token = bin2hex(random_bytes(32));
    $tokens = array($selector, $token);
    return $tokens;
}

public function savepassword($pass, $passR, $selector, $validator){
    if($pass !== $passR){
        header("location: Password_R.php?s=$selector&v=$validator&error=InvalidPassword");
        exit();
    }
    $validator2 = $validator;
    $params = array($selector, $validator2);

    if($this->w == 'c'){
        $query=$this->master->select('', CHECK_EXISTS_SELECT_C[0], CHECK_EXISTS_SELECT_C[1], $params);
    }else if($this->w == "cc"){
        $query=$this->master->select('', CHECK_EXISTS_SELECT_CC[0], CHECK_EXISTS_SELECT_CC[1], $params);
        
    }else{
        $query=$this->master->select('', CHECK_EXISTS_SELECT[0], CHECK_EXISTS_SELECT[1], $params);   
    }
    
    if(!$query){
        header("location: Password_R.php?s=$selector&v=$validator&error=DatabaseError");
        exit();
    }else{
      $result=$this->master->getResult();
      if(empty($result)){
        
        header("location: Password_R.php?s=$selector&v=$validator&error=InvalidCredentials");
        exit();
      }else{
        $time = strtotime($result['expiryDate']);
        $curtime = time();
        
        if($time-$curtime >70000){
            $params = array($selector);
          
                $query=$this->master->delete('', DELETE_TOKEN[0], DELETE_TOKEN[1], $params);
            
            if(!$query){
                header("location: Password_R.php?s=$selector&v=$validator&error=DatabaseError");
                exit();
            }
            header("location: Password_R.php?s=$selector&v=$validator&error=ExpiredSession");
            exit();
        }else{
            
            if($this->w == 'c'){
                $params = array(password_hash($pass, PASSWORD_DEFAULT), $result['COMPANY_ID']);
            }else if($this->w == 'cc'){
                $params = array(password_hash($pass, PASSWORD_DEFAULT), $result['CONSULTANT_ID']);
            }else{
                $params = array(password_hash($pass, PASSWORD_DEFAULT), $result['SMME_ID']);
            }
            if($this->w == 'c'){
                $query=$this->master->update('', UPDATE_TOKEN_C[0], UPDATE_TOKEN_C[1], $params);
            }else if($this->w == 'cc'){
                $query=$this->master->update('', UPDATE_TOKEN_CC[0], UPDATE_TOKEN_CC[1], $params);
            }else{
                $query=$this->master->update('', UPDATE_TOKEN[0], UPDATE_TOKEN[1], $params);
            }
            
            if(!$query){
                header("location: Password_R.php?s=$selector&v=$validator&error=DatabaseError");
                exit();
            }
            $params = array($selector);
            
                $query=$this->master->delete('', DELETE_TOKEN[0], DELETE_TOKEN[1], $params);
            
            if(!$query){
                header("location: Password_R.php?s=$selector&v=$validator&error=DatabaseError");
                exit();
            }else{
                if($this->w == 'c'){
                    header("location: ../COMPANY/login.php?error=passwordsuccess");
                exit();
                }else if($this->w == 'cc'){
                    header("location: ../CONSULTANT/login.php?error=passwordsuccess");
                exit();
                }else{
                    header("location: ../SMME/login.php?error=passwordsuccess");
                    exit();
                }
            }
        }
      }
}
}

public function checkFromLogin($code){
    if($code == "12345"){
        return TRUE;
    }else{
        return FALSE;
    }
}
public function getFromLogin(){
    $filepath = realpath(dirname(__FILE__, 2));
    echo "<h1>Enter your email address</h1>
    <form action='main/Main_password_recovery.php?w=$this->w' method='Post'>
    <table>
    <tr>
    <td><input type='email' name='email' required></td>
    </tr>
    </table>
    <input type='submit' name='mail'>
    </form>";
}

public function checkFromEmail($selector, $validator){
    $params = array($selector, $validator);
    // print_r($params);
    // exit();
    if($this->w == 'c'){
        $query=$this->master->select('', CHECK_EXISTS_SELECT_C[0], CHECK_EXISTS_SELECT_C[1], $params);
    }else if($this->w == 'cc'){
        $query=$this->master->select('', CHECK_EXISTS_SELECT_CC[0], CHECK_EXISTS_SELECT_CC[1], $params);
    }else{
        $query=$this->master->select('', CHECK_EXISTS_SELECT[0], CHECK_EXISTS_SELECT[1], $params);
    }
    
    if(!$query){
        return FALSE;
    }else{
      $result=$this->master->getResult();
     
      if(empty($result)){
        return FALSE;
      }else{
        return TRUE;
      }
    }
}

public function getFromEmail(){
    $filepath = realpath(dirname(__FILE__,2));
    
    echo "<h1>Enter new password</h1>
    <form action='$filepath/main/Main_password_recovery.php?w=$this->w' method=Post>
    <table>
    <tr>
    <td><input type='password' name='password' required></td>
    <td><input type='password' name='passwordR' required></td>
    </tr>
    </table>
    <input type='submit' name='pass'>
    </form>";
}
}