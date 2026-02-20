 <?php
 
use PHPMailer\PHPMailer\Exception;

require 'mail.extend.php';
require 'mail_body.class.php';
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../helpers/val.php');
include_once($filepath.'/../helpers/token.php');
include_once($filepath.'/../view/admin_view/view.php');
include_once($filepath.'/../view/admin_view/adminReview.php');
include_once($filepath.'/../view/analytics_view/analytics_view.php');
include_once($filepath.'/../view/Review/companyReview.php');
include_once($filepath.'/../view/Review/consultantReview.php');
include_once($filepath.'/../view/Review/smmeReview.php');
include_once($filepath.'/../view/Edit/companyEdit.php');
include_once($filepath.'/../view/Edit/smmeEdit.php');
include_once($filepath.'/../view/Edit/consultantEdit.php');
include_once($filepath.'/../view/Delete/companyDelete.php');
include_once('notification_body.class.php');

define("FILEPATH", $filepath);

abstract class Generic{
  protected $master;
  function __construct(){
    $this->master=new Master($this->var);
  }
  public function SignUp($sname, $surname, $username, $email, $password, $passwordRepeat, $terms_policies, $returnurl=null){
    $check = array($sname, $surname, $username, $email, $password, $passwordRepeat);
    val::checkempty($check);
    val::checkemailusername($email, $username);
    $select = array($username);
    $selectsql =$this->SIGNUP_SELECT[0];
    $selecttypes =$this->SIGNUP_SELECT[1];
    $selectAndInsertTable ="signup";
    $query=$this->master->selectnonquery($selectAndInsertTable, $selectsql, $selecttypes, $select);
    $xi=$this->master->numRows();
if($xi!==0 && $query){
      header("location: ../".$this->classname."/login.php?error=usernametaken&res=".$xi);
      exit();
    }elseif(!$query){
      header("location: ../".$this->classname."/login.php?error=databaseError1");
      exit();
    }else{
        
      val::checkpasswords($password, $passwordRepeat, $this->classname);
      
      $insertsql=$this->SIGNUP_INSERT[0];
      $inserttypes=$this->SIGNUP_INSERT[1];
      $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
      $insert = array($sname, $surname, $username, $email, $hashedPwd, $terms_policies);
      $query=$this->master->insert($selectAndInsertTable, $insertsql, $inserttypes, $insert);
      if(!$query){
        header("location: ../".$this->classname."/login.php?error=databaseError2");
        exit();
      }else{
        $this->defaultProfile($username);
        // email verification
        $body = new mail_body;
        //send mail
        try {
          //Instantiate your new class, making use of the new `$body` parameter 
          $mail = new Mailer(true);
          $linkInsertsql=$this->EMAIL_VERIFICATION_INSERT[0];
          $linkInserttypes=$this->EMAIL_VERIFICATION_INSERT[1];
          $link = bin2hex(random_bytes(32));
          $verification = array($link, $email);
          $EVENT_ID = 50;
          $query=$this->master->insert("email_verification", $linkInsertsql, $linkInserttypes, $verification);
          $mail->send_single($email, $sname, $body->subject($EVENT_ID, $sname), $body->verifyaccount($link, $sname, $surname, $this->classname,$email));
        if(!$mail){
          header("location: ../".$this->classname."/login.php?email");
        exit();
        }
        }catch (Exception $e) {
            //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
            echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
        }
        //send email here


        if(is_numeric($returnurl) && $returnurl !== ""){
          //taking the url query parameters on the url and adding them to the next url (without the return url)
          $queries = array();
          parse_str($_SERVER['QUERY_STRING'], $queries);
          unset($queries["r"]);
          unset($queries["error"]);
          unset($queries["e"]);
      
      if(empty($queries)){
      $realurl = "location: ../".$this->classname."/login.php?error=emailsent&r=".$returnurl;
      }else{
        $querystring = http_build_query($queries);
          $realurl = "location: ../".$this->classname."/login.php?error=emailsent&r=".$returnurl."&".$querystring;
      }
      header($realurl);
      exit();
  }else{
    header("location: ../".$this->classname."/login.php?error=emailsent");
  }
      }
    }
}
public function AdminSignUp($sname, $surname, $username, $email, $password, $passwordRepeat, $terms_policies,$role, $city, $province,$industry, $returnurl=null){
  $check = array($sname, $surname, $username, $email, $password, $passwordRepeat, $role, $city, $province, $industry);
  val::checkempty($check);
  val::checkemailusername($email, $username);
  $select = array($username);
  $selectsql =$this->SIGNUP_SELECT[0];
  $selecttypes =$this->SIGNUP_SELECT[1];
  $selectAndInsertTable ="signup";
  $query=$this->master->selectnonquery($selectAndInsertTable, $selectsql, $selecttypes, $select);
  $xi=$this->master->numRows();
  if($xi!==0 && $query){
    header("location: ../".$this->classname."/login.php?error=usernametaken= ".$xi);
    exit();
  }elseif(!$query){
    header("location: ../".$this->classname."/login.php?error=databaseError1");
    exit();
  }else{
    val::checkpasswords($password, $passwordRepeat, $this->classname);
    $insertsql=$this->SIGNUP_INSERT[0];
    $inserttypes=$this->SIGNUP_INSERT[1];
    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    $insert = array($sname, $surname, $username, $email, $hashedPwd, $terms_policies, $role, $city, $province, $industry);
    $query=$this->master->insert($selectAndInsertTable, $insertsql, $inserttypes, $insert);
    $id = $this->master->getLastID();
    if(!$query){
      header("location: ../".$this->classname."/login.php?error=databaseError2");
      exit();
    }else{
      $this->defaultProfile($username);
      //insert industry into admin_sector table
      
      $insertsql=$this->SECTOR_INSERT[0];
      $inserttypes=$this->SECTOR_INSERT[1];
      $insert = array($id, $industry);
      $query=$this->master->insert($selectAndInsertTable, $insertsql, $inserttypes, $insert);
      // email verification
      $body = new mail_body;
      //send mail
      try {
        //Instantiate your new class, making use of the new `$body` parameter 
        $mail = new Mailer(true);
        $linkInsertsql=$this->EMAIL_VERIFICATION_INSERT[0];
        $linkInserttypes=$this->EMAIL_VERIFICATION_INSERT[1];
        $link = bin2hex(random_bytes(32));
        $verification = array($link, $email);
        $EVENT_ID = 50;
        $query=$this->master->insert("email_verification", $linkInsertsql, $linkInserttypes, $verification);
        $mail->send_single($email, $sname, $body->subject($EVENT_ID, $sname), $body->verifyaccount($link, $sname, $surname, $this->classname,$email));
      if(!$mail){
        header("location: ../".$this->classname."/login.php?email");
      exit();
      }
      }catch (Exception $e) {
          //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
          echo 'Caught a ' . get_class($e) . ': ' . $e->getMessage();
      }
      //send email here


      if(is_numeric($returnurl) && $returnurl !== ""){
        //taking the url query parameters on the url and adding them to the next url (without the return url)
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        unset($queries["r"]);
        unset($queries["error"]);
        unset($queries["e"]);
    
    if(empty($queries)){
    $realurl = "location: ../".$this->classname."/login.php?r=".$returnurl;
    }else{
      $querystring = http_build_query($queries);
        $realurl = "location: ../".$this->classname."/login.php?r=".$returnurl."&".$querystring;
    }
    header($realurl);
    exit();
}else{
  header("location: ../".$this->classname."/index.php?result=success");
}
    }
  }
}

public function verify_account($email, $link){
  
  $query=$this->master->select("email_verification", $this->VERIFY_ACCOUNT_SELECT[0], $this->VERIFY_ACCOUNT_SELECT[1], array($link, $email));
  if(!$query){
    print_r($this->VERIFY_ACCOUNT_SELECT[0]);
    exit();
  }else{
    $result = $this->master->getResult();

    if($result[1] == 1){
      $query=$this->master->update("email_verification", $this->VERIFY_ACCOUNT_UPDATE[0], $this->VERIFY_ACCOUNT_UPDATE[1], array($email));
      if(!$query){
        print_r(-2);
        exit();
      }else{
    
          print_r(1);
          exit();
        
      }
    }
  }

}



  //login function
  public function Login($userName, $password, $returnurl){
    // print_r($userName);
    // echo "</br>";
    // print_r($password);
    // exit();

    val::checkempty(array($userName,$password));
   if($this->classname == "COMPANY" || $this->classname == "SMME" || $this->classname == "CONSULTANT"){
    $query=$this->master->select("signup", $this->LOGIN_SELECT[0], $this->LOGIN_SELECT[1], array($userName, $this->classname));
   
   }else{
    $query=$this->master->select("signup", $this->LOGIN_SELECT[0], $this->LOGIN_SELECT[1], array($userName));
   } 
    if(!$query) {
      header("location: ../".$this->classname."/login.php?error=databaserror");
      exit();
    }
      $result=$this->master->getResult();
      $xi=$this->master->numRows();

      if($xi==0){
        if(strcmp($this->classname, "ADMIN")==0){
          header("location: ../ADMIN/login.php?error=InvalidUserNameOrPassword1");
          
        }else{
          header("location: ../".$this->classname."/login.php?error=InvalidUserNameOrPassword4");
        }
        exit();
      }
      if (strcmp($this->classname, "ADMIN") === 0) {

            $pwdcheck = password_verify($password, $result['password']);
        
        } else {
        
            $pwdcheck = password_verify($password, $result['password']);
        
        }
      if ($pwdcheck == false){
        if(strcmp($this->classname, "ADMIN")==0){
          
          header("location: ../ADMIN/login.php?error=InvalidUserNameOrPassword2");
        }else{

          header("location: ../".$this->classname."/login.php?error=InvalidUserNameOrPassword5");
        }
        exit();
      }
    elseif($pwdcheck == true) {
        if($this->classname == "ADMIN"){
          Session::set("WHO", "ADMIN");
          Session::set($this->id, $result["id"]);
          $array =$this->pimg($result["id"]);
              Session::set('Name',$result['firstname']);
              if($result['avatar']!=null){
                Session::set('ext', $array['avatar']);
              }
              token::create_session_key();
        }else{
          if( $result['verified'] ==1){
        Session::init();
              $who = $result['typeOfEntity'];
              Session::set("WHO", $who);
            
              Session::set($this->id, $result[$this->id]);
              $array =$this->pimg($result[$this->id]);
              Session::set('Name',$result['First_Name']);
              if($array['ext']!==null){
                Session::set('ext', $array['ext']);
              }
              token::create_session_key();
        }else{
        header("location: ../".$this->classname."/login.php?error=InvalidUserNameOrPasswordt");
      }
      }
      if(is_numeric($returnurl) && $returnurl !== ""){
            //taking the url query parameters on the url and adding them to the next url (without the return url)
            $queries = array();
            parse_str($_SERVER['QUERY_STRING'], $queries);
            unset($queries["r"]);
            unset($queries["error"]);
            unset($queries["e"]);
            if(strpos($this->classname, "ADMIN")){
              require "../ADMIN/dictionary/dictionary.php";
            }else{
              require "../".$this->classname."/dictionary/dictionary.php";
                }
              
        $url = dictionary($returnurl);
        
        if(empty($queries)){
          
          if(strpos($this->classname, "ADMIN")){
          
          
            $realurl = "location: ../ADMIN/".$url."?id=".Session::get($this->id);
          }else{
            $realurl = "location: ../".$this->classname."/".$url."?id=".Session::get($this->id);
          }
        }else{
          $querystring = http_build_query($queries);
          if(strpos($this->classname, "ADMIN")){
            $realurl = "location: ../ADMIN/".$url."?".$querystring."&id=".Session::get($this->id);
          }else{
            $realurl = "location: ../".$this->classname."/".$url."?".$querystring."&id=".Session::get($this->id);
          }
        }
        header($realurl);
        exit();
    
    }else{
      
      if(strpos($this->classname, "ADMIN")){
      
        header("location: ../ADMIN/index.php?r_is=".$returnurl);
      }else{
        header("location: ../".$this->classname."/index.php?r_is=".$returnurl);
      }
    
      exit();
    }
      
      
    
  } else {
  header("location: ../index.php?error");
  exit();
}
}


// public function AdminLogin($userid) {
//     // Ensure the userid is provided
//     if (empty($userid)) {
//         header("location: ../".$this->classname."/login.php?error=useridmissing");
//         exit();
//     }

//     // Fetch user data based on userid
//     $query = $this->master->select("signup", $this->LOGIN_SELECT[0], $this->LOGIN_SELECT[1], array($userid), 'id');

//     // If query fails, redirect to the login page with a database error
//     if (!$query) {
//         header("location: ../".$this->classname."/login.php?error=databaserror");
//         exit();
//     }

//     // Fetch the result and check if the user exists
//     $result = $this->master->getResult();
//     $xi = $this->master->numRows();

//     if ($xi == 0) {
//         $error = (strcmp($this->classname, "ADMIN") == 0) ? "InvalidUserNameOrPassword1" : "InvalidUserNameOrPassword4";
//         header("location: ../".$this->classname."/login.php?error=$error");
//         exit();
//     }

//     // Check if the user is verified
//     if ($result['verified'] != 1) {
//         header("location: ../".$this->classname."/login.php?error=InvalidUserNameOrPassword");
//         exit();
//     }

//     // Handle successful login
//     if ($this->classname == "ADMIN") {
//         Session::set("WHO", "ADMIN");
//         Session::set($this->id, $result["id"]);
//         $array = $this->pimg($result["id"]);
//         Session::set('Name', $result['firstname']);
//         if ($result['avatar'] != null) {
//             Session::set('ext', $array['avatar']);
//         }
//         token::create_session_key();
//     } else {
//         Session::init();
//         $who = $result['typeOfEntity'];
//         Session::set("WHO", $who);
//         Session::set($this->id, $result[$this->id]);
//         $array = $this->pimg($result[$this->id]);
//         Session::set('Name', $result['First_Name']);
//         if ($array['ext'] !== null) {
//             Session::set('ext', $array['ext']);
//         }
//         token::create_session_key();
//     }

//     // Redirect to the provided user id page
//     header("location: ../".$this->classname."/job_order_infor .php?id=" . htmlspecialchars($userid));
//     exit();
// }






  function defaultProfile($username){
    $select=array($username);
    $selectsql =  $this->DEFAULTPROFILE_SELECT[0];
    $selecttypes = $this->DEFAULTPROFILE_SELECT[1];
    $selectTable ="signup";
    $query=$this->master->select($selectTable, $selectsql, $selecttypes, $select);
    $result=$this->master->getResult();
    $SMMEID=$result[$this->id];
    $insert=array($SMMEID);
    $insertTable='pimg';
    $insertsql =  $this->DEFAULTPROFILE_INSERT[0];
    $inserttypes = $this->DEFAULTPROFILE_INSERT[1];
    $query=$this->master->insert($insertTable, $insertsql, $inserttypes, $insert);
  }
  //register the consultant information
  public function consultant_register($race, $idtype, $idnumber, $gender){
  $register_values = array(
    $race, 
    $idtype, 
    $idnumber, 
    $gender,
    session::get($this->id)
  );
    //val::checkempty($register_values);
    $select = array(session::get($this->id));
    $insertSelectTableRegister = "consultant_information";
    $regInsertSQL = $this->REGISTER_SELECT[0];
    $regInsertTypes = $this->REGISTER_SELECT[1];
    $query = $this->master->selectnonquery($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $select);
    $xi=$this->master->numRows();
    if(!$xi==0 && $query){
      header("location: ../index.php?error=alreadyuploaded");
      exit();
    }
    elseif(!$query){
    
     header("location: ../index.php?error=databaseErrorTHIS1");
      exit();
    }else{
      $regInsertSQL = $this->REGISTER_INSERT[0];
      $regInsertTypes = $this->REGISTER_INSERT[1];
      $query = $this->master->insert($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $register_values);
      if(!$query){
    //      echo "SQl => ".$regInsertSQL;
    //  echo "<br> Types => ".$regInsertTypes;
    //  echo "<br> ID => ".$register_values;
        header("location: ../index.php?error=databaseErrorINSERT");
        exit();
      }
      header("location: ../".$this->classname."/index.php?result=success");
      exit();
    }
  }

  public function register( $tradename,$name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email,$foo, $offices,$industry, $financial){
    $register_values = array(
      $tradename,
      $name,
      $RegNum,
      $Address,
      $Postal,
      $City,
      $Province,
      $Contact,
      $email,
      $foo,
      $offices,
      $industry,
      $financial,
      session::get($this->id)
    );
      val::checkempty($register_values);
      $select = array(session::get($this->id));
      $insertSelectTableRegister = "register";
      $regInsertSQL = $this->REGISTER_SELECT[0];
      $regInsertTypes = $this->REGISTER_SELECT[1];
      

      $query = $this->master->selectnonquery($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $select);
      $xi=$this->master->numRows();

      $query=$this->master->select("register", $this->SMME_ACTIVE_UPDATE[0], $this->SMME_ACTIVE_UPDATE[1], array(session::get($this->id)));

      $result=$this->master->getResult();


       if(!$xi==0 && $result['Active'] == 1){


        $query=$this->master->update("register", $this->REGISTER_SMME_UPDATE[0],$this->REGISTER_SMME_UPDATE[1], $register_values);

        header("location: ../".$this->classname."/company_info.php?result=success");
        exit();
      
      }else if (!$xi==0 && $result['Active'] == 0) { 


        header("location: ../".$this->classname."/company_info.php?result=exists");
        exit();
        
        
      }
      else if ($xi==0 && $result['Active'] == 0) {

        $regInsertSQL = $this->REGISTER_INSERT[0];
        $regInsertTypes = $this->REGISTER_INSERT[1];
        // print_r($regInsertSQL);
        // echo "</br>";
        // print_r($regInsertTypes);
        // echo "</br>";
        // print_r($register_values);
        // exit();
        $query = $this->master->insert($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $register_values);
        if(!$query){
          header("location: ../index.php?error=databaseError");
          exit();
        }
        if($this->classname == "COMPANY"){
          header("location: ../".$this->classname."/keywords.php?result=success");
          exit();
        }else{
          header("location: ../".$this->classname."/company_dir.php?result=success");
          exit();
        }



      }
    }


public function deleteproduct($action){


  $sql = $this->PRODUCT_DELETE[0];
  $types = $this->PRODUCT_DELETE[1];
  $params = array($action,session::get($this->id));
  $query = $this->master->update("products",$sql, $types, $params);

  if(!$query){
    header("location: ../".$this->classname."/index.php?error=databaseError2");
    exit();
  }else{
    
      header("location: ../".$this->classname."/edit.php?result=deleted");
      exit();
    
  }
  echo $action;


}

    public function registerCompany( $tradename,$name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email,$foo, $industry, $financial){
      $register_values = array(
        $tradename,
        $name,
        $RegNum,
        $Address,
        $Postal,
        $City,
        $Province,
        $Contact,
        $email,
        $foo,
        $industry,
        $financial,
        session::get($this->id)
      );
        val::checkempty($register_values);
        $select = array(session::get($this->id));
        $insertSelectTableRegister = "register";
        $regInsertSQL = $this->REGISTER_SELECT[0];
        $regInsertTypes = $this->REGISTER_SELECT[1];
        
  
        $query = $this->master->selectnonquery($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $select);
        $xi=$this->master->numRows();
  
        $query=$this->master->select("register", $this->COMPANYREG_ACTIVE[0], $this->COMPANYREG_ACTIVE[1], array(session::get($this->id)));
  
        $result=$this->master->getResult();
  
  
         if(!$xi==0 && $result['Active'] == 1){
  
  
          $query=$this->master->update("register", $this->REGISTER_UPDATE[0],$this->REGISTER_UPDATE[1], $register_values);
  
          header("location: ../".$this->classname."/company_info.php?result=success");
          exit();
        
        }else if (!$xi==0 && $result['Active'] == 0) { 
  
  
          header("location: ../".$this->classname."/company_info.php?result=exists");
          exit();
          
          
        }
        else if ($xi==0 && $result['Active'] == 0) {
  
          $regInsertSQL = $this->REGISTER_INSERT[0];
          $regInsertTypes = $this->REGISTER_INSERT[1];
          // print_r($regInsertSQL);
          // echo "</br>";
          // print_r($regInsertTypes);
          // echo "</br>";
          // print_r($register_values);
          // exit();
          $query = $this->master->insert($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $register_values);
          if(!$query){
            header("location: ../index.php?error=databaseError");
            exit();
          }
          if($this->classname == "COMPANY"){
            header("location: ../".$this->classname."/company_statement.php?result=success");
            exit();
          }else{
            header("location: ../".$this->classname."/company_dir.php?result=success");
            exit();
          }
  
  
  
        }
      }


  public function adminUpdate($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
    
    $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
    
    val::checkempty($check);
    array_push($check, session::get($this->id));
    $query=$this->master->update("admin", $this->ADMIN_UPDATE[0],$this->ADMIN_UPDATE[1], $check);
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=adminupdated");
          exit();
        
      }

  }
  public function ConsultantadminUpdate($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
    $id = session::get($this->id);
    $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
    $signup = array($name, $surname, $email,$id);
    $info = array($IdType, $IDNumber, $Gender, $EthnicGroup,$id);
   
    // exit();
    val::checkempty($check);
    array_push($check, session::get($this->id));
    $query=$this->master->transactionUpdate(array("signup","consultant_information"), array($this->ADMIN_UPDATE[0],$this->SIGNUP_ADMIN_UPDATE[0]),array($this->ADMIN_UPDATE[1],$this->SIGNUP_ADMIN_UPDATE[1]), array($info,$signup));
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=adminupdated");
          exit();
        
      }

  }

  public function adminSMMEUpdate($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
    $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
    val::checkempty($check);
    array_push($check, session::get($this->id));
    $query=$this->master->update("admin", $this->ADMIN_SMME_UPDATE[0],$this->ADMIN_SMME_UPDATE[1], $check);
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=success");
          exit();
        
      }

  }

  public function REGISTERSMMEUpdate($tradename,$name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email,$foo, $industry, $financial){
    $check = array($tradename,$name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email,$foo, $industry, $financial);

    val::checkempty($check);
    array_push($check, session::get($this->id));
    // print_r($this->REGISTER_SMME_UPDATE[0]); echo count($this->REGISTER_SMME_UPDATE);
    // echo"<br>";
    // print_r($this->REGISTER_SMME_UPDATE[1]); echo  count($this->REGISTER_SMME_UPDATE);
    // echo"<br>";
    // print_r($check); echo  count($check);
    // exit();
    $query=$this->master->update("register", $this->REGISTER_SMME_UPDATE[0],$this->REGISTER_SMME_UPDATE[1], $check);
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=success");
          exit();
        
      }

  }

  


  public function SMMEStatementUPDATE($introduction, $vision, $mission, $values, $goals_objectives){
    $check = array($introduction, $vision, $mission, $values, $goals_objectives);
   
    val::checkempty($check);
    array_push($check, session::get($this->id));

    $query=$this->master->update("company_profile", $this->STATEMENT_SMME_UPDATE[0],$this->STATEMENT_SMME_UPDATE[1], $check);

      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=success");
          exit();
        
      }

  }

  public function COMPANYStatementUPDATE($introduction, $vision, $mission, $values, $goals_objectives){
    $check = array($introduction, $vision, $mission, $values, $goals_objectives);
   
    val::checkempty($check);
    array_push($check, session::get($this->id));

    $query=$this->master->update("company_profile", $this->STATEMENT_COMPANY[0],$this->STATEMENT_COMPANY[1], $check);

      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=success");
          exit();
        
      }

  }


  public function SMMESDocUPDATE($Number_Shareholders, $Number_Black_Shareholders, $Number_White_Shareholders, $Black_Ownership_Percentage, $Black_Female_Percentage, $White_Ownership_percentage, $BBBEE_Status, $Date_Of_Issue, $Expiry_Date){
    $check = array($Number_Shareholders, $Number_Black_Shareholders, $Number_White_Shareholders, $Black_Ownership_Percentage, $Black_Female_Percentage, $White_Ownership_percentage, $BBBEE_Status, $Date_Of_Issue, $Expiry_Date);
    
    array_push($check, session::get($this->id));

    $query=$this->master->update("company_documentation", $this->DOCUMENT_SMME_UPDATE[0],$this->DOCUMENT_SMME_UPDATE[1], $check);

      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/edit.php?result=success");
          exit();
        
      }

  }

  public function  SMMEDIRECTORUPDATE($name, $surname, $IdType,$IDNumber, $Gender, $EthnicGroup, $fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg){
    $copy = $this->UploadFile("IDcopy",$fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg);
      if($id=session::get($this->id)){
        $sql=$this->DIRECTOR_SMME_UPDATE[0];
        $array=array();
        $types="";
        for ($a = 0; $a < count($name); $a++)
        {
          if($a!==(count($name)-1)){
            $sql.="(?, ?, ?, ?, ?, ?, ?), ";
          }else{
            $sql.="(?, ?, ?, ?, ?, ?, ?);";
          }
      
          $types.="sssissi";
          array_push($array, $name[$a], $surname[$a], $IdType[$a], $IDNumber[$a], $Gender[$a], $EthnicGroup[$a], $id);
        }
      $firstDoc = array("ID COPY", $copy, session::get($this->id));
      $insertTables = array("company_director","file_uploads");
      $insertsql = array($sql, $this->UPLOADFILE_INSERT[0]);
      $inserttypes = array($types, $this->UPLOADFILE_INSERT[1]);
      $documents = array($array, $firstDoc);
      
     
      $query = $this->master->transactionInsert($insertTables, $insertsql, $inserttypes, $documents);
        
        if(!$query){
          
          exit();
         }else{
          header("location: ../".$this->classname."/company_statement.php?result=success");
          exit();
         }
    
    
      }else{
        header("location: ../index.php?error=invalidCredentials");
        exit();
    }
  }


  public function admin($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
    $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
    val::checkempty($check);
    $query=$this->master->selectnonquery("admin", $this->ADMIN_SELECT[0], $this->ADMIN_SELECT[1], array(session::get($this->id)));
    $xi=$this->master->numRows();
    $query=$this->master->select("admin", $this->ADMIN_SELECT_ACTIVE[0], $this->ADMIN_SELECT_ACTIVE[1], array(session::get($this->id)));
    $result=$this->master->getResult();

    $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));

    if(!$xi==0 && $result['Active'] == 1){

      $query=$this->master->update("admin", $this->ADMIN_SMME_UPDATE[0],$this->ADMIN_SMME_UPDATE[1], $insert);

      header("location: ../".$this->classname."/admin_info.php?result=success");
      exit();

    }
    elseif (!$xi==0 && $result['Active'] == 0){

      header("location: ../".$this->classname."/admin_info.php?result=exists");
      exit();

      $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));
      $query=$this->master->insert("admin", $this->ADMIN_INSERT[0],$this->ADMIN_INSERT[1], $insert);
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/company_info.php?result=success");
          exit();
        
      }
    }

    elseif ($xi==0 && $result['Active'] == 0){


      $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));
      $query=$this->master->insert("admin", $this->ADMIN_INSERT[0],$this->ADMIN_INSERT[1], $insert);
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
      }else{
        
          header("location: ../".$this->classname."/company_info.php?result=success");
          exit();
        
      }
    }
}

public function adminCompany($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
  $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
  val::checkempty($check);
  $query=$this->master->selectnonquery("admin", $this->ADMIN_SELECT[0], $this->ADMIN_SELECT[1], array(session::get($this->id)));
  $xi=$this->master->numRows();
  $query=$this->master->select("admin", $this->COMPANY_ACTIVE[0], $this->COMPANY_ACTIVE[1], array(session::get($this->id)));
  $result=$this->master->getResult();

  $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));

  if(!$xi==0 && $result['Active'] == 1){


    $query=$this->master->update("admin", $this->ADMIN_UPDATE[0],$this->ADMIN_UPDATE[1], $insert);

    header("location: ../".$this->classname."/admin_info.php?result=success");
    exit();

  }
  elseif (!$xi==0 && $result['Active'] == 0){

    header("location: ../".$this->classname."/admin_info.php?result=exists");
    exit();

    $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));
    $query=$this->master->insert("admin", $this->ADMIN_INSERT[0],$this->ADMIN_INSERT[1], $insert);
   
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/company_info.php?result=success");
        exit();
      
    }
  }

  elseif ($xi==0 && $result['Active'] == 0){


    $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));
    $query=$this->master->insert("admin", $this->ADMIN_INSERT[0],$this->ADMIN_INSERT[1], $insert);
   
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/company_info.php?result=success");
        exit();
      
    }
  }
}



  function addCompanyStatements($introduction, $vision, $mission, $values, $goals_objectives){
    $statements = array($introduction,$vision,$mission,$values,$goals_objectives);
    val::checkempty($statements);
    array_push($statements, session::get($this->id));
    $insertTable = "company_profile";

    $query=$this->master->select("company_profile", $this->ADDCOMPANYSTATEMENTS_SELECT[0], $this->ADDCOMPANYSTATEMENTS_SELECT[1], array(session::get($this->id)));
    $xi=$this->master->numRows();
    $result=$this->master->getResult();
    
    if(!$xi==0 && $result['Active']==1){
      
      $query=$this->master->update("company_profile", $this->STATEMENT_SMME_UPDATE[0],$this->STATEMENT_SMME_UPDATE[1], $statements);

      header("location: ../".$this->classname."/company_statement.php?result=success");
     
    } else if (!$xi==0 && $result['Active'] == 0){


      header("location: ../".$this->classname."/company_statement.php?result=exists");
      exit();

      
    }

    else if ($xi==0 && $result['Active'] == 0) {

      $insertsql = $this->ADDCOMPANYSTATEMENTS_INSERT[0];
      $inserttypes = $this->ADDCOMPANYSTATEMENTS_INSERT[1];
      $query = $this->master->insert($insertTable, $insertsql, $inserttypes, $statements);
      $xi=$this->master->numRows();
      if(!$query){
        header("location: ../".$this->classname."/company_statement.php?result=exists");
        exit();
      }else{
        
        header("location: ../".$this->classname."/company_documentation.php?result=success");
            exit();
          }

    }
    
  }


  function addCompanyStatementsCompany($introduction, $vision, $mission, $values, $goals_objectives){
    $statements = array($introduction,$vision,$mission,$values,$goals_objectives);
    val::checkempty($statements);
    array_push($statements, session::get($this->id));
    $insertTable = "company_profile";

    $query=$this->master->select("company_profile", $this->ADDCOMPANYSTATEMENTS_SELECT[0], $this->ADDCOMPANYSTATEMENTS_SELECT[1], array(session::get($this->id)));
    $xi=$this->master->numRows();
    $result=$this->master->getResult();

    
    if(!$xi==0 && $result['Active']==1){


      
      $query=$this->master->update("company_profile", $this->STATEMENT_COMPANY[0],$this->STATEMENT_COMPANY[1], $statements);

      header("location: ../".$this->classname."/company_statement.php?result=success");
     
    } else if (!$xi==0 && $result['Active'] == 0){


     

      header("location: ../".$this->classname."/company_statement.php?result=exists");
      exit();

      
    }

    else if ($xi==0 && $result['Active'] == 0) {

      $insertsql = $this->ADDCOMPANYSTATEMENTS_INSERT[0];
      $inserttypes = $this->ADDCOMPANYSTATEMENTS_INSERT[1];
      $query = $this->master->insert($insertTable, $insertsql, $inserttypes, $statements);
      $xi=$this->master->numRows();
      if(!$query){
        header("location: ../".$this->classname."/company_statement.php?result=exists");
        exit();
      }else{
        
        header("location: ../".$this->classname."/products_services.php?result=success");
            exit();
          }

    }
    
  }


function addCompanyDocuments($Number_Shareholders, $Number_Black_Shareholders, $Number_White_Shareholders, $Black_Ownership_Percentage, $Black_Female_Percentage, $White_Ownership_percentage, $BBBEE_Status, $Date_Of_Issue, $Expiry_Date, $fileNamebbbee,$fileTmpNamebbbee,$fileSizebbbee,$fileErrorbbbee, $fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg){

  $certificate = $this->UploadFile("BBBEEcertificate",$fileNamebbbee,$fileTmpNamebbbee,$fileSizebbbee,$fileErrorbbbee);
  $registration = $this->UploadFile("COMPANYregistration",$fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg);
  
 
  
  $Documents = array(
      $Number_Shareholders,
      $Number_Black_Shareholders,
      $Number_White_Shareholders,
      $Black_Ownership_Percentage,//
      $Black_Female_Percentage, 
      $White_Ownership_percentage,//
      $BBBEE_Status,
      $Date_Of_Issue,
      $Expiry_Date,
    );

    
    val::checkempty($Documents);
    
    array_push($Documents, session::get($this->id));
    $idz= array(session::get($this->id));
    $firstDoc = array("BBBEE CERTIFICATE", $certificate, session::get($this->id));
    $secondDoc = array("REGISTRATION CERTIFICATE", $registration, session::get($this->id));
    $insertTables = array("file_uploads", "file_uploads", "company_documentation");
    $insertTables2 = array("file_uploads", "file_uploads");
    $insertsql = array($this->ADDCOMPANYDOCUMENTS_INSERT[0], $this->UPLOADFILE_INSERT[0], $this->UPLOADFILE_INSERT[0]);
    $inserttypes = array($this->ADDCOMPANYDOCUMENTS_INSERT[1], $this->UPLOADFILE_INSERT[1], $this->UPLOADFILE_INSERT[1]);
    $insertsql2 = array($this->UPLOADFILE_INSERT[0], $this->UPLOADFILE_INSERT[0]);
    $inserttypes2 = array( $this->UPLOADFILE_INSERT[1], $this->UPLOADFILE_INSERT[1]);
    $documents = array($Documents, $firstDoc, $secondDoc);
    $documents2 = array($idz, $firstDoc, $secondDoc);
    
   /*database transaction

   the code runs three insert statements via a databse transaction so it can insert the two documents and also the BBBEE information
   */

  $query=$this->master->select("company_documentation", $this->DOCUMENT_SELECT[0], $this->DOCUMENT_SELECT[1], array(session::get($this->id)));
  $result=$this->master->getResult();
  $xi=$this->master->numRows();


  if(!$xi==0 && $result["Active"]==1) {

  
    $query=$this->master->update("company_documentation", $this->DOCUMENT_SMME_UPDATE[0],$this->DOCUMENT_SMME_UPDATE[1], $Documents);
    header("location: ../".$this->classname."/company_documentation.php?result=success");
    exit();


  }elseif(!$xi==0 && $result["Active"]==0){

    header("location: ../".$this->classname."/company_documentation.php?result=exists1");
      exit();

  }
  elseif ($xi==0 && $result['Active'] == 0){

    $query = $this->master->transactionInsert($insertTables, $insertsql, $inserttypes, $documents);
    if(!$query){
      
      header("location: ../".$this->classname."/company_documentation.php?result=exists");
      exit();
    }else{
      header("location: ../".$this->classname."/expense_summary.php?result=success");
          exit();
        }
        

  }

  
      
    }




    private function temp($id, $ext, $filedelete){
        $cur=$this->pimg($id);
        if ($cur!=='error'){
        $PIMGEXT=$cur['ext'];
        
        if($PIMGEXT !== "https://openlinks.co.za/TIMS/Images/Profiles/profile_image.png"){
          if(file_exists($filedelete)){
            unlink($filedelete);
          }
        }           
      //   print_r($this->TEMP_UPDATE[0]);
      //   echo "</br>";
      //   print_r($this->TEMP_UPDATE[1]);
        
      //  print_r($this->classname);
      // exit();
      $query=$this->master->update("pimg", $this->TEMP_UPDATE[0], $this->TEMP_UPDATE[1], array($ext, $id));
      
      session::set('ext',$ext);
    }
  }

  public function pimg($id){
    $query=$this->master->select("pimg", $this->PIMG_SELECT[0], $this->PIMG_SELECT[1], array($id));
    if($query)
      return $this->master->getResult();
      return 'error';
  }

  private function fetchData(){
    $query=$this->master->select("pimg", $this->PASSWORD_SELECT[0], $this->PASSWORD_SELECT[1], array(session::get($this->id)));
    if($query)
      return $this->master->getResult();
      return 'error';
  }
  

  public function updatepwd($old, $new, $repeat){
    if(strcmp($new, $repeat) == 0){
      $result = $this->fetchData();
      $pwd = $result['Pwd'];
      $verified = password_verify($old, $pwd);
      $id = session::get($this->id);
      if($verified){
        $password = password_hash($new,PASSWORD_DEFAULT);
        $query=$this->master->update("signup", $this->PASSWORD_UPDATE[0], $this->PASSWORD_UPDATE[1], array($password, $id));
        if(!$query){
          header("location: ../".$this->classname."/edit.php?status=failed");
        }else{
          header("location: ../".$this->classname."/edit.php?status=successful");
        }
      }else{
        header("location: ../".$this->classname."/edit.php?status=incorrect");
      }
   
    }else{
      header("location: ../".$this->classname."/edit.php?status=dontmatch");
    }
   
  }

  public function UploadProfilePic($fileName,$fileTmpName,$fileSize,$fileError){
    //
    $id=session::get($this->id);
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg','jpeg','png');
    if(in_array($fileActualExt, $allowed)){
        if($fileError== 0){
            if($fileSize < 20000000){
                //$fileNameNew = $id.".".$fileActualExt;
                
                	
                $filedelete="https://openlinks.co.za/TIMS/Images/Profiles/_logo".token::encode1($fileName).token::encode1(session::get($this->id)).".".$fileActualExt;
                //$filedelete = token::encode1($fileName).token::encode1(session::get($this->id)).".".$fileActualExt;
                $fileNameNew = token::encode1($fileName).token::encode1(session::get($this->id)).".".$fileActualExt;
                //$fileDestination = 'http://localhost/BBBEE_Project/Project%20One/Images/Profiles/_logo'.$fileNameNew;
                $fileDestination = '../Images/Profiles/'.$fileNameNew;
                $this->temp($id, $fileDestination, $filedelete);
                move_uploaded_file($fileTmpName, $fileDestination);
               
                header("Location: ../".$this->classname."/edit.php?upload=successful");
                exit();
            }
            else{
                header("location: ../".$this->classname."/edit.php?error=YourFileIsTooBig");
                exit();
            }
        }
        else{
            header("location: ../".$this->classname."/edit.php?error=ThereWasAnErrorUploadingYourFile");
            exit();
        }
    }
    else{
        header("location: ../".$this->classname."/edit.php?error=YouCannotUploadThisTypeOfFile");
        exit();
    }

  }

  /*
  Uploadfile function takes in 6 parameters
  form -> this is the form to which the file was uploaded i.e. company documentation which will be used in the file name
  filename -> is the actual file name with the entire extention included
  fileTmpName ->
  filesize is the size of the file, only 2mb or smaller allowed 
  file error is for if the file has failed, this is 0 if not failed and 1 if failed
  file type is pdf or jpg  
  */
  protected function UploadFile($form,$fileName,$fileTmpName,$fileSize,$fileError){
      
      $fileExt = explode('.', $fileName);
      $fileActualExt = strtolower(end($fileExt));
      $allowed = array('jpg','jpeg','png','pdf');
      $images = array('jpg','jpeg','png');
      if(in_array($fileActualExt, $allowed)){
        
          if($fileError== 0){
            
              if($fileSize < 2000000){
                
                  $fileNameDelete = token::encode1($fileName).token::encode1(session::get($this->id))."_".$form.".".$fileActualExt;
                  
                  $fileNameNew = token::encode1($fileName).token::encode1(session::get($this->id))."_".$form.".".$fileActualExt;
                  
                  if(in_array($fileActualExt, $images)){
                    $fileDestination = '../STORAGE/IMAGES/'.$fileNameNew;
                    
                  }else{
                    $fileDestination = '../STORAGE/FILES/'.$fileNameNew;
                  }
                  
                  if(file_exists($fileNameDelete)){
                    unlink($fileNameDelete);
                  }
                  
                  move_uploaded_file($fileTmpName, $fileDestination);
                  
                  
                  return $fileNameNew;
              }
              else{
                  return "too large";
              }
          }
          else{
              return "file error" ;
          }
      }
      else{
          return "not right file";
      }
  }

public function registerCompanyData($legal_name, $trading_name, $registration_number, $financial_year){
  $Documents= array(
    $legal_name,
    $registration_number,
    $trading_name,
    $financial_year,
    session::get($this->id)
    );
    val::checkempty($Documents);

    $select = array(session::get($this->id));
    $insertDetailsTable ="company_data";
    $regInsertSQL= $this->REGISTERCOMPANYDATA_SELECT[0];
    $regInsertTypes = $this->REGISTERCOMPANYDATA_SELECT[1];
    $query = $this->master->selectnonquery($insertDetailsTable, $regInsertSQL, $regInsertTypes, $select);
    $xi=$this->master->numRows();
  if(!$xi==0 && $query){
    header("location: ../SMME/company_data.php?result=exists");
    exit();
  }else{
    $regInsertSQL = $this->REGISTERCOMPANYDATA_INSERT[0];
    $regInsertTypes = $this->REGISTERCOMPANYDATA_INSERT[1];
    $query = $this->master->insert($insertDetailsTable, $regInsertSQL, $regInsertTypes, $Documents);
    if(!$query){
      header("location: ../index.php?error=databaseError2");
    exit();
    }
    header("location: ../SMME/company_data.php?result=success");
    exit();

  }}

public function tempDirectors($name, $surname, $IdType,$IDNumber, $Gender, $EthnicGroup, $id){
  $Details= array(
    $name,
    $surname,
    $IdType,
    $IDNumber,
    $Gender,
    $EthnicGroup
  );
  val::checkempty($Details);
  $select = array($id);
  $regInsertSQL= $this->REGISTER_SELECT[0];
  $regInsertTypes = $this->REGISTER_SELECT[1];
  $insertDetails="......";
  $query = $this->master->selectnonquery($insertDetails, $regInsertSQL, $regInsertTypes, $select);
  if(!$query){
    header("location: ../index.php?error=databaseError");
    exit();
  }
  else{
    $regInsertSQL = $this->REGISTER_INSERT[0];
  $regInsertTypes = $this->REGISTER_INSERT[1];
  $query = $this->master->insert("company_director", $regInsertSQL, $regInsertTypes, $Details);
  }

}

function Directors($name, $surname, $IdType,$IDNumber, $Gender, $EthnicGroup, $fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg){
 
  $copy = $this->UploadFile("IDcopy",$fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg);
    if($id=session::get($this->id)){
      $sql=$this->DIRECTORS_INSERT[0];
      $array=array();
      $types="";
      for ($a = 0; $a < count($name); $a++)
      {
        if($a!==(count($name)-1)){
          $sql.="(?, ?, ?, ?, ?, ?, ?), ";
        }else{
          $sql.="(?, ?, ?, ?, ?, ?, ?);";
        }
    
        $types.="sssissi";
        array_push($array, $name[$a], $surname[$a], $IdType[$a], $IDNumber[$a], $Gender[$a], $EthnicGroup[$a], $id);
      }
    $firstDoc = array("ID COPY", $copy, session::get($this->id));
    $insertTables = array("company_director","file_uploads");
    $insertsql = array($sql, $this->UPLOADFILE_INSERT[0]);
    $inserttypes = array($types, $this->UPLOADFILE_INSERT[1]);
    $documents = array($array, $firstDoc);
    
    
    $query = $this->master->transactionInsert($insertTables, $insertsql, $inserttypes, $documents);
      
      if(!$query){
        
        exit();
       }else{
        header("location: ../".$this->classname."/company_statement.php?result=success");
        exit();
       }
  
  
    }else{
      header("location: ../index.php?error=invalidCredentials");
      exit();
  }
}


function expensesummary($serviceprovider, $productname, $productspecification, $randvalue, $frequency, $type){
  if($id=session::get($this->id)){
    $sql=$this->EXPENSESUMMARY_INSERT[0];
    $array=array();
    $types="";

    
    for ($a = 0; $a < count($serviceprovider); $a++)
    {
      
      if(!empty($serviceprovider[$a])){
        if($a!==(count($serviceprovider)-1) && !empty($serviceprovider[$a+1])){
          $sql.="(?,?,?,?,?,?,?), ";
        }else{
          $sql.="(?,?,?,?,?,?,?);";
        }
    
        $types.="sssiiii";
        array_push($array, $serviceprovider[$a], $productname[$a], $productspecification[$a], $randvalue[$a], $frequency[$a], $type, $id);
      }
      
      
    }
    
    
    
    $query=$this->master->insert("expense_summary", $sql, $types, $array);
    if(!$query){
      header("location: ../index.php?error=databaseError2");
      exit();
     }else{
      header("location: ../".$this->classname."/products_services.php?result=success");
      exit();
     }


  }else{
    header("location: ../".$this->classname."/Expense_summary.php?result=exists");
    exit();
}
}

function keywords($key_words){
  $key_words = explode(",", $key_words);
// print_r($key_words);
// exit();
  if($id=session::get($this->id)){
    $sql=$this->KEYWORD_INSERT[0];
    $array=array();
    $types="";
    for ($a = 0; $a < count($key_words); $a++)
    {
  if($a!==(count($key_words)-1)){
        $sql.="(?,?), ";
      }else{
        $sql.="(?,?);";
      }
  
      $types.="is";
      
      array_push($array, $id, $key_words[$a]);
    } 

    $query=$this->master->insert("keywords", $sql, $types, $array);

    if(!$query){
      echo "insert('keywords', $sql, $types, ".print_r($array).")";
      exit();
     }else{
        header("location: ../".$this->classname."/business_links.php?result=success");
        exit();
     }


  }else{
    header("location: ../index.php?result=exists");
    exit();
}

}

function smmekeywords($key_words){
  $key_words = explode(",", $key_words);
// print_r($key_words);
// exit();
  if($id=session::get($this->id)){
    $sql=$this->KEYWORD_INSERT[0];
    $array=array();
    $types="";
    for ($a = 0; $a < count($key_words); $a++)
    {
  if($a!==(count($key_words)-1)){
        $sql.="(?,?), ";
      }else{
        $sql.="(?,?);";
      }
  
      $types.="is";
      
      array_push($array, $id, $key_words[$a]);
    } 

    $query=$this->master->insert("keywords", $sql, $types, $array);

    if(!$query){
      echo "insert('keywords', $sql, $types, ".print_r($array).")";
      exit();
     }else{
        header("location: ../".$this->classname."/links.php?result=success");
        exit();
     }


  }else{
    header("location: ../index.php?result=exists");
    exit();
}

}



function products_services($productname,$productdes,$price, $fileName,$fileTmpName,$fileSize,$fileError ){

  if($id=session::get($this->id)){
    $sql=$this->PRODUCTS_INSERT[0];
    $array=array();
    $types="";
    for ($a = 0; $a < count($productname); $a++)
    {
  if($a!==(count($productname)-1)){
        $sql.="(?,?,?,?,?), ";
      }else{
        $sql.="(?,?,?,?,?);";
      }
  
      $types.="ssisi";
      $errors = array('too large', "file error", "not right file");
      
      $location = $this->UploadFile("PRODUCT",$fileName[$a],$fileTmpName[$a],$fileSize[$a],$fileError[$a]);
      
      if(in_array($location, $errors)){
        header("location: ../".$this->classname."/products_services.php?result=".$location."");
        exit();
      }else{
        array_push($array,$productname[$a],$productdes[$a],$price[$a],$location, $id);
      }
    } 

    $query=$this->master->insert("products", $sql, $types, $array);
    if(!$query){
      // header("location: ../index.php?error=databaseError3");
      echo "its me";
      exit();
     }else{
      header("location: ../".$this->classname."/keywords.php?result=success");
      exit();
     }


  }else{
    header("location: ../".$this->classname."/products_services.php?result=exists");
    exit();
  }
}



function smme_links($businesslink,$linktype){


  if($ids=session::get($this->id)){
    $sql=$this->BUSINESS_LINKS_INSERT[0];
    $array=array();
    $types="";
    for ($a = 0; $a < count($businesslink); $a++)
    {
  if($a!==(count($businesslink)-1)){
        $sql.="(?,?,?), ";
      }else{
        $sql.="(?,?,?);";
      }
  
      $types.="iis";
      
      
      array_push($array,$ids, $linktype[$a], $businesslink[$a]);
    } 
   

    $query=$this->master->select("business_links", $this->SMME_LINK[0], $this->SMME_LINK[1], array(session::get($this->id)));
    $result=$this->master->getResult();


    for ($a = 0; $a < count($linktype) ; $a++) {

    for ($i = 0; $i < count($result) ; $i++) {

      if ($linktype[$a]==$result[$i]['LINK_ID']){


        header("location: ../".$this->classname."/links.php?result=exists");
        exit();

      
      }
      else{

        
      }

    }
    
   }
 

    $query=$this->master->insert("business_links", $sql, $types, $array);
    if(!$query){
      header("location: ../".$this->classname."/links.php?result=exists");
      exit();
     }else{
      header("location: ../".$this->classname."/links.php?result=success");
      exit();
     }


  }else{
    header("location: ../".$this->classname."/links.php?result=exists");
    exit();
  }
}



function business_links($businesslink,$linktype){

  if($id=session::get($this->id)){
    $sql=$this->BUSINESS_LINKS_INSERT[0];
    $array=array();
    $types="";
    for ($a = 0; $a < count($businesslink); $a++)
    {
  if($a!==(count($businesslink)-1)){
        $sql.="(?,?,?), ";
      }else{
        $sql.="(?,?,?);";
      }
  
      $types.="iis";
      
      array_push($array,$id, $linktype[$a], $businesslink[$a]);
    } 
    $query=$this->master->insert("products", $sql, $types, $array);
    if(!$query){
      header("location: ../".$this->classname."/business_links.php?result=exists");
      exit();
     }else{
      header("location: ../".$this->classname."/business_links.php?result=success");
      exit();
     }
  }else{
    header("location: ../".$this->classname."/links.php?result=exists");
    exit();
  }
}

function expensesummary_get(){
  $params = array(session::get($this->id));
  $query = $this->master->select_prepared_async($this->EXPENSESUMMARY_SELECT[0], DB_NAME_1 , $this->EXPENSESUMMARY_SELECT[1], $params);
  if(!$query){
    echo "An error has occured in the database";
    exit();
  }else{
    $result = $this->master->getResult();
  }
}




// function peek($where, $which){
//   $id = session::get($this->id);
//   if(!$id){
//     echo 99;
//     exit();
//   }else{
//     if($where==0 || $where==2){// 0-smme seeing companies; 2-company seeing smmes
//         $sql1=$this->TOVIEW1_SELECT1[0];
//         $temp1=$this->var2;
//         $sql2=$this->TOVIEW1_SELECT2[0];
//         $types2=$this->TOVIEW1_SELECT2[1];
//         $sql3=$this->TOVIEW1_SELECT3[0];
//         $types3=$this->TOVIEW1_SELECT3[1];
//       }elseif($where==1){
//         $sql1=$this->TOVIEW1_SELECT_2_1[0];
//         $temp1=$this->var3;
//         $sql2=$this->TOVIEW1_SELECT_2_2[0];
//         $types2=$this->TOVIEW1_SELECT_2_2[1];
//         $sql3=$this->TOVIEW1_SELECT_2_3[0];
//         $types3=$this->TOVIEW1_SELECT_2_3[1];
//       }else{
//       echo "Invalid credentials";
//       exit();
//     }
//     if($which==0){
//       $query1=$this->master->select_multiple_async($sql1, $temp1);
//       if(!$query1){
//         echo "query 1 error";
//         echo implode("", $this->master->connresult);
//       }
//     }elseif($which==1 || $which==2){
//       $query2=$this->master->select_prepared_async($sql2, $temp1, $types2, array($which, $id));
//       if(!$query2){
//         echo "query 2 error";
//         echo implode("", $this->master->connresult);
//       }
//     }else{
//       //for comparative charts
//     }
//       $result=$this->master->getResult();
//       if(empty($result)){
//         echo "EMPTY";
//         exit();
//       }
//       else{
//         $x=array();
//         foreach($result as $key => $val) {
//           if($which==0){
//           $x[$key] = $val["BBBEE_Status"];
//           }elseif($which==3){
//             //anchor for comparative charts
//           }else{
//             $x[$key] = $val["Progress"];
//           }
//        }
//        if($x==session::get($this->classname."_Progress"[$which])){
//          echo 1;
//          exit();
//        }else{
//         session::set($this->classname."_Progress", $x);
//         if($where==0 && $which!==0){
//           $this->COMPANYloop($result);
//         }elseif($which==0 && $where==0){
//           $this->COMPANYAllTable($result);
//         }elseif($which==0 && $where==2){
//           $this->SMMEAllTable($result);
//         }elseif($which==0 && $where==1){
//           //npo all
//         }
//         elseif($where==1 && $which!==0){
//           $this->NPOloop($result);
//         }else{
//           $this->SMMEloop($result);
//         }
//       }
//     }
//   }
// }

function ToView_entity_REQUESTED($entity){
  // if($this->classname == "COMPANY"){
  //   $EVENT_ID = 1;
  //   $sql = "SELECT register.Legal_name, Address, ext, BBBEE_Status, a.Progress, a.EVENT_ID, a.event_Start, register.SMME_ID AS ID,
  //   s.typeOfEntity, i.title
  //   FROM register, company_documentation, company_profile, pimg p, yasccoza_openlink_association_db.smme_company_events a,
  //   signup AS s, yasccoza_openlink_association_db.industry_title as i
  //   WHERE register.SMME_ID=company_documentation.SMME_ID 
  //   AND company_documentation.SMME_ID=company_profile.SMME_ID  
  //   AND company_profile.SMME_ID=p.SMME_ID 
  //   AND p.SMME_ID=a.SMME_ID
  //   AND register.SMME_ID = s.SMME_ID
  //   AND register.INDUSTRY_ID= i.TITLE_ID
  //   AND a.SMME_ID=?
  //   AND a.COMPANY_ID=?
  //   order by event_Start desc ";
  //  }elseif($this->classname == "SMME"){
  //   $EVENT_ID = 2;
  //  }
  $query=$this->master->select_prepared_async($this->TOVIEW1_SELECT2[0], $this->var2, $this->TOVIEW1_SELECT2[1], array($entity, session::get($this->id)));
  if(!$query){
    echo "query 2 error<br>";
    print_r(implode(" ", $this->master->connresult));
    echo "<br>";
    echo "select_prepared_async(".$this->TOVIEW1_SELECT2[0].", ".$this->var2."., ".$$this->TOVIEW1_SELECT2[1].", array($entity, ".session::get($this->id)."));";
    echo "<br>";
    exit();
  }else{
    $result=$this->master->getResult();
    // print_r($this->TOVIEW1_SELECT2[0]);
    // print_r($this->TOVIEW1_SELECT2[1]);
    // exit();
    
    $query=$this->master->select_prepared_async($this->TOVIEW1_SELECT2[0], $this->var2, $this->TOVIEW1_SELECT2[1], array($entity, session::get($this->id)));
    if($this->classname == "COMPANY"){
      $this->SMMEloop($result);
     }elseif($this->classname == "SMME"){
      $this->COMPANYloop($result);
     }
  }
}
function MyConsultantSelect($page){
  $query_limit = $this->CONSULTANTS_SELECT[0]." LIMIT ".$page.",".(10);
  // print_r($query_limit);
  // exit();
  $query=$this->master->select_prepared_async($this->CONSULTANTS_SELECT[0], $this->var2, $this->CONSULTANTS_SELECT[1], array(session::get($this->id)));
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    $pages = ceil(count($result)/10);
    $query=$this->master->select_prepared_async($query_limit, $this->var2, "i", array(session::get($this->id)));
    if(!$query){
      echo "query 1 error";
      echo implode("", $this->master->connresult);
      exit();
    }else{
      $result=$this->master->getResult();
      if($this->classname == "COMPANY"){
        $this->myConsultantsTable($result,$pages);
      
    }
  }
  }
}
function AllConsultants($page){
  $query_limit = $this->ALL_CONSULTANTS_SELECT[0]." LIMIT ".$page.",".(10);
  // print_r($query_limit);
  // exit();
  $query=$this->master->select_multiple_async($this->ALL_CONSULTANTS_SELECT[0], $this->var4);
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    $pages = ceil(count($result)/10);
    $query=$this->master->select_multiple_async($query_limit, $this->var4);
    if(!$query){
      echo "query 1 error";
      echo implode("", $this->master->connresult);
      exit();
    }else{
      $result=$this->master->getResult();
      if($this->classname == "M_ADMIN" || $this->classname == "G_ADMIN" || $this->classname == "ADMIN" ){
        $this->myConsultantsTable($result,$pages);
      
    }
  }
  }
}
function ToView_entity_ALL($page){
  $query_limit = $this->TOVIEW1_SELECT1[0]." LIMIT ".$page.",".(10);
  $query=$this->master->select_multiple_async($this->TOVIEW1_SELECT1[0], $this->var2);
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    $pages = ceil(count($result)/10);
    $query=$this->master->select_multiple_async($query_limit, $this->var2);
    if(!$query){
      echo "query 1 error";
      echo implode("", $this->master->connresult);
      exit();
    }else{
      $result=$this->master->getResult();
      if($this->classname == "COMPANY"){
        $this->SMMEAllTable($result,$pages);
        
      }elseif($this->classname == "SMME" || $this->classname == "NPO"){
        $this->COMPANYAllTable($result,$pages);
      }
      
    }
    
  }
}
function ToView_smme_ALL($page){
  $query_limit = $this->TOVIEW1_SELECT1[0]." LIMIT ".$page.",".(10);
  $query=$this->master->select_multiple_async($this->TOVIEW1_SELECT1[0], $this->var2);
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    $pages = ceil(count($result)/10);
    $query=$this->master->select_multiple_async($query_limit, $this->var2);
    if(!$query){
      echo "query 1 error";
      echo implode("", $this->master->connresult);
      exit();
    }else{
      $result=$this->master->getResult();
        $this->AdminSMMEAllTable($result,$pages);
    }
    
  }
}
function ToView_company_ALL($page){
  // $this->master->changedb("yasccoza_openlink_companies");
  $query_limit = $this->TOVIEW2_SELECT2[0]." LIMIT ".$page.",".(10);
  // print_r($this->TOVIEW2_SELECT2[0]);
  // exit();
  $query=$this->master->select_multiple_async($this->TOVIEW2_SELECT2[0], $this->var2);
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    $pages = ceil(count($result)/10);
    $query=$this->master->select_multiple_async($query_limit, $this->var2);
    if(!$query){
      echo "query 1 error";
      echo implode("", $this->master->connresult);
      exit();
    }else{
      $result=$this->master->getResult();
        $this->AdminCOMPANYAllTable($result,$pages);
    }

    
    
  }
}
function individual_rating($id){//works out a system rating for a single smme
  $query = $this->master->select_prepared_async($this->COMPARITIVE_CHART[0], DB_NAME_1, $this->COMPARITIVE_CHART[1],array($id));
  // $query=$this->master->select_multiple_async($this->COMPARITIVE_CHART[0], 'smmes');
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    if(empty($result)){
      return 0;
    }

    
        $factor = 18000;
        $total_points = 20;
        for($x = 0; $x < count($result); $x++){
          $sum = 0;
          $expense_score = 0;
          $status_score = 0;
          $overall_score = 0;
          
          $sum += $result[$x]["rand_value"];
          
        //chechking expense summary for a score 
          if($sum >= $factor){
            $expense_score = 10;
          }else if((($sum/$factor)*100)>= 75){
            $expense_score = 7.51;
          }else if((($sum/$factor)*100)>= 68){
            $expense_score = 6.83;
          }else if((($sum/$factor)*100)>= 59){
            $expense_score = 5.92;
          }else if((($sum/$factor)*100)>= 51){
            $expense_score = 5.13;
          }else if((($sum/$factor)*100)>= 46){
            $expense_score = 4.67;
          }else if((($sum/$factor)*100)>= 38){
            $expense_score = 3.86;
          }else if((($sum/$factor)*100)>= 23){
            $expense_score = 2.39;
          }else{
            $expense_score = 0;
          }

          $status = $result[$x]["BBBEE_Status"];
          
          if($status > 8){
            $status_score = 0;
          }
          else{//non compliance
            $status_score = $this->status_rating($status);
          }
          $overall_score = $status_score + $expense_score;
          $rating = ($overall_score / $total_points) * 100;
          return $rating;
          //score out of 20 as a percentage
          // array_push($entity_metrics, array($result[$x]["Legal_name"],$rating));
  
    }
  }
}
public function ToView_Charts(){
  
  $query = $this->master->select_prepared_async($this->COMPARITIVE_CHART[0], DB_NAME_1, $this->COMPARITIVE_CHART[1],array(session::get($this->id)));
  // $query=$this->master->select_multiple_async($this->COMPARITIVE_CHART[0], 'smmes');
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    
    if(empty($result)){
      return -1;
      
    }
    if($this->classname == "COMPANY"){
        $entity_metrics = array();
        $factor = 18000;
        $total_points = 20;
        for($x = 0; $x < count($result); $x++){
          $sum = 0;
          $expense_score = 0;
          $status_score = 0;
          $overall_score = 0;
          
          $sum += $result[$x]["rand_value"];
          
        //chechking expense summary for a score 
          if($sum >= $factor){
            $expense_score = 10;
          }else if((($sum/$factor)*100)>= 75){
            $expense_score = 7.51;
          }else if((($sum/$factor)*100)>= 68){
            $expense_score = 6.83;
          }else if((($sum/$factor)*100)>= 59){
            $expense_score = 5.92;
          }else if((($sum/$factor)*100)>= 51){
            $expense_score = 5.13;
          }else if((($sum/$factor)*100)>= 46){
            $expense_score = 4.67;
          }else if((($sum/$factor)*100)>= 38){
            $expense_score = 3.86;
          }else if((($sum/$factor)*100)>= 23){
            $expense_score = 2.39;
          }else{
            $expense_score = 0;
          }

          $status = $result[$x]["BBBEE_Status"];
          
          if($status > 8){
            $status_score = 0;
          }
          else{//non compliance
            $status_score = $this->status_rating($status);
          }
          $overall_score = $status_score + $expense_score;
          $rating = ($overall_score / $total_points) * 100;//score out of 20 as a percentage
          array_push($entity_metrics, array($result[$x]["Legal_name"],$rating));
         
          // if($x == 0){
          //   array_push($percentages, $entity_metrics[$x][1]); 
          // }else{
          //   if(empty($percentages)){
          //     print_r($percentages);
          //     echo "That array is empty";
          //     exit();
          //   }    print_r($percentages);
          // print_r($entity_metrics);
          // print_r($result);
          // exit();
          //   if( $percentages < $entity_metrics[$x][1] ){
              
          //     $cur =  $percentages[$x-1];
          //     array_push($percentages, $entity_metrics[$x] );
          //     array_push($percentages, $cur);
            
          //   }else{
          //     $percentages[$x] =  $entity_metrics;
          //   }
          // }
        }
        // print_r($entity_metrics);
        return json_encode($entity_metrics);
      }
    }
  }


private function status_rating($status){
  $status_rating = array(
    1 => 10,
    2 => 7.513148009,
    3 => 6.830134554,
    4 => 5.92025254,
    5 => 5.131581182,
    6 => 4.665073802,
    7 => 3.855432894,
    8 => 2.393920494

  );
  foreach($status_rating as $key => $val){
    if($status == $key){
        return $status_rating[$status];
    }
  }

}

Private function NPOloop(array $result){
  echo "<table class='newone'>";
  echo "<thead>";
  echo  "<tr class='arrow-steps'>";
  echo    "<th scope='col'>#</th>";
  echo    "<th scope='col'class='step current'>Step 1</th>";
  echo    "<th scope='col' class='step'>Step 2</th>";
  echo    "<th scope='col' class='step  current'>Step 3</th>";
  echo    "<th scope='col' class='step'>Step 4</th>";
  echo  "</tr>";
  echo "</thead>";
  echo "<tbody>";
  for($i=0; $i<=count($result)-1; $i++){//row
    $sum = $i + 1;
    echo "<tr>";
    echo "<th scope='row'>".$sum."</th>";
   for($j=1; $j<=4; $j++){//cell in row
    echo "<td>";
     if($result[$i]['Progress']==$j){
       echo "<div class='card' style='width: 100%;'>";
       echo "<div>";
        echo "<img src='".$result[$i]['ext']."'class='img-fluid rounded circle' w-50 mb-3' style='max-width:100%; max-height:100%;'>";
        echo "</div>";
       echo "<div class='card-body' style='width: 100%;'>";
       echo "<h5 class='card-title'>".$result[$i]['Legal_name']."</h5>";
       echo "<h5 class='card-text'>".$result[$i]['Province'].", ".$result[$i]['city']."</h5>";
       echo "<p class='card-text'> BBBEE Level: ".$result[$i]['BBBEE_Status']."</p>";
       echo "</div>";
       echo "</div>";
     }
    echo "</td>";
   }
   echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";
}

private function SMMEloop(array $result){
  if(empty($result)){
    echo "<p class='text-capitalize text-center h1' >No Requests Yet</p>";
    exit();
  }
  echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  echo     "<th></th>";
  echo     "<th>Legal Name</th>";
  echo     "<th>Address</th>";
  echo   "<th>Industry</th>";
  echo     "<th>BBBEE</th>";
  echo     "<th>Profile</th>";
  echo     "<th>Progress</th>";
  echo     "</tr>";
  echo   "</thead>";
  echo "<tbody>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["office"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["BBBEE_Status"]."</td>";
    $id = token::encode($result[$i]["ID"]);
  echo "<td class='table-cell' data-href=''>";
  echo "<form method='POST' action='view_more.php?id=".$id."'>"; 
  echo '<input type="text" name="tk" value=';
  token::get("VIEW_MORE_YASC");
  echo ' required="" hidden>';
  echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
    $progress = 0;
    $progress_description = "";
    switch($result[$i]['Progress']){
      case 1:
        $progress = 25;
        $progress_description = "REQUESTED";
        break;
      case 2:
        $progress = 50;
        $progress_description = "READ";
        break;
      case 3:
        $progress = 75;
        $progress_description = "IN PROGRESS";
        break;
      case 4:
        $progress = 100;
        $progress_description = "FINALIZED";
        break;
        case 5:
          $progress = 100;
          $progress_description = "TERMINATED";
          break;

    }
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>";
    echo "<div class='progress'>
            <div class='progress-bar rounded' role='progressbar' style='width: ".$progress."%;color:black !important;' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100'><span class='text-center'>". $progress_description."</span></div>
          </div>";
    echo "</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";


}
private function myConsultantsTable(array $result, $page){
  echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  // echo     "<th></th>";
  echo     "<th>Name</th>";
  echo     "<th>Surname</th>";
  echo   "<th>Email</th>";
  echo     "<th>Gender</th>";
  echo     "<th>Profile</th>";
  echo     "<th>Initiate</th>";
  echo     "</tr>";
  echo   "</thead>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    // echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["First_Name"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Surname"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Email"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Gender"]."</td>";
    $id = token::encode($result[$i]["ID"]);
    echo "<td class='table-cell' data-href=''>";
    // echo "<form method='POST' action='view_more.php?id=".$id."'>"; 
    // echo '<input type="text" name="tk" value=';
    // token::get("VIEW_MORE_YASC");
    // echo ' required="" hidden>';
    // echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
    echo "<a class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='Chat' href='chat.php?url=".token::encode($result[$i]["ID"])."'> <i class='fa fa-comments-o'></i></a></td>";
    echo "<td>";
    echo "<form method='Post' action='../Main/Main_Notify.php?id=".token::encode($result[$i]["ID"])."'>";
    echo '<input type="text" name="tk" value=';
    token::get("COMPANY_request_notification_YASC");
    echo ' required="" hidden>';
    echo "<button class='btn btn-primary' type='submit' name='COMPANY_request_notification' >";
    echo "Connect";
    echo "</button>";
    echo "</form>";
    echo "</td>";
    // $id = base64_encode($result[$i]["ID"]);
    // $type_of_entity = base64_encode($result[$i]["T"]);
    // echo "<a href='view_more.php?t=".$type_of_entity."&i=".$id."' class='btn' type='button' >View More</button></td>";
    // echo  "<td class='table-cell' data-href=''><a href='chat.php?url=".token::encode($result[$i]["ID"])."'>Chat</a></td>";
    // echo "</td>";
    echo "</tr>";
  }
  echo "</table>";
  echo '<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-end">
   ';
  
   for($i = 1; $i <= $page; $i++){
    echo ' <li class="page-item"><a class="page-link" href="mySMME_ALL.php?page='.$i.'">'.$i.'</a></li>';
   
   }
  echo'
    
    
  </ul>
  </nav>';
  
  
  }
private function CONSULTANTSAllTable(array $result, $page){
  echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  echo     "<th></th>";
  echo     "<th>Legal Name</th>";
  echo     "<th>Address</th>";
  echo   "<th>Industry</th>";
  echo     "<th>BBBEE</th>";
  echo     "<th>Profile</th>";
  echo     "<th>Initiate</th>";
  echo     "</tr>";
  echo   "</thead>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["office"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["BBBEE_Status"]."</td>";
    $id = token::encode($result[$i]["ID"]);
    echo "<td class='table-cell' data-href=''>";
    echo "<form method='POST' action='view_more.php?id=".$id."'>"; 
    echo '<input type="text" name="tk" value=';
    token::get("VIEW_MORE_YASC");
    echo ' required="" hidden>';
    echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
    echo "<a class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='Chat' href='chat.php?url=".token::encode($result[$i]["ID"])."'> <i class='fa fa-comments-o'></i></a></td>";
    echo "<td>";
    echo "<form method='Post' action='../Main/Main_Notify.php?id=".token::encode($result[$i]["ID"])."'>";
    echo '<input type="text" name="tk" value=';
    token::get("COMPANY_request_notification_YASC");
    echo ' required="" hidden>';
    echo "<button class='btn btn-primary' type='submit' name='COMPANY_request_notification' >";
    echo "Connect";
    echo "</button>";
    echo "</form>";
    echo "</td>";
    // $id = base64_encode($result[$i]["ID"]);
    // $type_of_entity = base64_encode($result[$i]["T"]);
    // echo "<a href='view_more.php?t=".$type_of_entity."&i=".$id."' class='btn' type='button' >View More</button></td>";
    // echo  "<td class='table-cell' data-href=''><a href='chat.php?url=".token::encode($result[$i]["ID"])."'>Chat</a></td>";
    // echo "</td>";
    echo "</tr>";
  }
  echo "</table>";
  echo '<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-end">
   ';
  
   for($i = 1; $i <= $page; $i++){
    echo ' <li class="page-item"><a class="page-link" href="mySMME_ALL.php?page='.$i.'">'.$i.'</a></li>';
   
   }
  echo'
    
    
  </ul>
  </nav>';
  
  
  }

  private function AdminSMMEAllTable(array $result, $page){
    echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
    echo  "<thead>";
    echo     "<tr>";
    echo     "<th></th>";
    echo     "<th>Legal Name</th>";
    echo     "<th>Address</th>";
    echo   "<th>Industry</th>";
    echo     "<th>BBBEE</th>";
    echo     "<th>Profile</th>";
    echo     "<th>Initiate</th>";
    echo     "</tr>";
    echo   "</thead>";
    for($i=0; $i<=count($result)-1; $i++){//row
      echo "<tr>";
      echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
      echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
      echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
      echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["office"]."</td>";
      echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["BBBEE_Status"]."</td>";
      $id = token::encode($result[$i]["ID"]);
      echo "<td class='table-cell' data-href=''>";
      echo "<form method='POST' action='view_more.php?id=".$id."'>"; 
      echo '<input type="text" name="tk" value=';
      token::get("VIEW_MORE_YASC");
      echo ' required="" hidden>';
      echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
      echo "<a class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='Chat' href='chat.php?url=".token::encode($result[$i]["ID"])."'> <i class='fa fa-comments-o'></i></a></td>";
      echo "<td>";
      echo "<form method='Post' action='../ADMIN/verify.php?id=".token::encode($result[$i]["ID"])."'>";
      echo "<input type='text' name='tk' value='";
      token::get("ADMIN_VIEWFILES_YASC");
      echo "' required='' hidden>";
      echo "<button class='btn btn-primary' type='submit' name='SMME_request_notification' >";
      echo "Verify";
      echo "</button>";
      echo "</form>";
      echo "</td>";
      // $id = base64_encode($result[$i]["ID"]);
      // $type_of_entity = base64_encode($result[$i]["T"]);
      // echo "<a href='view_more.php?t=".$type_of_entity."&i=".$id."' class='btn' type='button' >View More</button></td>";
      // echo  "<td class='table-cell' data-href=''><a href='chat.php?url=".token::encode($result[$i]["ID"])."'>Chat</a></td>";
      // echo "</td>";
      echo "</tr>";
    }
    echo "</table>";
    echo '<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-end">
     ';
    
     for($i = 1; $i <= $page; $i++){
      echo ' <li class="page-item"><a class="page-link" href="mySMME_ALL.php?page='.$i.'">'.$i.'</a></li>';
     
     }
    echo'
      
      
    </ul>
    </nav>';
    
    
}

private function SMMEAllTable(array $result, $page){
echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
echo  "<thead>";
echo     "<tr>";
echo     "<th></th>";
echo     "<th>Legal Name</th>";
echo     "<th>Address</th>";
echo   "<th>Industry</th>";
echo     "<th>BBBEE</th>";
echo     "<th>Profile</th>";
echo     "<th>Initiate</th>";
echo     "</tr>";
echo   "</thead>";
for($i=0; $i<=count($result)-1; $i++){//row
  echo "<tr>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["office"]."</td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["BBBEE_Status"]."</td>";
  $id = token::encode($result[$i]["ID"]);
  echo "<td class='table-cell' data-href=''>";
  echo "<form method='POST' action='view_more.php?id=".$id."'>"; 
  echo '<input type="text" name="tk" value=';
  token::get("VIEW_MORE_YASC");
  echo ' required="" hidden>';
  echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
  echo "<a class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='Chat' href='chat.php?url=".token::encode($result[$i]["ID"])."'> <i class='fa fa-comments-o'></i></a></td>";
  echo "<td>";
  echo "<form method='Post' action='../Main/Main_Notify.php?id=".token::encode($result[$i]["ID"])."'>";
  echo '<input type="text" name="tk" value=';
  token::get("COMPANY_request_notification_YASC");
  echo ' required="" hidden>';
  echo "<button class='btn btn-primary' type='submit' name='COMPANY_request_notification' >";
  echo "Connect";
  echo "</button>";
  echo "</form>";
  echo "</td>";
  // $id = base64_encode($result[$i]["ID"]);
  // $type_of_entity = base64_encode($result[$i]["T"]);
  // echo "<a href='view_more.php?t=".$type_of_entity."&i=".$id."' class='btn' type='button' >View More</button></td>";
  // echo  "<td class='table-cell' data-href=''><a href='chat.php?url=".token::encode($result[$i]["ID"])."'>Chat</a></td>";
  // echo "</td>";
  echo "</tr>";
}
echo "</table>";
echo '<nav aria-label="Page navigation example">
<ul class="pagination justify-content-end">
 ';

 for($i = 1; $i <= $page; $i++){
  echo ' <li class="page-item"><a class="page-link" href="mySMME_ALL.php?page='.$i.'">'.$i.'</a></li>';
 
 }
echo'
  
  
</ul>
</nav>';


}

private function COMPANYAllTable(array $result,$page){
  echo "<table class='table-responsive table table-striped company_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  echo     "<th></th>";
  echo     "<th>Legal Name</th>";
  echo     "<th>Address</th>";
  echo   "<th>Industry</th>";
  echo   "<th>Profile</th>";
  echo     "<th>Initiate</th>";
  echo     "</tr>";
  echo   "</thead>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["title"]."</td>";
    $id = token::encode($result[$i]["ID"]);
    echo "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><form method='POST' action='view_more.php?id=".$id."'>"; 
    echo '<input type="text" name="tk" value=';
    token::get("VIEW_MORE_YASC");
    echo ' required="" hidden>';
    echo '<div style="display:flex">';
    echo '<div style="flex:1">';
    echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
    echo '</div>';
    echo '<div style="flex:1">';
    echo "<a href='chat.php?url=".token::encode($result[$i]["ID"])."'class='btn btn-primary' type='button'  data-toggle='tooltip' data-placement='top' title='Message'><i class='fa fa-envelope'></i></a></td>";
    echo '</div>';
    echo "<td>";
    echo "<form method='Post' action='../Main/Main_Notify.php?id=".token::encode($result[$i]["ID"])."'>";
    echo "<input type='text' name='tk' value='";
    token::get("SMME_request_notification_YASC");
    echo "' required='' hidden>";
    echo "<button class='btn btn-primary' type='submit' name='SMME_request_notification' >";
    echo "Connect";
    echo "</button>";
    echo "</form>";
    echo "</td>";
    echo "</td>";
    echo "</tr>";
    echo '</div>';
    

  }
  echo "</table>";
  $display = '<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-end">
  ';
  for($i = 1; $i <= $page; $i++){
    $display .= ' <li class="page-item"><a class="page-link" href="myBBBEE_ALL.php?page='.$i.'">'.$i.'</a></li>
    ';
  }
  $display .='
    
    
  </ul>
  </nav>';
  echo $display;
}

private function AdminCOMPANYAllTable(array $result,$page){
  echo "<table class='table-responsive table table-striped company_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  echo     "<th></th>";
  echo     "<th>Legal Name</th>";
  echo     "<th>Address</th>";
  echo   "<th>Industry</th>";
  echo   "<th>Profile</th>";
  echo     "<th>Initiate</th>";
  echo     "</tr>";
  echo   "</thead>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["title"]."</td>";
    $id = token::encode($result[$i]["ID"]);
    echo "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><form method='POST' action='view_more.php?id=".$id."'>"; 
    echo '<input type="text" name="tk" value=';
    token::get("VIEW_MORE_YASC");
    echo ' required="" hidden>';
    echo '<div style="display:flex">';
    echo '<div style="flex:1">';
    echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
    echo '</div>';
    echo '<div style="flex:1">';
    echo "<a href='chat.php?url=".token::encode($result[$i]["ID"])."'class='btn btn-primary' type='button'  data-toggle='tooltip' data-placement='top' title='Message'><i class='fa fa-envelope'></i></a></td>";
    echo '</div>';
    echo "<td>";
    echo "<form method='Post' action='../ADMIN/verify.php?id=".token::encode($result[$i]["ID"])."'>";
    echo "<input type='text' name='tk' value='";
    token::get("ADMIN_VIEWFILES_YASC");
    echo "' required='' hidden>";
    echo "<button class='btn btn-primary' type='submit' name='SMME_request_notification' >";
    echo "Verify";
    echo "</button>";
    echo "</form>";
    echo "</td>";
    echo "</td>";
    echo "</tr>";
    echo '</div>';
    

  }
  echo "</table>";
  $display = '<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-end">
  ';
  for($i = 1; $i <= $page; $i++){
    $display .= ' <li class="page-item"><a class="page-link" href="myBBBEE_ALL.php?page='.$i.'">'.$i.'</a></li>
    ';
  }
  $display .='
    
    
  </ul>
  </nav>';
  echo $display;
}

private function COMPANYloop(array $result){
  if(empty($result)){
    echo "<p class='text-capitalize text-center h1' >No Requests Yet</p>";
    exit();
  }
  echo "<table class='table-responsive table table-striped company_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  echo     "<th></th>";
  echo     "<th>Legal Name</th>";
  echo     "<th>Address</th>";
  echo   "<th>Industry</th>";
  echo   "<th>Profile</th>";
  echo   "<th>Progress</th>";
  echo     "</tr>";
  echo   "</thead>";
  echo "<tbody>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</br>".$result[$i]["foo"]." Company</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</br>".$result[$i]["Province"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["office"]."</td>";
    $id = token::encode($result[$i]["ID"]);
    echo "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><form method='POST' action='view_more.php?id=".$id."'>"; 
    echo '<input type="text" name="tk" value=';
    token::get("VIEW_MORE_YASC");
    echo ' required="" hidden>';
    echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form></td>";
    $progress = 0;
    $progress_description = "";
    switch($result[$i]['Progress']){
      case 1:
        $progress = 25;
        $progress_description = "REQUESTED";
        break;
      case 2:
        $progress = 50;
        $progress_description = "READ";
        break;
      case 3:
        $progress = 75;
        $progress_description = "IN PROGRESS";
        break;
      case 4:
        $progress = 100;
        $progress_description = "FINALIZED";
        break;
        case 5:
          $progress = 100;
          $progress_description = "TERMINATED";
          break;

    }
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>";
    echo "<div class='progress'>
            <div class='progress-bar rounded' role='progressbar' style='width: ".$progress."%; color:black !important;' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100'><span class='text-center text-dark'>".$progress_description."</span></div>
          </div>";
    echo "</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";

}




private function products($id){
  

  $types = $this->PRODUCTS[1];
  if($_SESSION['WHO'] == "SMME" ||  $_SESSION['WHO'] == "NPO"){
    $temp1=DB_NAME_1;
    $sql1 = $this->COMPANY_PRODUCTS[0];
  }
  else{
    $sql1 = $this->PRODUCTS[0];
    $temp1=$this->var2;
  }
  if($this->classname == "M_ADMIN"){
    $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id,$id));
  }else{
    $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id));
  }
  
  if(!$query){
    echo "error occured with products";
    exit();
  }else{
    $result = $this->master->getResult();
    return $result;
  }
}

private function fetchMore_info($id){//incomplete
  $sql1 = $this->MORE_INFO[0];
  $types = $this->MORE_INFO[1];
  $temp1=$this->var2;
  if($this->classname=="M_ADMIN"){
    
    $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id, $id));

  }else{
    $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id));
  }
    if(!$query){
    echo "sql ". $sql1;
    echo "<br>types ". $types;
    echo "<br>temp ". $temp1;
    echo "<br>value ". $id;
    echo $this->classname;
    // header("location: ../index.php?error=failedtofetchmoreinfo");
    exit();
  }else{
    $result = $this->master->getResult();
    
    if(is_numeric($result[0])){
      echo "<p class='text-center'>No information available yet, check in again later.</p>";
      exit();
  }else{
    return $result;
  }
    
  }
  
}

private function fetchLinks($id){
  $sql = $this->FETCH_LINKS[0];
  $types = $this->FETCH_LINKS[1];
  $temp1=$this->var2;
  if($this->classname == "M_ADMIN"||$this->classname == "G_ADMIN"||$this->classname == "ADMIN"){
    $query = $this->master->select_prepared_async($sql, $temp1, $types, array($id, $id));
  }else{
    $query = $this->master->select_prepared_async($sql, $temp1, $types, array($id));
  }
 
  if(!$query){

  }else{
    $result = $this->master->getResult();
    
    return $result;
  }

}
private function fetchMore_info2($id){//this one is for when an smme wants to view smme information
  $sql1 = $this->SMME_tO_SMME_MORE_INFO[0];
  $types = $this->SMME_tO_SMME_MORE_INFO[1];
    $query = $this->master->select_prepared_async($sql1, $this->var, $types, array($id));
    if(!$query){
    
    header("location: ../index.php?error=failedtofetchmoreinfo");
    exit();
  }else{
    $result = $this->master->getResult();
    return $result;
  }
  
}
private function fetchMore_info3($id){//this one is for when an company wants to view company information
  $sql1 = $this->COMPANY_TO_COMPANY_VIEW_MORE[0];
  $types = $this->COMPANY_TO_COMPANY_VIEW_MORE[1];
    $query = $this->master->select_prepared_async($sql1, $this->var, $types, array($id));
    if(!$query){
    
    header("location: ../index.php?error=failedtofetchmoreinfo");
    exit();
  }else{
    $result = $this->master->getResult();
    return $result;
  }
  
}

public function view_more_chart($id){
  $sql1 = $this->MORE_INFO_CHART[0];
  $types = $this->MORE_INFO_CHART[1];
  if($this->classname == "M_ADMIN"){
    $query = $this->master->select("smmes",$sql1, $types, array($id));
  }else{
    $query = $this->master->select("smmes",$sql1, $types, array($id, session::get($this->id)));
  }
  
  if(!$query){
    print_r($sql1);
      print_r($types);
      echo $id;
      
    exit();
  }else{
    $result = $this->master->getResult();
    // print_r($this->MORE_INFO_CHART[0]);
    // exit();
    if(empty($result)){
      return -1;
    }else{
      echo json_encode($result);
    }
    
  }
}

private function smme_view($result, $products, $id, $links){
  if(empty($result)&&empty($products)){
    echo "<p class='h3 text-center text-capitalize'>No information available</p>";
  }elseif(empty($result)){
    echo "<p class='h3 text-center text-capitalize'>No information available</p>";
  }else{
    
  $address = $result[0]['city'].", ".$result[0]['Province'];
  // Current avatar --><i class="fa fa-angle-left"></i>
  $display = '
                <div class="row">
               
                <h2  class="text-capitalize profile_title  display-4 " style="margin-left:25px; background-color:white; font-weight:bold; font-size:35px;">'.$result[0]['Legal_name'].'</h2>
                    <div class="col-md-3 col-sm-3 col-lg-3">
                    <img class="img-responsive border-rounded " style="width: 120px; margin-left:100px" src="'.$result[0]['ext'].'" alt="Avatar" title="">
                    </div>
                    <div class="col-md-9 col-sm-9 col-lg-9 justify-content-center align-items-center">
                      
                    </div>
                </div>
                
          
              ';
  $display .= '<hr><div class="col-md-12 col-sm-12 col-lg-12 ">
  <h4 class="profile_title h2 col-lg-12 col-md-12 textc" style="margin-left:px; font-size:28px; background-color:white">Company Information</h4><br><br><br>
                  <ul class="list-unstyled user_data" style="margin-left:70px">
                    <li class="text-capitalize movers" style="font-size:15px;">
                      <i class="fa fa-map-marker user-profile-icon" ></i> Address -> '.$address.'
                    </li>

                    <li class="text-capitalize text-jusitfy movers" style="font-size:15px;">
                      <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result[0]['foo'].'
                    </li>
                    <li class="text-capitalize text-jusitfy movers" style="font-size:15px; width:250px">
                    <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result[0]['title'].'
                    </li>
                    
                    <li class="text-capitalize text-jusitfy movers" style="font-size:15px;">
                    <a href="mailto:'.$result[0]['Email'].'"><i class="fa fa-envelope user-profile-icon"></i></a> Email -> '.$result[0]['Email'].'
                    </li>
                    <li class="text-capitalize text-jusitfy movers" style="font-size:15px;">
                    <a href="tel:';
                    $Contact = "";
                    if(substr($result[0]['Contact'],0,1) == 0 && (strlen($result[0]['Contact'])==10)){
                      $Contact = ltrim($result[0]['Contact'],'0');
                      $Contact = "+27".$result[0]['Contact'];
                    }else if((substr($result[0]['Contact'],0,1) != 0 && (strlen($result[0]['Contact'])==9))){
                      $Contact = "+27".$result[0]['Contact'];
                    }else{
                      $Contact = "+".$result[0]['Contact'];
                    }
                    $display.=''.$Contact.'"><i class="fa fa-phone user-profile-icon;" style="font-size:15px;"></i></a>'.$result[0]['Contact'].'
                    </li>
                  </ul>

                
                
  <!-- start skills -->
    ';
   
    if(!empty($links)){
      $display .= '<ul class="list-unstyled user_data" style="margin-left:70px">';
      for($i=0; $i<=count($links)-1; $i++){
       
        
        $display .= '<li class="text-capitalize" style="font-size:15px;">
        <button style="border:none; background:white" class=" WebsiteLinks" value="'.$links[$i]["LINK_ID"].'"><i class="'.$links[$i]["fav_icon_class"].'"/></i></button>
        '.$links[$i]["link_name"].' 
        <input value="'.$links[$i]["LINK_ID"].'" hidden> 
        <input value="'.$links[$i]["url"].'" hidden>
        </li>';
        

      }
      $display .= '</ul></div>';
    }
    $display .= '
    <br>
    <div class="row" style="width: 100% !important">
      
        <h4 class="profile_title h2 col-lg-12 col-md-12 textc" style="margin-left:20px; font-size:28px; background-color:white">Company Statements</h4><br>
        <table class="col-lg-9 col-md-12 col-sm-12" style="width: 100% !important; margin-left:70px ">
              <tbody style="width: 100% !important" >
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Introduction:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['introduction'].'</p>
                  </td>
                </tr>
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Mission:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['mission'].'</p>
                  </td>
                </tr>
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Vision:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['vision'].'</p>
                  </td>
                </tr>
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Values:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['values_'].'</p>
                  </td>
                </tr>
                
              </tbody>
        </table></div><br>
    ';
  //   <li class="text-capitalize text-jusitfy">
  //   
  // </li>
  // <li class="text-capitalize text-jusitfy">
  //  mission -> 
  // </li>
  // <li class="text-capitalize text-jusitfy">
  //  values -> '.$result[0]['values_'].'
  // </li>

    if($_SESSION['WHO'] == "COMPANY" || $this->classname == "ADMIN"|| $this->classname == "M_ADMIN"|| $this->classname == "G_ADMIN"){
      $expenses = $this->display_expense($id, 2);
      if($this->classname == "COMPANY"){
        $COMP_ID = session::get($this->id);
      $SMME_ID = $id;
      $params = array($COMP_ID, $SMME_ID);
      $query = $this->master->select_prepared_async($this->VALIDATE_CONNECTION[0], DB_NAME_5, $this->VALIDATE_CONNECTION[1], $params);
      $connection = $this->master->getResult();
     //2 symbolising that it is the entity viewing the smme expenses
      // print_r($connection);
      // print_r($params);
      // exit();
        if($expenses !== -1 && !empty($connection)){//Expenses not available when = -1. $CONNECTION VARIABLE HOLDS 1 IF CONNECTION EXISTS AND EMPTY IF NONE
        $display .= '<hr><div class=" row ">
          <div class="col-sm-12 col-md-12 col-lg-12">
            <br><h4 class="profile_title h2 col-lg-12 col-md-12 " style="margin-left:10px; font-size:28px; background-color:white">Expense Summary</h4><br>
          </div>
        </div>';
        $display .= '<div class="row">
          <h4 class="text-center">Direct Expenses</h4>
        <table class="table table-striped">
        ';
          $display .= $expenses;

          //########### PRODUCTS
          $display .= '
          <!-- start skills -->
            <hr>

            </section>
            <section>

            
            <h4 class="profile_title h2 col-lg-12 col-md-12 " style=" font-size:28px; background-color:white">Products</h4><br><br><div style="font-size:15px !important; margin-left:80px"><br><ul class="list-unstyled user_data propic">
            ';
            for($i=0; $i<=count($products)-1; $i++){
              if(isset($products[$i]['image'])){

              
              $display .= '<li class="products">';
              $display.= '<span class="product_name"> '.$products[$i]['product_name'].'</span></br>
                          <img class="product_image" src="../STORAGE/IMAGES/'.$products[$i]['image'].'"></br>
                          <span class="product_list_price">R'.$products[$i]['price'].'</span></br>
                          <p class="product_list_description">'.$products[$i]['product_description'].'</p>

              ';
              $display .= '</li>';
              }

            }
          $display .= '</ul></div><hr>';

          //###########
          $display .= '<!-- start of user-activity-graph -->
          
    
          <h4 class="profile_title h2 col-lg-12 col-md-12 " style="margin-left:10px; font-size:28px; background-color:white">Shareholder Information</h4>
          
  
    <!-- end of user-activity-graph -->';
        }else{
          $display .= '
          <!-- start skills -->
          <h4 class=" h2 col-lg-12 col-md-12 " style="margin-left:15px; font-size:28px; background-color:white">Products</h4><br>
           
            <section></br></br><div class="list-unstyled user_data" style="font-size:15px !important; margin-left:80px">
            <ul class="list-unstyled user_data propic">
            ';

            for($i=0; $i<=count($products)-1; $i++){
              if(isset($products[$i]['image'])){

              
              $display .= '<li class="products">';
              $display.= '<span class="product_name"> '.$products[$i]['product_name'].'</span></br>
                          <img class="product_image" src="../STORAGE/IMAGES/'.$products[$i]['image'].'"></br>
                          <span class="product_list_price">R'.$products[$i]['price'].'</span></br>
                          <p class="product_list_description">'.$products[$i]['product_description'].'</p>

              ';
              $display .= '</li>';
              }

            }

          if(!empty($connection)){
            $display .= '<!-- start of user-activity-graph -->
          
                <h2 class="profile_title">Shareholder Information</h2>
               
          <!-- end of user-activity-graph -->';
          }
        }
      }else{
        if($expenses !== -1){//Expenses not available when = -1.
          $display .= '<hr><div class=" row ">
            <div class="col-sm-12 col-md-12 col-lg-12">
              <br><h4 class="profile_title h2 col-lg-12 col-md-12 " style="margin-left:10px; font-size:28px; background-color:white">Expense Summary</h4><br>
            </div>
          </div>';
          $display .= '<div class="row">
            <h4 class="text-center">Direct Expenses</h4>
          <table class="table table-striped">
          ';
            $display .= $expenses;
  
            //########### PRODUCTS
            $display .= '
            <!-- start skills -->
              
  
              </section>
              <section>
  
              
              <h4 class="profile_title h2 col-lg-12 col-md-12 " style=" font-size:28px; background-color:white">Products</h4><div style="font-size:15px !important; margin-left:80px"><br><ul class="list-unstyled user_data propic">
              ';
              for($i=0; $i<=count($products)-1; $i++){
                if(isset($products[$i]['image'])){
  
                
                $display .= '<li class="products">';
                $display.= '<span class="product_name"> '.$products[$i]['product_name'].'</span></br>
                            <img class="product_image" src="../STORAGE/IMAGES/'.$products[$i]['image'].'"></br>
                            <span class="product_list_price">R'.$products[$i]['price'].'</span></br>
                            <p class="product_list_description">'.$products[$i]['product_description'].'</p>
  
                ';
                $display .= '</li>';
                }
  
              }
            $display .= '</ul></div>';
  
            //###########
            $display .= '<!-- start of user-activity-graph -->
            
      
            <h4 class="profile_title h2 col-lg-12 col-md-12 " style="margin-left:10px; font-size:28px; background-color:white">Shareholder Information</h4>
            
    
      <!-- end of user-activity-graph -->';
          }else{
            $display .= '
            <!-- start skills -->
            <h4 class=" h2 col-lg-12 col-md-12 " style="margin-left:15px; font-size:28px; background-color:white">Products</h4><br>
             
              <section></br></br><div class="list-unstyled user_data" style="font-size:15px !important; margin-left:80px">
              <ul class="list-unstyled user_data">
              ';
  
              for($i=0; $i<=count($products)-1; $i++){
                if(isset($products[$i]['image'])){
  
                
                $display .= '<li class="products">';
                $display.= '<span class="product_name"> '.$products[$i]['product_name'].'</span></br>
                            <img class="product_image" src="../STORAGE/IMAGES/'.$products[$i]['image'].'"></br>
                            <span class="product_list_price">R'.$products[$i]['price'].'</span></br>
                            <p class="product_list_description">'.$products[$i]['product_description'].'</p>
  
                ';
                $display .= '</li>';
                }
  
              }
              
            if(!empty($connection)){
              $display .= '<!-- start of user-activity-graph -->
            
                  <h2 class="profile_title">Shareholder Information</h2>
                 
            <!-- end of user-activity-graph -->';
            }
          }
      }
      
    }else{
      $display .= '
      <!-- start skills -->
        <h4 class=" h2 col-lg-12 col-md-12 " style="margin-left:15px; font-size:28px; background-color:white">Products</h4><br>
        <section><ul class="list-unstyled user_data">
        ';
        for($i=0; $i<=count($products)-1; $i++){
          if(isset($products[$i]['image'])){

          
          $display .= '<li class="products">';
          $display.= '<span class="product_name"> '.$products[$i]['product_name'].'</span></br>
                      <img class="product_image" src="../STORAGE/IMAGES/'.$products[$i]['image'].'"></br>
                      <span class="product_list_price">R'.$products[$i]['price'].'</span></br>
                      <p class="product_list_description">'.$products[$i]['product_description'].'</p>

          ';
          $display .= '</li>';
          }

        }
      
      if(!empty($connection)){
        $display .= '<!-- start of user-activity-graph -->
      
            <h2 class="profile_title">Shareholder Information</h2>
    
        
        
      <!-- end of user-activity-graph -->';
      }
    }

    






  // $info .= "<form method='Post' action='../Main/main_notify.php?id=".$result[$i]["ID"]."'>";
  // if($this->classname == "SMME"){
  // $info .= "<button class='btn btn-primary' type='submit' name='SMME_request_notification' >";
  // }else if($this->classname == "NPO"){
  //   $info .= "<button class='btn btn-primary' type='submit' name='NPO_request_notification' >";
  // }
  // else{
  //   $info .= "<button class='btn btn-primary' type='submit' name='COMPANY_request_notification' >";
  // }
  // $info .= "Connect";
  // $info .="</button>";
  // $info .= "<button class='btn' type='submit'>Message</button>";
  // $info .= "</form>";
  // $info .= "</td>";
  // $info .= "</tr>";
  // $info .= "</table>";
  echo $display;
  }
}

private function company_view_more($result, $products, $links){

  $address = $result[0]['city'].", ".$result[0]['Province'];

  $display = '<div class="col-md-3 col-sm-3  profile_left" >
  <div class="profile_img">
    <div id="crop-avatar" >
      <!-- Current avatar -->
      <img class="img-responsive avatar-view" src="'.$result[0]['ext'].'" alt="Avatar" title="Change the avatar">
    </div>
  </div>
  
  </div>

    ';
    $display .= '<div class="col-md-12 col-sm-12 col-lg-12 ">
    <h4 class="profile_title h2 col-lg-12 col-md-12 textc" style="margin-left:px; font-size:28px; background-color:white">Company Information</h4><br><br><br>
                    <ul class="list-unstyled user_data textc" style="margin-left:70px">
                      <li class="text-capitalize movers" style="font-size:15px;">
                        <i class="fa fa-map-marker user-profile-icon"  ></i> Address -> '.$address.'
                      </li>
  
                      <li class="text-capitalize text-jusitfy movers " style="font-size:15px;">
                        <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result[0]['foo'].'
                      </li>
                      <li class="text-capitalize text-jusitfy movers" style="font-size:15px; word-wrap:break-word !important; width:250px">
                      <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result[0]['title'].'
                      </li>
                      
                      <li class="text-capitalize text-jusitfy movers" style="font-size:15px;">
                      <a href="mailto:'.$result[0]['Email'].'"><i class="fa fa-envelope user-profile-icon"></i></a> Email -> '.$result[0]['Email'].'
                      </li>
                      <li class="text-capitalize text-jusitfy movers" style="font-size:15px;">
                      <a href="tel:';
                      $Contact = "";
                      if(substr($result[0]['Contact'],0,1) == 0 && (strlen($result[0]['Contact'])==10)){
                        $Contact = ltrim($result[0]['Contact'],'0');
                        $Contact = "+27".$result[0]['Contact'];
                      }else if((substr($result[0]['Contact'],0,1) != 0 && (strlen($result[0]['Contact'])==9))){
                        $Contact = "+27".$result[0]['Contact'];
                      }else{
                        $Contact = "+".$result[0]['Contact'];
                      }
                      $display.=''.$Contact.'"><i class="fa fa-phone user-profile-icon;" style="font-size:15px;"></i></a> '.$result[0]['Contact'].'
                      </li>
                      </ul>
                  
                  
    <!-- start skills -->
      ';
      if(!empty($links)){
        $display .= '<ul class="list-unstyled user_data" >';
        for($i=0; $i<=count($links)-1; $i++){
         
          
          $display .= '<li class="text-capitalize" style="font-size:15px;">
          <button style="border:none; background:white" class=" WebsiteLinks" value="'.$links[$i]["LINK_ID"].'"><i class="'.$links[$i]["fav_icon_class"].'"/></i></button>
          '.$links[$i]["link_name"].' 
          <input value="'.$links[$i]["LINK_ID"].'" hidden> 
          <input value="'.$links[$i]["url"].'" hidden>
          </li>';
          
  
        }
        $display .= '</ul></div>';
      }
      $display .= '
      <br>
      <div class="row" style="width: 100% !important">
      
        <h4 class="profile_title h2 col-lg-12 col-md-12 textc" style="margin-left:20px; font-size:28px; background-color:white">Company Statements</h4><br>
        <table class="col-lg-9 col-md-12 col-sm-12" style="width: 100% !important; margin-left:70px ">
              <tbody style="width: 100% !important" >
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Introduction:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['introduction'].'</p>
                  </td>
                </tr>
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Mission:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['mission'].'</p>
                  </td>
                </tr>
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Vision:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['vision'].'</p>
                  </td>
                </tr>
                <tr class="border-bottom" style="width: 100%">
                  <td style="padding: 10px !important; margin:5px !important; font-size:15px; "><p class="col-lg-3 col-md-3 col-sm-3 movers">Values:</p>
                  </td>
                  <td style="padding: 10px !important; margin:5px !important; ">
                  <p class="col-lg-9 col-md-9 col-sm-9 movers " style="word-wrap: break-word !important; margin-right:400px !important; text-align:justify; font-size:15px;">'.$result[0]['values_'].'</p>
                  </td>
                </tr>
                
              </tbody>
        </table></div><br>
      ';
      if(!empty($products)){
        $display .= '
          <!-- start skills -->
          <h4 class="profile_title h2 col-lg-12 col-md-12 textc" style="margin-left:15px; font-size:28px; background-color:white">Products</h4><br><br><br>
           
            <section><div class="list-unstyled user_data product" style="font-size:15px !important; margin-left:80px"><ul class="product_list_view_more propic">
            ';
            for($i=0; $i<=count($products)-1; $i++){
              if(isset($products[$i]['image'])){

              
              $display .= '<li class="products">';
              $display.= '<span class="product_name"> '.$products[$i]['product_name'].'</span></br>
                          <div class="product_image"><img class="img_products"  src="../STORAGE/IMAGES/'.$products[$i]['image'].'"></div></br>
                          <span class="product_list_price">R'.$products[$i]['price'].'</span></br>
                          <p class="product_list_description">'.$products[$i]['product_description'].'</p>

              ';
              $display .= '</li>';
              }

            }
            $display .= "</ul></div>";
      }
      
      

  echo $display;
}

private function insert_views($id){
  $insertsql = $this->VIEWS_INSERT[0];
  $inserttypes = $this->VIEWS_INSERT[1];
  
  $insert = array("VIEW MORE", session::get($this->id), $id);
  
  $selectAndInsertTable = "entity_clicks";
  $query=$this->master->insert($selectAndInsertTable, $insertsql, $inserttypes, $insert);
  if(!$query){
    echo "this is what is wrong";
    exit();
  }
  
}

public function insert_Websiteviews($id, $user){
  $insertsql = $this->VIEWS_INSERT[0];
  $inserttypes = $this->VIEWS_INSERT[1];
  $user = token::decode($user);
  $insert = array("WEBSITE VISIT", session::get($this->id), $user);
  
  $selectAndInsertTable = "entity_clicks";
  $query=$this->master->insert($selectAndInsertTable, $insertsql, $inserttypes, $insert);
  if(!$query){
    echo "this is what is wrong";
    exit();
  }else{
    //fetch and return url
    $query2 = $this->master->select_prepared_async($this->URL_SELECT[0], $this->var2, $this->URL_SELECT[1], array($id));
    if(!$query){
      print(-1) ;
    }else{
      $result = $this->master->getResult();
      if(!empty($result))$result = $result[0];
      print_r(json_encode($result));
      exit();
    }
  }
  
}
public function view_moreInfo($id){
  //#####NEW COMMENT##
  //when doing products display and want to display image, creat

$image_link = "https://openlinks.co.za/TIMS/STORAGE/IMAGES/";
//load the image name from the database and attach to this and that becoomes the src for the image

  //Function view more: used to view a users profile
  //makes a call to functions insert views, fetchmore info, products, smme_view and company_view
  //Insert view -> this is for analytics, everytime user views profile, record in databse
  //fetch more info -> fetches the users information
  //products -> if it is an smme that you are viewing, then fetch products information 
  //smme_view -> if you are viewing smme, call the display that runs for smme
  //company_view more -> if you are viewing company, call the display that runs for company
  $this->insert_views($id);
  $result = $this->fetchMore_info($id);//fetch all data here including who so we can identify who they are and work with conditions in the code
  $products = array();
  $links = $this->fetchLinks($id);
  if($this->classname == "COMPANY"){
    $products = $this->products($id);
    $this->smme_view($result,$products,$id, $links);
  }else{  
      $products = $this->products($id);  
      
      $this->company_view_more($result, $products, $links); 
  }
}

public function SMME_TO_SMME_view_moreInfo($id){
  $this->insert_views($id);
  $result = $this->fetchMore_info2($id);
  
  $products = array();
    $products = $this->products($id);
    $links = $this->fetchLinks($id);
    $this->smme_view($result,$products,$id,$links);
  
}

public function COMPANY_TO_COMPANY_view_moreInfo($id){
  $this->insert_views($id);
  $result = $this->fetchMore_info3($id);
  $products = $this->products($id);
  $links = $this->fetchLinks($id);
  $this->company_view_more($result, $products, $links);
  
}

public function display_expense($id, $identifier){
  $direct_expenses=[];
  $non_direct_expenses = [];
  $sql1 = $this->EXPENSESUMMARY_SELECT[0];
  $types = $this->EXPENSESUMMARY_SELECT[1];
  $query = $this->master->select_prepared_async($sql1, DB_NAME_1, $types, array($id));
  if(!$query){
    exit();
  }else{
    $result = $this->master->getResult();
    if(empty($result)&& $identifier ==1){
        
    }
    else if(empty($result)&& $identifier ==2){
      return -1;
    }else{
    for($i=0; $i<=count($result)-1; $i++){
      if($result[$i]['type_of_expense']==0){//direct expense = 0, non-direct = 1
        array_push($direct_expenses, $result[$i]['rand_value'] );
      }
      else if($result[$i]['type_of_expense']==1){
        array_push($non_direct_expenses, $result[$i]['rand_value'] );
      }
      else{
        echo "Something wrong happened public";
        exit();
      }
    }
  }
  if($identifier == 1){
    $body = $this->summary_layout($result);
  // $content = "<div class='container d-flex summary_display' style='height: 50vh'>";
  $content = $body;
  echo $content;
  }
  elseif($identifier == 2){
    $body = $this->summary_layout_two($result);
  // $content = "<div class='container d-flex summary_display' style='height: 50vh'>";
  $content = $body;
  return $content;
  }}
  
}

private function create_row($array){//takes in the expenses array and creates a row for the expense table
  $layout = "";
  $total = 0;
  $total_py = 0;
  for($i=0; $i<=count($array)-1; $i++){
    $layout .= "<tr class='row'>";
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".strtoupper($array[$i]['product_name'])."</td>";
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['rand_value']."</td>";
    $total+=$array[$i]['rand_value'];
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['frequency']."</td>";
    $amount = $array[$i]['rand_value'] * $array[$i]['frequency'];
    $total_py += $amount;
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".$amount."</td>";
    $layout .= "</tr>";
  }
  $layout.="<tr class='row '><td  class=' text-center col-sm-3 col-md-3 col-lg-3'><b>Total<b></td>";
  $layout.="<td  class='totals col-sm-3 col-md-3 col-lg-3'>".$total."</td>";
  $layout.="<td  class='text-center col-sm-3 col-md-3 col-lg-3'><b>Total Per Year<b></td>";
  $layout.="<td  class='totals col-sm-3 col-md-3 col-lg-3'>".$total_py."</td></tr>";
  $layout .= "</table>";
  // echo $layout;
  return $layout;
}
private function create_row2($array){
  $layout = "";
  $total = 0;
  $total_py = 0;
  for($i=0; $i<=count($array)-1; $i++){
    // $layout .= "<form action='POST' ";
    $layout .= "<tr value='".$array[$i]['EXPENSE_NUMBER']."' class='row' data-toggle='modal'
    data-target='#expenseModal'>";
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".strtoupper($array[$i]['product_name'])."</td>";
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['rand_value']."</td>";
    $total+=$array[$i]['rand_value'];
    $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['frequency']."</td>";
    $amount = $array[$i]['rand_value'] * $array[$i]['frequency'];
    $total_py += $amount;
    $layout.="<td   class='col-sm-3 col-md-3 col-lg-3'>".$amount."</td>";
    $layout .= "</tr>";
  }
  $layout.="<tr class='row' ><td  ><b>Total<b></td>";
  $layout.="<td  class='totals col-sm-3 col-md-3 col-lg-3'>".$total."</td>";
  $layout.="<td  class='col-sm-3 col-md-3 col-lg-3'><b>Total Per Year<b></td>";
  $layout.="<td  class='totals col-sm-3 col-md-3 col-lg-3'>".$total_py."</td></tr>";
  $layout .= "</table>";
 
  // echo $layout;
  return $layout;
}

private function summary_layout($result){//takes in the array of whichever expense table and 
  if(empty($result)){
    echo "<p class='text-capitalize text-center h1' >No Expenses Yet, fill in your expense summary report and keep track of it here.</p>";
  }else{
  $direct_expenses_array = [];
  $non_direct_expenses_array = [];

  for($i=0; $i<=count($result)-1; $i++){
   
    if($result[$i]['type_of_expense']==0){//direct expense = 0, non-direct = 1 populates the arrays accordingly
      array_push($direct_expenses_array, $result[$i]);
    }
    else if($result[$i]['type_of_expense']==1 ){
      array_push($non_direct_expenses_array, $result[$i] );
    }
    else{
      echo "Something wrong happened";
      exit();
    }
  }
  $direct_expense_row = $this->create_row($direct_expenses_array);
  $non_direct_expense_row = $this->create_row($non_direct_expenses_array);
  if(empty($direct_expense_row)){
    echo "<p class='text-capitalize text-center h1' >No Requests Yet</p>";
    exit();
  }
  $layout = "<p class='text-capitalize text-center h2' >Direct Expenses</p><br><table id='direct_expenses_summary' class='table table-responsive table-bordered '>";
  $layout .= "<tr class='row'>
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Expense</b>
                </th> 
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Amount (R)</b>
                </th>
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Frequency<b>
                </th>
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Value</b>
                </th>
                </tr>";
  if(empty($direct_expense_row)){
    echo "<p class='text-capitalize text-center h1' >No Direct Expenses Yet</p>";
    exit();
  }else{
    $layout .= $direct_expense_row;
  }
  $layout .= "<br><p class='text-capitalize text-center h2' >Non-Direct Expenses</p><br><table  id='non_direct_expenses_summary' class='table table-responsive table-bordered'>";
  $layout .= "<tr class='row'>
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Expense</b>
                </th> 
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Amount (R)</b>
                </th>
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Frequency<b>
                </th>
                <th  class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Value</b>
                </th>
                </tr>";
  if(empty($non_direct_expense_row)){
    echo "<p class='text-capitalize text-center h1' >No Non-Direct Expenses Yet</p>";
    exit();
  }else{
    $layout .= $non_direct_expense_row;
  }
  
  $layout .= "</div>";
  // echo $layout;
  return $layout;
}
  }
  
private function summary_layout_two($result){//takes in the array of whichever expense table and 
    $direct_expenses_array = [];
    $non_direct_expenses_array = [];
    for($i=0; $i<=count($result)-1; $i++){
      if($result[$i]['type_of_expense']==0){//direct expense = 0, non-direct = 1
        array_push($direct_expenses_array, $result[$i]);
      }
      else if($result[$i]['type_of_expense']==1){
        array_push($non_direct_expenses_array, $result[$i] );
      }
      else{
        echo "Something wrong happened";
        exit();
      }
    }
    $direct_expense_row = $this->create_row2($direct_expenses_array);
    $non_direct_expense_row = $this->create_row2($non_direct_expenses_array);
    
    $layout = "<tr class='row'>
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Expense</b>
                  </td> 
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Amount (R)</b>
                  </td>
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Frequency</b>
                  </td>
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Value</b>
                  </td>
                  </tr>";
    $layout .= $direct_expense_row;
    $layout .= '<h4 class="text-center">Non-Direct Expenses</h4>';
    $layout .= "</table><br><table class='table table-striped' >";
    $layout .= "<tr class='row'>
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Expense</b>
                  </td> 
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Amount (R)</b>
                  </td>
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                    <b>Frequency</b>
                  </td>
                  <td class='col-sm-3 col-md-3 col-lg-3'>
                  <b>Value</b>
                  </td>
                  </tr>
                  ";
    $layout .= $non_direct_expense_row;
    $layout .= "</table></div>";
    // echo $layout;
    return $layout;
    }
    
  

private function calculate_averages($values){
  $sum = 0;
  for($i=0; $i<=count($values)-1; $i++){
    $sum .= $values[$i];
  }
  $average = ($sum/count($values))*1.0;
  return $average;
}


//analytics
public function PROGRESS_PROCESS_SELECT($identifier=null){
    
  $query=$this->master->select_multiple_async($this->PROGRESS_PROCESS_SELECT[0], $this->var5);
  if(!$query){
      "Flop";
      exit();
    }else{
      $result=$this->master->getResult();
      if($identifier == 1){
        return $result[0];
      }
      //echo "['requests',".$result[0]['requests']."],['connections',".$result[0]['connections']."],['finalized',".$result[0]['finalized']."]";
      // print_r($result[0]);
      //exit();
      echo json_encode($result[0]);
    }
}
public function PROCESS_AVERAGE_TIME_SELECT(){

  $query=$this->master->select_multiple_async($this->PROCESS_AVERAGE_TIME_SELECT[0], $this->var5);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      if(empty($result)){
        echo "Result is empty";
        exit();
      }
      // print_r($this->PROGRESS_PROCESS_SELECT(1));
      // exit();
       VIEW::myBBBEE($result, $this->PROGRESS_PROCESS_SELECT(1));
      
    }
}
public function PAGE_VISITS_GRAPGH(){
  
  $query=$this->master->select_multiple_async($this->PAGE_VISITS_GRAPGH[0], $this->var5);
  if(!$query){
      echo "Flop page_visits";
      exit();
    }
    else{
      $result=$this->master->getResult();
      echo json_encode($result);
    }
}
public function display_user_statistics(){

  $smme = $this->user_statistics_smme();
  $company = $this->user_statistics_company();
  $total = $this->user_statistics_total();
  $current_day_searches = $this->CURRENT_DAY_SEARCHES_SELECT();
  // $all_emails_sent = $this->ALL_EMAILS_SENT_SELECT();
  // $all_clicked_email = $this->ALL_CLICKED_EMAILS_SELECT();
  

  VIEW::total_users_stats($smme, $company, $total,$current_day_searches);
  
}
private function user_statistics_company(){
  $query=$this->master->select_multiple_async($this->TOTAL_COMPANY_USERS[0], $this->var5);
  if(!$query){
       ECHO "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0]['entities'];
    }
}
private function user_statistics_smme(){
  $query=$this->master->select_multiple_async($this->TOTAL_SMME_USERS[0], $this->var5);
  if(!$query){
      echo "Flop 1";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0]['entities'];
    }
}
private function user_statistics_total(){
  $query=$this->master->select_multiple_async($this->TOTAL_NUMBER_USERS[0], $this->var5);
  if(!$query){
      ECHO "Flop3";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0]['SUM(e.entities)'];
    }
}
public function page_visits(){
  $min = $this->MIN_PAGE_VISITS();
  $max = $this->MAX_PAGE_VISITS();
  $average = $this->MIN_PAGE_VISITS();
  // echo "min ". print_r($min);
  // echo "max ". print_r($max);
  // echo "av ". print_r($average);
  // exit();
  VIEW::page_visits($min, $max, $average);
  
}
private function MAX_PAGE_VISITS(){
  $query=$this->master->select_multiple_async($this->MAX_PAGE_VISITS_SELECT[0], $this->var5);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
     
      return $result;
    }
}
private function MIN_PAGE_VISITS(){
  $query=$this->master->select_multiple_async($this->MIN_PAGE_VISITS_SELECT[0], $this->var5);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result;

    }
}
private function AVERAGE_PAGE_VISITS(){
  $query=$this->master->select_multiple_async($this->AVERAGE_PAGE_VISITS_SELECT[0], $this->var5);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      
      return $result;

    }
}
public function SEARCH_GRAPGH_SELECT(){
  $query=$this->master->select_multiple_async($this->SEARCH_GRAPGH_SELECT[0], $this->var5);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      // print_r($result);
      // exit();
      $new_array = array();
      for($i = 0; $i<=count($result)-1;$i++){
        array_push($new_array, array($result[$i]['term_name'],$result[$i]['SUM(s.Searches)'] ));
      }
      
      echo json_encode($new_array);
    }
}
public function search_terms(){
 
  $most_searched_name = $this->MOST_SEARCHED_NAME_SELECT();
  
  $most_searched_industry = $this->MOST_SEARCHED_INDUSTRY();

  $most_searched_product = $this->MOST_SEARCHED_PRODUCT();
 
  VIEW::search_stats($most_searched_name, $most_searched_industry, $most_searched_product);
 
}
private function MOST_SEARCHED_NAME_SELECT(){
  $query=$this->master->select_multiple_async($this->MOST_SEARCHED_NAME_SELECT[0], $this->var5);
  if(!$query){
      echo "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result;
    }
}
private function MOST_SEARCHED_INDUSTRY(){
  $query=$this->master->select_multiple_async($this->MOST_SEARCHED_INDUSTRY[0], $this->var5);
  if(!$query){
      echo "Flop 2";
      exit();
    }
    else{
      $result=$this->master->getResult();
      
      return $result;
    }
}
private function MOST_SEARCHED_PRODUCT(){
  $query=$this->master->select_multiple_async($this->MOST_SEARCHED_PRODUCT[0], $this->var5);
  if(!$query){
      echo "Flop 3";
      exit();
    }else{
      $result=$this->master->getResult();
      return $result;
    }
}


public function com_products_update($product, $productdes,  $productprice,$proudctid){

  $check = array($product);
  val::checkempty($check);


  $sql = array();
  $types = array();
  $tables = array();
  $params= array();
  for($i = 0; $i < count($product); $i++){
    array_push($sql,$this->COMPANY_UPDATE[0]);
    array_push($types,$this->COMPANY_UPDATE[1]);
    $set =array($product[$i], $productdes[$i] , $productprice[$i], session::get($this->id), $proudctid[$i]);
    array_push($params, $set);
    array_push($tables, "products");
  }
  $query=$this->master->transactionUpdate($tables, $sql,$types, $params);
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/edit.php?result=success");
        exit();
      
    }

}

public function deletecomproduct($action){


  $sql = $this->COMPANY_DELETE_PRODUCT[0];
  $types = $this->COMPANY_DELETE_PRODUCT[1];
  $params = array($action,session::get($this->id));
  $query = $this->master->update("products",$sql, $types, $params);

  if(!$query){
    header("location: ../".$this->classname."/index.php?error=databaseError2");
    exit();
  }else{
    
      header("location: ../".$this->classname."/edit.php?result=deleted");
      exit();
    
  }
  echo $action;


}

private function CURRENT_DAY_SEARCHES_SELECT(){
  $query=$this->master->select_multiple_async($this->CURRENT_DAY_SEARCHES_SELECT[0], $this->var5);
  if(!$query){
      echo "Flop1";
      exit();
    }
    else{
      $result=$this->master->getResult();
      // print_r($result[0]['current_searches']);
      // exit();
      return $result[0]['current_searches'];
    }
}
private function ALL_EMAILS_SENT_SELECT(){
  $query=$this->master->select_multiple_async($this->ALL_EMAILS_SENT_SELECT[0], $this->var5);
  if(!$query){
      echo "Flop2";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result;
    }
}
private function ALL_CLICKED_EMAILS_SELECT(){
  $query=$this->master->select_multiple_async($this->ALL_CLICKED_EMAILS_SELECT[0], $this->var5);
  if(!$query){
      echo "Flop3";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result;
    }
}


private function SYSTEM_CONNECTIONS_GRAPGH(){
  $query=$this->master->select_multiple_async($this->SYSTEM_CONNECTIONS_PER_MONTH_SELECT[0], DB_NAME_5);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      if(empty($result)){
        return -1;
      }else{
      // print_r($result);
      // exit();
      $new_array = array();
      for($i = 0; $i<=count($result)-1;$i++){
        array_push($new_array, array($result[$i]['Month'],$result[$i]['connections'] ));
      }
      
      return $new_array;
    }
    }
}
public function ENTITY_CONNECTIONS_GRAPGH(){
  $query = $this->master->select_prepared_async($this->CONNECTIONS_PER_MONTH_SELECT[0], DB_NAME_5, $this->CONNECTIONS_PER_MONTH_SELECT[1], array(session::get($this->id)));
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      // print_r($result);
      // exit();
      $new_array = array();
      $system_connection = $this->SYSTEM_CONNECTIONS_GRAPGH();
      array_push($new_array, $system_connection);
      for($i = 0; $i<=count($result)-1;$i++){
        array_push($new_array, array($result[$i]['Month'],$result[$i]['connections'] ));
      }
      print_r(json_encode($new_array));
    }
}
public function  SEARCH_GRAPGH(){
  $name = $this->getLegalName();
$industry =$this->getIndustry();
$keywords =$this->getKeywords();
$products =$this->getProducts();
$product_hits = $this->Search_PercomanceQuery($products);
$name_hits = $this->Search_PercomanceQuery($name);
$industry_hits = $this->Search_PercomanceQuery($industry);
$keyword_hits = $this->Search_PercomanceQuery($keywords, 1);
$new_array_percentages = array();
$new_array_percentages["products"] = $product_hits[0]['hits'];
$new_array_percentages["Name"] = $name_hits[0]['hits'];
$new_array_percentages["keywords"] = $keyword_hits[0]['hits'];
$new_array_percentages["industry"] = $industry_hits[0]['hits'];
print_r(json_encode($new_array_percentages));
exit();
}

private function Search_PercomanceQuery($result, $keyword_ = null){
  $new_query = "";
  $sql_front = "SELECT COUNT(`TERM_ID`) AS hits FROM yasccoza_openlink_smmes.search_terms WHERE `term_name` LIKE ? "; 
  
  $last = array_pop($result);
  
  if(isset($keyword_)){
    $params = array($last['keyword'], $last['keyword']);
  }else{
    $params = array($last['result'], $last['result']);
  }
  
  $types="ss";
  $sql_back = "SELECT COUNT(`TERM_ID`) AS hits FROM yasccoza_openlink_companies.search_terms WHERE `term_name` LIKE ? ";
  if(count($result) == 0){
    $new_query= $sql_front." UNION ".$sql_back;
  }else{
    for($i = 0; $i < count($result); $i++){
      $sql_front .= "OR `term_name` LIKE ? ";
      $sql_back .= "OR `term_name` LIKE ? ";
      $types.= "ss";
      if(isset($keyword_)){
        array_push($params, $result[$i]['keyword']);
        array_push($params, $result[$i]['keyword']);
        
      }else{
        array_push($params, $result[$i]['result']);
        array_push($params, $result[$i]['result']);
       }//print_r($params)
      
    }
    $new_query= $sql_front."UNION ".$sql_back;
  }
  $actual_query = "SELECT SUM(a.hits) AS hits FROM(".$new_query.") a";
  // if(isset($keyword_)){ print_r($actual_query); exit();}
  $query = $this->master->select_prepared_async($actual_query, $this->var, $types, $params);
  if(!$query){
      "Flop";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result;
    }
}

public function KEYWORD_PERFORMANCE(){
  $result = $this->getKeywords();
  if(empty($result)){
      echo "Flop2";
      exit();
    }
    else{
      $keyword_hits = array();
      
      // print_r(json_encode($result));
      // exit();
      $new_array_percentages = array();
      $keywordList = array();
      for($i=0;$i<=count($result)-1;$i++){
        $params = array($result[$i]["keyword"],$result[$i]["keyword"]);
        array_push($keywordList, $result[$i]);
        $query1 = $this->master->select_prepared_async($this->KEYWORD_HITS_SELECT[0], $this->var, $this->KEYWORD_HITS_SELECT[1], $params);
        
        if(!$query1){
         
          exit();
        }else{
          $result2=$this->master->getResult();
          array_push($keyword_hits, $result2[0]["hits"]);
        }
      }
      if(!empty($keyword_hits)){
        $total = array_sum($keyword_hits);
        $length = count($keyword_hits);
        $i =0;
        while($i < $length){
        
          $percentage = ceil(($keyword_hits[$i]/$total)*100);
        
        
        
        $new_array_percentages[$keywordList[$i]['keyword']] = $keyword_hits[$i];
        
        $i++;
      }
      
    
      // return $new_array_percentages;
      print_r(json_encode($new_array_percentages));
      exit();
      }else{
        print_r(1);
        exit();
      }
      
     
    }
}
private function getProducts(){
  
  $query1 = $this->master->select_prepared_async($this->PRODUCT_NAME_SELECT[0], $this->var, $this->PRODUCT_NAME_SELECT[1], array(session::get($this->id)));
  if(!$query1){
    exit();
  }else{
    $result=$this->master->getResult();
    return $result;
  }
}
private function getIndustry(){
  
  $query1 = $this->master->select_prepared_async($this->INDUSTRY_NAME_SELECT[0], $this->var, $this->INDUSTRY_NAME_SELECT[1], array(session::get($this->id)));
  if(!$query1){
    exit();
  }else{
    $result=$this->master->getResult();
    return $result;
  }
}
private function getLegalName(){
  
  $query1 = $this->master->select_prepared_async($this->LEGAL_NAME_SELECT[0], $this->var, $this->LEGAL_NAME_SELECT[1], array(session::get($this->id)));
  if(!$query1){
    exit();
  }else{
    $result=$this->master->getResult();
    return $result;
  }
}
private function getKeywords(){
  
  $query = $this->master->select_prepared_async($this->KEYWORD_ANALYTICS_SELECT[0], $this->var, $this->KEYWORD_ANALYTICS_SELECT[1], array(session::get($this->id)));
  if(!$query){
      echo "Flop2";
      exit();
    }
    else{
      $keyword_hits = array();
      $result=$this->master->getResult();
      return $result;
    }
}
public function analytics_head(){
  $requests_made = $this->requests_made();
  $connections = $this->in_progress();
  $requests_received = $this->requests_received();
  $finalised = $this->finalised_connections();
  echo ANALYTICS_VIEW::BBBEE_stats($requests_made, $requests_received, $connections,$finalised);
}
public function marketplace_head(){
  $views = $this->num_views();
  $web_visits = $this->web_visits();
  $intention_to_engage = $views["views"] + $web_visits["web_visits"];
  $enganged = $this->engaged();
  echo ANALYTICS_VIEW::marketplace($views, $web_visits,$intention_to_engage, $enganged );
}
private function requests_made(){
  $sql = $this->TOTAL_NUMBER_REQUESTS_SELECT[0];
  $types = $this->TOTAL_NUMBER_REQUESTS_SELECT[1];
  $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0];
    }
}
private function requests_received(){
  $sql = $this->TOTAL_NUMBER_REQUESTS_RECEIVED_SELECT[0];
  $types = $this->TOTAL_NUMBER_REQUESTS_RECEIVED_SELECT[1];
  $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0];
    }
}
private function in_progress(){
  $sql = $this->TOTAL_NUMBER_CONNECTIONS_SELECT[0];
  $types = $this->TOTAL_NUMBER_CONNECTIONS_SELECT[1];
  $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0];
    }
}
private function finalised_connections(){
  $sql = $this->FINALISED_SELECT[0];
  $types = $this->FINALISED_SELECT[1];
  $query = $this->master->select_prepared_async($sql, DB_NAME_5, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0];
    }
}
private function num_views(){
  $sql = $this->MORE_INFO_VIEWS_SELECT[0];
  $types = $this->MORE_INFO_VIEWS_SELECT[1];
  $query = $this->master->select_prepared_async($sql, $this->var, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      
      return $result[0];
    }
}
private function web_visits(){
  $sql = $this->WEBSITELINK_VIEWS_SELECT[0];
  $types = $this->WEBSITELINK_VIEWS_SELECT[1];
  $query = $this->master->select_prepared_async($sql, $this->var, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0];
    }
}
private function engaged(){
  $sql = $this->ENGAGED_ANALYTICS_SELECT[0];
  $types = $this->ENGAGED_ANALYTICS_SELECT[1];
  $query = $this->master->select_prepared_async($sql, $this->var, $types, array(session::get($this->id)));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      return $result[0];
    }
}
private function profile_stats_select(){
  $sql = $this->PROFILE_STATS_SELECT[0];
  $types = $this->PROFILE_STATS_SELECT[1];
  $id = session::get($this->id);
  if($this->classname == "SMME"){
    $query = $this->master->select_prepared_async($sql, $this->var, $types, array($id, $id, $id, $id));
  }else{
    $query = $this->master->select_prepared_async($sql, $this->var, $types, array($id, $id));
  }
  //$query = $this->master->select_prepared_async($sql, $this->var, $types, array($id, $id, $id, $id));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      $incomplete = array();
      $complete = array();
      foreach($result[0] as $key => $value ){
        if(is_null($value)){
          array_push($incomplete, array($key => 0));
        }else{
          array_push($complete, array($key => 1));
        }
      }
      
      $final_array = array($incomplete, $complete);
      // print_r($final_array);
      
      // exit();
     
      
      return $final_array;
    }
}
public function ENTITY_PROFILE_STATS(){
      
      // 
      if($this->classname == "COMPANY"){
        $result = $this -> profile_stats_select();
        echo ANALYTICS_VIEW::company_profile_stats_view($result);
      }else{
        $result = $this -> profile_stats_select();
        echo ANALYTICS_VIEW::smme_profile_stats_view($result);
      }
      
}

/* Review and update code here */

private function fetchCOMPANYData(){
  $sql = array($this->REVIEW_ADMIN_SELECT[0],$this->REVIEW_REGISTER_SELECT[0],$this->REVIEW_KEYWORDS_SELECT[0],$this->REVIEW_LINKS_SELECT[0],$this->REVIEW_STATEMENTS_SELECT[0],$this->REVIEW_PRODUCTS_SELECT[0] );
  $types = array($this->REVIEW_ADMIN_SELECT[1],$this->REVIEW_REGISTER_SELECT[1], $this->REVIEW_KEYWORDS_SELECT[1], $this->REVIEW_LINKS_SELECT[1],$this->REVIEW_STATEMENTS_SELECT[1],$this->REVIEW_PRODUCTS_SELECT[1] );
  $id = array(session::get($this->id));
  $params = array($id, $id, $id, $id, $id, $id);
  $which = array($this->var, $this->var, $this->var, $this->var, $this->var, $this->var);
  $query = $this->master->transactionSelect($which,$sql, $types, $params);
  if(!$query){
    //print_r($query);
      echo "";
      exit();
    }
    else{
      return $query;
    }
}
private function fetchFiles($id){
  $sql = $this->FILEUPLOAD_SELECT[0];
  $types = $this->FILEUPLOAD_SELECT[1];

  
  

  $query = $this->master->select_prepared_async($sql,"yasccoza_openlink_companies", $types, array($id, $id));

  if(!$query){
  header("location: ../index.php?error=failedtofetchmoreinfopo");
  exit();
  }else{
    $result = $this->master->getResult();
    return $result;
  }
  
}

private function fetchCONSULTANTData(){
  $sql = array($this->REVIEW_ADMIN_SELECT[0]);
  $types = array($this->REVIEW_ADMIN_SELECT[1]);
  $id = array(session::get($this->id));
  $params = array($id);
  $which = array($this->var);
  $query = $this->master->transactionSelect($which,$sql, $types, $params);
  if(!$query){
    //print_r($query);
      echo "";
      exit();
    }
    else{
      return $query;
    }
}
private function fetchADMINData(){
  $sql = array($this->ADMIN_SELECT[0]);
  $types = array($this->ADMIN_SELECT[1]);
  $id = array(session::get($this->id));
  $params = array($id);
  $which = array($this->var);
  $query = $this->master->transactionSelect($which,$sql, $types, $params);
  if(!$query){
    //print_r($query);
      echo "";
      exit();
    }
    else{
      return $query;
    }
}



private function fetchSMMEData(){
  $sql = array($this->SMME_ADMIN_SELECT[0],$this->SMME_REGISTER_SELECT[0],$this->SMME_DIRECTOR_SELECT[0],$this->SMME_STATEMENT_SELECT[0] , $this->SMME_DOCUMENTATION_SELECT[0],$this->SMME_PRODUCTS_SELECT[0],$this->SMME_KEYWORDS_SELECT[0], $this->SMME_LINK_SELECT[0]);

  $types = array($this->SMME_ADMIN_SELECT[1],$this->SMME_REGISTER_SELECT[1],$this->SMME_DIRECTOR_SELECT[1],$this->SMME_STATEMENT_SELECT[1] , $this->SMME_DOCUMENTATION_SELECT[1],$this->SMME_PRODUCTS_SELECT[1],$this->SMME_KEYWORDS_SELECT[1], $this->SMME_LINK_SELECT[1] );
  $id = array(session::get($this->id));
  $params = array($id, $id, $id, $id,$id, $id, $id, $id);

  $which = array($this->var, $this->var, $this->var, $this->var, $this->var, $this->var, $this->var, $this->var);
  $query = $this->master->transactionSelect($which,$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
      
      return $query;

    }
}

private function fetchLinkType($links){
  $sql = array();
  $types =array();
  $id = array();
  $which = array();
  $params = array();
  $count = count($links);
  for($i = 0; $i < count($links); $i++){
    array_push($sql, $this->LINK_NAME_SELECT[0]);
    array_push($types, $this->LINK_NAME_SELECT[1]);
    array_push($id, $links[$i]["LINK_ID"]);
    array_push($which, $this->var4);
    
  }
  while($count != 0){
    array_push($params, array($id[$count-1]));
    $count--;
  }
  $query = $this->master->transactionSelect($which,$sql, $types, $params);
  if(!empty($query) && !$query){
      echo "comp_requests_made query error 2";
      exit();
    }
    else{
      return $query;
    }
}

private function fetchSMMELinkType($links){
  $sql = array();
  $types =array();
  $id = array();
  $which = array();
  $params = array();
  $count = count($links);
  for($i = 0; $i < count($links); $i++){
    array_push($sql, $this->LINK_SMME_NAME_SELECT[0]);
    array_push($types, $this->LINK_SMME_NAME_SELECT[1]);
    array_push($id, $links[$i]["LINK_ID"]);
    array_push($which, $this->var4);
    
  }
  while($count != 0){
    array_push($params, array($id[$count-1]));
    $count--;
  }

  //print_r($sql);
  //exit();
  $query = $this->master->transactionSelect($which,$sql, $types, $params);
  if(!empty($query) && !$query){

      //echo "comp_requests_made query error 3";
      //exit();
    }
    else{
      return $query;
    }
}

public function displayCompanyReview(){
  $data = $this->fetchCOMPANYData();
  $admin_info = $data[0];
  $company_info = $data[1];
  $keywords = $data[2];
  $links = $data[3];
  $names = $this->fetchLinkType($links);
  $temp = new companyReview();
  $logo = session::get('ext');
  $statement = $data[4];
  $products = $data[5];
  $temp->display($admin_info, $company_info, $keywords, $links, $names,$statement, $products, $logo);
}
public function displayConsultantReview(){
  $data = $this->fetchCONSULTANTData();
  $admin_info = $data[0];
  $temp = new consultantReview();
  $logo = session::get('ext');
  $temp->display($admin_info, $logo);
}

public function displayAdminReview(){
  $data = $this->fetchADMINData();
  $admin_info = $data[0];
  $temp = new adminReview();
  $logo = session::get('ext');
  $temp->display($admin_info, $logo);
}



public function displayFiles($id){
  $id = token::decode($id);
  $data = $this->fetchFiles($id);
  $temp = new VIEW();
  $temp->displayFILES($data);
}
public function displaySMMEReview(){
  $data = $this->fetchSMMEData();
  $admin_info = $data[0];
  $company_info = $data[1];
  $director = $data[2];
  $statement = $data[3];
  $document = $data[4];
  $products = $data[5];
  $keywords = $data[6];
  $links = $data[7];
  $names= $this->fetchSMMELinkType($links);
  $temp = new smmeReview();
  $logo = session::get('ext');
  $temp->display($admin_info, $company_info, $director, $statement, $links, $document,$products,$keywords, $names, $logo);
}



public function displayAdminUpdate(){
  $data = $this->fetchCOMPANYData();
  $admin_info = $data[0];
  $temp = new companyEdit();
  $temp->adminForm($admin_info);
}
public function displayConsultantUpdate(){
  $data = $this->fetchCONSULTANTData();
  $temp = new consultantEdit();
  $temp->adminForm($data[0][0]);
}
public function displayAdminSMMEUpdate(){
  $data = $this->fetchSMMEData();
  $admin_info = $data[0];
  $temp = new smmeEdit();
  $temp->adminForm($admin_info);

}



public function displayREGISTERSMMEUpdate(){
  $data = $this->fetchSMMEData();
  $register_info = $data[1];
  $temp = new smmeEdit();
  $temp->registerForm($register_info);
 

}

public function displayCOMPANYUpdate(){
  $data = $this->fetchCOMPANYData();
  $statement = $data[4];
  $temp = new companyEdit();
  $temp->Statement($statement);
 

}

private function getIND_ID($title_id){
  $sql = $this->FETCH_INDUUSTRY_ID[0];
  $types = $this->FETCH_INDUUSTRY_ID[1];
  $query = $this->master->select_prepared_async($sql, $this->var, $types, array($title_id));
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();

      return $result[0]["INDUSTRY_ID"];

      
    }
}

public function displayDirectorSMMEUpdate(){
  $data = $this->fetchSMMEData();
  $director = $data[2];
  $temp = new smmeEdit();
  $temp->Director($director);
 

}

public function displayStatementSMMEUpdate(){
  $data = $this->fetchSMMEData();
  $statement = $data[3];
  $temp = new smmeEdit();
  $temp->Statement($statement);
 

}

public function displayCOMProducts(){
  $data = $this->fetchCOMPANYData();
  $products = $data[5];
  $temp = new companyEdit();
  $temp->products($products);

}

public function displayDocumentationSMMEUpdate(){
  $data = $this->fetchSMMEData();
  $documentation = $data[4];
  $temp = new smmeEdit();
  $temp->documentation($documentation);

}


public function   displayProducts(){
    $temp = '';
    $products = '';
    if(strcmp($this->classname, "SMME")==0){
        $data = $this->fetchSMMEData();
        $products = $data[5];
        $temp = new smmeEdit();
    }
    if(strcmp($this->classname, "COMPANY")==0){
        $data = $this->fetchCOMPANYData();
        $products = $data[5];
        $temp = new companyEdit();
    }
    $temp->products($products);
}

public function displaySMMEkeywords(){
  $data = $this->fetchSMMEData();
  $keywords = $data[6];
  $temp = new smmeEdit();
  $temp->keywordsForm($keywords);

}

public function displayRegisterUpdate(){
  $data = $this->fetchCOMPANYData();
  $register = $data[1];
  $temp = new companyEdit();
  $ind = $this->getIND_ID($register[0]["INDUSTRY_ID"]);
  $temp->registerForm($register, $ind);

}
public function displayKeywordsUpdate(){
  $data = $this->fetchCOMPANYData();
  $keywords = $data[2];
  $temp = new companyEdit();
  $temp->keywordsForm($keywords);

}
public function displayLinksUpdate(){
  $data = $this->fetchCOMPANYData();
  $links = $data[3];
  $names = $this->fetchLinkType($links);
  $temp = new companyEdit();
  $temp->linksForm($links, $names);

}

// public function adminUpdate($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
//   $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
//   val::checkempty($check);
//   array_push($check, session::get($this->id));
//   $query=$this->master->update("admin", $this->ADMIN_UPDATE[0],$this->ADMIN_UPDATE[1], $check);
   
//     if(!$query){
//       header("location: ../".$this->classname."/index.php?error=databaseError2");
//       exit();
//     }else{
      
//         header("location: ../".$this->classname."/edit.php?result=adminupdated");
//         exit();
      
//     }

// }

public function registerUpdate($legalname,$name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email, $foo, $industry,$financial_year){
  $check = array($legalname, $name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email,$foo, $industry, $financial_year,session::get($this->id));
  
 
  val::checkempty($check);
  
  $query=$this->master->update("register", $this->REGISTER_UPDATE[0],$this->REGISTER_UPDATE[1], $check);
   
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/edit.php?result=success");
        exit();
      
    }

}



public function linksUpdate($businesslink,$linktype, $ids){

  $check = array($ids);
  val::checkempty($check);

  $sql = array();
  $types = array();
  $tables = array();
  $params= array();
  $ids = explode(",",$ids);
  $businesslink = explode(",",$businesslink);
  
  for($i = 0; $i < count($ids); $i++){
    array_push($sql,$this->LINKS_UPDATE[0]);
    array_push($types,$this->LINKS_UPDATE[1]);
    $set =array($businesslink[$i], session::get($this->id), $ids[$i]);
    array_push($params, $set);
    array_push($tables, "business_links");
  }
  $query=$this->master->transactionUpdate($tables, $sql,$types, $params);
    if(!$query){
      print_r(-1);
        exit();
    }else{
        print_r(1);   
        exit();
    }

}

public function products_update($product, $productdes,  $productprice,$proudctid){

  $check = array($product);
  val::checkempty($check);


  $sql = array();
  $types = array();
  $tables = array();
  $params= array();
  for($i = 0; $i < count($product); $i++){
    array_push($sql,$this->PRODUCT_UPDATE[0]);
    array_push($types,$this->PRODUCT_UPDATE[1]);
    $set =array($product[$i], $productdes[$i] , $productprice[$i], session::get($this->id), $proudctid[$i]);
    array_push($params, $set);
    array_push($tables, "products");
  }
  $query=$this->master->transactionUpdate($tables, $sql,$types, $params);
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/edit.php?result=success");
        exit();
      
    }

}

public function keywordsUpdate($key_words, $ids){
  $check = array($key_words);
  val::checkempty($check);

  $ids = explode(',', $ids);
  $key_words = explode(',', $key_words);
  array_pop($ids);
  $sql = array();
  $types = array();
  $tables = array();
  $params= array();
  for($i = 0; $i < count($key_words); $i++){
    array_push($sql,$this->KEYWORDS_UPDATE[0]);
    array_push($types,$this->KEYWORDS_UPDATE[1]);
    $set =array($key_words[$i], $ids[$i], session::get($this->id));
    array_push($params, $set);
    array_push($tables, "keywords");
  }


  $query=$this->master->transactionUpdate($tables, $sql,$types, $params);
   
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/edit.php?result=keywordupdated");
        exit();
      
    }

}


public function smmekeywordsUpdate($key_words, $ids){
  $check = array($key_words);
  val::checkempty($check);

  $ids = explode(',', $ids);
  $key_words = explode(',', $key_words);
  array_pop($ids);
  $sql = array();
  $types = array();
  $tables = array();
  $params= array();
  for($i = 0; $i < count($key_words); $i++){
    array_push($sql,$this->KEYWORD__SMME_UPDATE[0]);
    array_push($types,$this->KEYWORD__SMME_UPDATE[1]);
    $set =array($key_words[$i], $ids[$i], session::get($this->id));
    array_push($params, $set);
    array_push($tables, "keywords");
  }


  $query=$this->master->transactionUpdate($tables, $sql,$types, $params);
   
    if(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError2");
      exit();
    }else{
      
        header("location: ../".$this->classname."/edit.php?result=keywordupdated");
        exit();
      
    }

}


public function DeleteLinks(){
  $sql = $this->LINKS_DELETE[0];
  $types = $this->LINKS_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("business_links",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}
public function DeleteAdmin(){
  $sql = $this->ADMIN_DELETE[0];
  $types = $this->ADMIN_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("admin",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}

public function smmeDeleteAdmin(){
  $sql = $this->SMME_ADMIN_DELETE[0];
  $types = $this->SMME_ADMIN_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("admin",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}

public function smmeDeleteRegister(){
  $sql = $this->SMME_REGISTER_DELETE[0];
  $types = $this->SMME_REGISTER_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("register",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}

public function smmeDeleteDir(){
  $sql = $this->SMME_DIR_DELETE[0];
  $types = $this->SMME_DIR_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("company_director",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}

public function smmeDeleteState(){
  $sql = $this->SMME_STATE_DELETE[0];
  $types = $this->SMME_STATE_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("company_profile",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}

public function companyDeleteState(){
  $sql = $this->COMPANY_STATE_DELETE[0];
  $types = $this->COMPANY_STATE_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("company_profile",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}

public function smmeDeleteDoc(){
  $sql = $this->SMME_DOC_DELETE[0];
  $types = $this->SMME_DOC_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("company_profile",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}


public function DeleteKeywords(){
  $sql = $this->KEYWORDS_DELETE[0];
  $types = $this->KEYWORDS_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("keywords",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
}
public function updateNotification($id){#1
    
  $sql = $this->NOTIFICATION_VIEWED[0];
  $types = $this->NOTIFICATION_VIEWED[1];
  $params = array($id);
  $this->master->update('yasccoza_openlink_association_db.notifications', $sql, $types, $params, 1);
print_r($params);
exit();
  return true;
     
}

public function DeleteRegister(){
  $sql = $this->REGISTER_DELETE[0];
  $types = $this->REGISTER_DELETE[1];
  $params = array(session::get($this->id));
  $query = $this->master->update("register",$sql, $types, $params);
  if(!$query){
    print_r($query);
      echo "";
      exit();
    }
    else{
        header("location: ../".$this->classname."/edit.php?result=deleted");
        exit();
    }
  
}
public function displayAdmins(){
  $sql = $this->DISPLAY_ADMIN_CHAT_SELECT[0];;
  $query = $this->master->select_multiple_async($sql, "yasccoza_openlink_admin_db");
  if(!$query){

  }else{
    $result = $this->master->getResult();
    
    if(strcmp($this->classname, "SMME")==0){
      $temp = new smmeReview();
      $temp->displayAdmins($result);
    }else{
      $temp = new companyReview();
      $temp->displayAdmins($result);
    }
  }
}

public function displayAdminsNavigation(){
// echo "here";
// exit();
  $sql = $this->ADMINS_NAVIGATION_SELECT[0];
  $query = $this->master->select_multiple_async($sql, "yasccoza_openlink_admin_db");
  if(!$query){

  }else{
    $result = $this->master->getResult();
    if(strcmp($this->classname, "SMME")==0){
      $temp = new smmeReview();
      $temp->filterDsiplay($result);
    }else{
      $temp = new companyReview();
      $temp->filterDsiplay($result);
    }
    
  }
}

public function ADMINS_FILTERED($filter, $condition){
    // print_r($filter);
    // echo "<br>";
    // print_r($condition);
  $where_cond = "";
  switch($filter){
    case "office":
      $where_cond = ", yasccoza_openlink_admin_db.admin_sector u
      WHERE u.ADMIN_ID = s.id
      AND u.OFFICE_ID = ".$condition;
      break;
    case "industry":
      $where_cond = ",  yasccoza_openlink_admin_db.admin_sector u
      WHERE u.ADMIN_ID = s.id
      AND u.INDUSTRY_ID = ".$condition;
      break;
    case "role":
      $where_cond = "WHERE s.type = '".$condition."'";
      break;

  }
  $sql ="SELECT * FROM yasccoza_tms_db.users s ".$where_cond;
//   print_r($sql);
//   exit();
  $query = $this->master->select_multiple_async($sql, "yasccoza_tms_db");
  if(!$query){
      echo "comp_requests_made query error";
      exit();
    }
    else{
      $result=$this->master->getResult();
      if(strcmp($this->classname, "SMME")==0){
        $temp = new smmeReview();
        $temp->displayAdmins($result);
      }else{
        $temp = new companyReview();
        $temp->displayAdmins($result);
      }
    }
}

public function verify_entity($id, $type){
  $sql = "";
 
  switch($type){
    case "BBBEE CERTIFICATE":
      $sql = "UPDATE yasccoza_openlink_association_db.entity_verification SET BBBEE_CERTIFICATE=1 WHERE USER=? ";
      break;
    case "REGISTRATION CERTIFICATE":
      $sql = "UPDATE yasccoza_openlink_association_db.entity_verification SET REGISTRATION=1 WHERE USER=? ";
      break;
    case "ID COPY":
      $sql = "UPDATE yasccoza_openlink_association_db.entity_verification SET IDENTITY_DOCUMENT=1 WHERE USER=? ";
      break;
    case "TAX CERTIFICATE":
      $sql = "UPDATE yasccoza_openlink_association_db.entity_verification SET TAX_CERTIFICATE=1 WHERE USER=? ";
      break;
    case "PHYSICAL LOCATION":
      $sql = "UPDATE yasccoza_openlink_association_db.entity_verification SET PHYSICAL_LOCATION=1 WHERE USER=? ";
      break;

  }
  $types = "i";
  $params = array($id);
  
  $query = $this->master->update("yasccoza_openlink_association_db",$sql, $types, $params);
  if(!$query){
    print_r($sql);
     print_r($params);
    
    }
    else{

      $sql2 = "UPDATE yasccoza_openlink_smmes.file_uploads SET verified=1 WHERE userID=? AND type=? ";
      $types2 = "is";
      $params2 = array($id, $type);
     
      $query = $this->master->update("yasccoza_openlink_association_db",$sql2, $types2, $params2);
      if(!$query){
        print_r($query);
          echo "";
          exit();
        }
        else{
            header("location: ../ADMIN/verify.php?result=success&id=".token::encode($id));

           
            exit();
        }
    }
}

public function printSMMEExcelData(){
  // Excel file name for download 
$fileName = "users_" . date('Y-m-d') . ".xls"; 
 
// Column names 
$fields = array('Legal Name', 'City','Province', 'Industry', 'BBBEE', 'TYPE'); 
$sql ="SELECT DISTINCT register.Legal_name,register.foo, city, Province , BBBEE_Status, register.SMME_ID AS ID, s.typeOfEntity as T,  IT.title, i.office
FROM yasccoza_openlink_smmes.signup as s, yasccoza_openlink_smmes.register, yasccoza_openlink_smmes.company_documentation, yasccoza_openlink_smmes.company_profile, yasccoza_openlink_association_db.industry_title as IT, yasccoza_openlink_association_db.industry as i
WHERE s.SMME_ID=register.SMME_ID 
AND register.INDUSTRY_ID = IT.TITLE_ID
AND IT.INDUSTRY_ID = i.INDUSTRY_ID
AND register.SMME_ID=company_documentation.SMME_ID 
AND company_documentation.SMME_ID=company_profile.SMME_ID";
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
$query=$this->master->select_multiple_async($this->TOVIEW1_SELECT1[0], $this->var2);
// Fetch records from database 
if(!$query){

}else{
  $result = $this->master->getResult();


  for($i =0; $i < count($result); $i++){
    $address = $result[$i]['city'];
    $province= $result[$i]['Province'];
    $lineData = array($result[$i]['Legal_name'], $address, $province, $result[$i]['office'], $result[$i]['BBBEE_Status'], $result[$i]['T']); 
    // array_walk($lineData, 'filterData'); 
    $excelData .= implode("\t", array_values($lineData)) . "\n"; 
  }
}
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 

 
// header("location: ../ADMIN/verifySMME.php?page=1");
exit();
}

}
?>