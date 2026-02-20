 <?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/Session.php');
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../helpers/val.php');
include_once($filepath.'/../helpers/token.php');
include_once($filepath.'/../view/admin_view/view.php');
include_once($filepath.'/../view/analytics_view/analytics_view.php');
define("FILEPATH", $filepath);

abstract class Generic{
  protected $master;
  function __construct(){
    $this->master=new Master($this->var);
  }
  public function SignUp($sname, $surname, $username, $email, $password, $passwordRepeat, $terms_policies, $returnurl){
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
      header("location: ../".$this->classname."/login.php?error=usernametaken= ".$xi);
      exit();
    }elseif(!$query){
      header("location: ../".$this->classname."/login.php?error=databaseError1");
      exit();
    }else{
      val::checkpasswords($password, $passwordRepeat);
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
    header("location: ../".$this->classname."/login.php");
  }
      }
    }
}

  //login function
  public function Login($userName, $password, $returnurl){
    val::checkempty(array($userName,$password));
    $query=$this->master->select("signup", $this->LOGIN_SELECT[0], $this->LOGIN_SELECT[1], array($userName, $this->classname));
    if(!$query) {
      header("location: ../".$this->classname."/login.php?error=databaserror");
      exit();
    }
    $result=$this->master->getResult();
    $xi=$this->master->numRows();
    if($xi==0){
      if(strpos($this->classname, "ADMIN")){
        header("location: ../ADMIN/login.php?error=InvalidUserNameOrPassword");
      }else{
        header("location: ../".$this->classname."/login.php?error=InvalidUserNameOrPassword");
      }
      exit();
    }
    $pwdcheck= password_verify($password, $result['Pwd']);
    if ($pwdcheck == false){
      if(strpos($this->classname, "ADMIN")){
        header("location: ../ADMIN/login.php?error=InvalidUserNameOrPassword");
      }else{
        header("location: ../".$this->classname."/login.php?error=InvalidUserNameOrPassword");
      }
      exit();
   }
   elseif($pwdcheck == true) {
    Session::init();
    $who = $result['typeOfEntity'];
    Session::set("WHO", $who);
    // if($who == "M_ADMIN"){
    //   Session::set($this->id, $result[substr($this->id, 2)]);
    // }else{
      
    // }
    Session::set($this->id, $result[$this->id]);
    $array =$this->pimg($result[$this->id]);
    Session::set('Name',$result['First_Name']);
    if($array['ext']!==null){
      Session::set('ext', $array['ext']);
    }
    token::create_session_key();
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
    header("location: ../ADMIN/index.php?id=".Session::get($this->id)."&r_is=".$returnurl);
  }else{
    header("location: ../".$this->classname."/index.php?id=".Session::get($this->id)."&r_is=".$returnurl);
  }

  exit();
}
} else {
  header("location: ../Home.php?error");
  exit();
}
}

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
      header("location: ../home.php?error=alreadyuploaded");
      exit();
    }
    elseif(!$query){
    
     header("location: ../home.php?error=databaseErrorTHIS1");
      exit();
    }else{
      $regInsertSQL = $this->REGISTER_INSERT[0];
      $regInsertTypes = $this->REGISTER_INSERT[1];
      $query = $this->master->insert($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $register_values);
      if(!$query){
    //      echo "SQl => ".$regInsertSQL;
    //  echo "<br> Types => ".$regInsertTypes;
    //  echo "<br> ID => ".$register_values;
        header("location: ../home.php?error=databaseErrorINSERT");
        exit();
      }
      header("location: ../".$this->classname."_userProfile.php?success");
      exit();
    }
  }

  public function register($name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email, $industry){
    $register_values = array(
      $name,
      $RegNum,
      $Address,
      $Postal,
      $City,
      $Province,
      $Contact,
      $email,
      $industry,
      session::get($this->id)
    );
      val::checkempty($register_values);
      $select = array(session::get($this->id));
      $insertSelectTableRegister = "register";
      $regInsertSQL = $this->REGISTER_SELECT[0];
      $regInsertTypes = $this->REGISTER_SELECT[1];
      $query = $this->master->selectnonquery($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $select);
      $xi=$this->master->numRows();
      if(!$xi==0 && $query){
        header("location: ../".$this->classname."/company_info.php?result=exists");
        exit();
      }
      elseif(!$query){
        header("location: ../home.php?error=databaseError");
        exit();
      }else{
        $regInsertSQL = $this->REGISTER_INSERT[0];
        $regInsertTypes = $this->REGISTER_INSERT[1];
        $query = $this->master->insert($insertSelectTableRegister, $regInsertSQL, $regInsertTypes, $register_values);
        if(!$query){
          header("location: ../home.php?error=databaseError");
          exit();
        }
        header("location: ../".$this->classname."/company_info.php?result=success");
        exit();
      }
    }


  public function admin($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup){
    $check = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
    val::checkempty($check);
    $query=$this->master->selectnonquery("admin", $this->ADMIN_SELECT[0], $this->ADMIN_SELECT[1], array(session::get($this->id)));
    $xi=$this->master->numRows();
    if(!$xi==0 && $query){
      header("location: ../".$this->classname."/admin_info.php?result=usernametaken&email= ".$email);
      exit();
    }elseif(!$query){
      header("location: ../".$this->classname."/index.php?error=databaseError1");
      exit();
    }
    else{
      $insert = array($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, session::get($this->id));
      $query=$this->master->insert("admin", $this->ADMIN_INSERT[0],$this->ADMIN_INSERT[1], $insert);
     
      if(!$query){
        header("location: ../".$this->classname."/index.php?error=databaseError2");
        exit();
    }      header("location: ../".$this->classname."/admin_info.php?result=success");
    exit();
    }
}



  function addCompanyStatements($introduction, $vision, $mission, $values, $goals_objectives){
    $statements = array($introduction,$vision,$mission,$values,$goals_objectives);
    val::checkempty($statements);
    array_push($statements, session::get($this->id));
    $insertTable = "company_profile";
    $insertsql = $this->ADDCOMPANYSTATEMENTS_INSERT[0];
    $inserttypes = $this->ADDCOMPANYSTATEMENTS_INSERT[1];
    $query = $this->master->insert($insertTable, $insertsql, $inserttypes, $statements);
    if(!$query){
   
      header("location: ../".$this->classname."/company_statement.php?result=exists");
      exit();
    }else{
      header("location: ../".$this->classname."/company_statement.php?result=success");
          exit();
        }
  }


function addCompanyDocuments($Number_Shareholders, $Number_Black_Shareholders, $Number_White_Shareholders, $Black_Ownership_Percentage, $Black_Female_Percentage, $White_Ownership_percentage, $BBBEE_Status, $Date_Of_Issue, $Expiry_Date, $fileNamebbbee,$fileTmpNamebbbee,$fileSizebbbee,$fileErrorbbbee, $fileNamereg,$fileTmpNamereg,$fileSizereg,$fileErrorreg){

  $this->UploadFile();
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

    $insertDocumentsTable = "company_documentation";
    $insertsql = $this->ADDCOMPANYDOCUMENTS_INSERT[0];
    $inserttypes = $this->ADDCOMPANYDOCUMENTS_INSERT[1];
    $query = $this->master->insert($insertDocumentsTable, $insertsql, $inserttypes, $Documents);
    if(!$query){
      header("location: ../".$this->classname."/company_documentation.php?result=exists");
      exit();
    }else{
      header("location: ../".$this->classname."/company_documentation.php?result=success");
          exit();
        }
    }




    private function temp($id, $ext, $filedelete){
      $cur=$this->pimg($id);
      if ($cur!=='error'){
      $PIMGEXT=$cur['ext'];
      if($PIMGEXT !== "http://localhost/Project%20One/Images/Profiles/profile_image.png"){
      $what=unlink($filedelete);
        if(!$what){
          header("location: ../".$this->classname."_userProfile.php?error=Failedtodelete");
          exit();
        }
    }           
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



  public function UploadProfilePic($fileName,$fileTmpName,$fileSize,$fileError){
    //not sure what to do here
    $id=session::get($this->id);
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg','jpeg','png');

    if(in_array($fileActualExt, $allowed)){
        if($fileError== 0){
            if($fileSize < 20000000){
                $fileNameNew = $id.".".$fileActualExt;
                $fileDestination = 'http://localhost/Project%20One/Images/Profiles/'.$fileNameNew;	
                $filedelete="http://localhost/Project%20One/Images/Profiles/".$id.".*";
                $this->temp($id, $fileDestination, $filedelete);
                move_uploaded_file($fileTmpName, $fileDestination);
                header("Location: ../".$this->classname."_userProfile.php?upload=successful");
                exit();
            }
            else{
                header("location: ../".$this->classname."_userProfile.php?error=YourFileIsTooBig");
                exit();
            }
        }
        else{
            header("location: ../".$this->classname."_userProfile.php?error=ThereWasAnErrorUploadingYourFile");
            exit();
        }
    }
    else{
        header("location: ../".$this->classname."_userProfile.php?error=YouCannotUploadThisTypeOfFile");
        exit();
    }

  }

  protected function UploadFile($form,$fileName,$fileTmpName,$fileSize,$fileError,$fileType){
      $fileExt = explode('.', $fileName);
      $fileActualExt = strtolower(end($fileExt));
      $allowed = array('jpg','jpeg','png','pdf');
      if(in_array($fileActualExt, $allowed)){
          if($fileError== 0){
              if($fileSize < 2000000){
                  $fileNameDelete = token::encode1(session::get($this->id))."_".$form.".*";
                  $fileNameNew = token::encode1(session::get($this->id))."_".$form.".".$fileActualExt;
                  $fileDestination = '../Uploads/files/'.$form.'/'.$fileNameNew;
                  unlink($fileNameDelete);
                  move_uploaded_file($fileTmpName, $fileDestination);
                  return $fileNameNew;
              }
              else{
                  return false;
              }
          }
          else{
              return false;
          }
      }
      else{
          return false;
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

function Directors($name, $surname, $IdType,$IDNumber, $Gender, $EthnicGroup){
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
      $query=$this->master->insert("expense_summary", $sql, $types, $array);
      if(!$query){
        echo "Sql is -> ".$sql;
        echo "<br><br><br><br>";
        echo "Types is -> ".$types;
        echo "<br><br><br><br>";
        print_r($name);
        exit();
       }else{
        header("location: ../".$this->classname."_userProfile.php?success=".$this->master->connresult[0]);
        exit();
       }
  
  
    }else{
      header("location: ../home.php?error=invalidCredentials");
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
  if($a!==(count($serviceprovider)-1)){
        $sql.="(?,?,?,?,?,?,?), ";
      }else{
        $sql.="(?,?,?,?,?,?,?);";
      }
  
      $types.="sssiiii";
      
      array_push($array, $serviceprovider[$a], $productname[$a], $productspecification[$a], $randvalue[$a], $frequency[$a], $type, $id);
    }//print_r($serviceprovider);
    //echo count($serviceprovider)."<br><br>";
    echo $sql;
    echo "<br><br>";
    $query=$this->master->insert("expense_summary", $sql, $types, $array);
    if(!$query){
      header("location: ../home.php?error=databaseError2");
      exit();
     }else{
      header("location: ../".$this->classname."/Expense_summary.php?result=success");
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
      header("location: ../".$this->classname."/index.php?result=success");
      exit();
     }


  }else{
    header("location: ../home.php?result=exists");
    exit();
}

}




function products_services($productname,$productdes){

  if($id=session::get($this->id)){
    $sql=$this->PRODUCTS_INSERT[0];
    $array=array();
    $types="";
    for ($a = 0; $a < count($productname); $a++)
    {
  if($a!==(count($productname)-1)){
        $sql.="(?,?,?), ";
      }else{
        $sql.="(?,?,?);";
      }
  
      $types.="ssi";
      
      array_push($array,$productname[$a],$productdes[$a], $id);
    } 

    echo count($productname)."<br><br>";;
    echo $sql;
    echo "<br>";
    echo $types;
    echo "<br>";
    $query=$this->master->insert("products", $sql, $types, $array);
    if(!$query){
      // header("location: ../home.php?error=databaseError3");
      exit();
     }else{
      header("location: ../".$this->classname."/products_services.php?result=success");
      exit();
     }


  }else{
    header("location: ../".$this->classname."/products_services.php?result=exists");
    exit();
  }
}



function smme_links($businesslink,$linktype){

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

    echo count($businesslink)."<br><br>";;
    echo $sql;
    echo "<br>";
    echo $types;
    echo "<br>";
    $query=$this->master->insert("products", $sql, $types, $array);
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

    echo count($businesslink)."<br><br>";;
    echo $sql;
    echo "<br>";
    echo $types;
    echo "<br>";
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




function peek($where, $which){
  $id = session::get($this->id);
  if(!$id){
    echo 99;
    exit();
  }else{
    if($where==0 || $where==2){// 0-smme seeing companies; 2-company seeing smmes
        $sql1=$this->TOVIEW1_SELECT1[0];
        $temp1=$this->var2;
        $sql2=$this->TOVIEW1_SELECT2[0];
        $types2=$this->TOVIEW1_SELECT2[1];
        $sql3=$this->TOVIEW1_SELECT3[0];
        $types3=$this->TOVIEW1_SELECT3[1];
      }elseif($where==1){
        $sql1=$this->TOVIEW1_SELECT_2_1[0];
        $temp1=$this->var3;
        $sql2=$this->TOVIEW1_SELECT_2_2[0];
        $types2=$this->TOVIEW1_SELECT_2_2[1];
        $sql3=$this->TOVIEW1_SELECT_2_3[0];
        $types3=$this->TOVIEW1_SELECT_2_3[1];
      }else{
      echo "Invalid credentials";
      exit();
    }
    if($which==0){
      $query1=$this->master->select_multiple_async($sql1, $temp1);
      if(!$query1){
        echo "query 1 error";
        echo implode("", $this->master->connresult);
      }
    }elseif($which==1 || $which==2){
      $query2=$this->master->select_prepared_async($sql2, $temp1, $types2, array($which, $id));
      if(!$query2){
        echo "query 2 error";
        echo implode("", $this->master->connresult);
      }
    }else{
      //for comparative charts
    }
      $result=$this->master->getResult();
      if(empty($result)){
        echo "EMPTY";
        exit();
      }
      else{
        $x=array();
        foreach($result as $key => $val) {
          if($which==0){
          $x[$key] = $val["BBBEE_Status"];
          }elseif($which==3){
            //anchor for comparative charts
          }else{
            $x[$key] = $val["Progress"];
          }
       }
       if($x==session::get($this->classname."_Progress"[$which])){
         echo 1;
         exit();
       }else{
        session::set($this->classname."_Progress", $x);
        if($where==0 && $which!==0){
          $this->COMPANYloop($result);
        }elseif($which==0 && $where==0){
          $this->COMPANYAllTable($result);
        }elseif($which==0 && $where==2){
          $this->SMMEAllTable($result);
        }elseif($which==0 && $where==1){
          //npo all
        }
        elseif($where==1 && $which!==0){
          $this->NPOloop($result);
        }else{
          $this->SMMEloop($result);
        }
      }
    }
  }
}

function ToView_entity_REQUESTED($entity){
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
    if($this->classname == "COMPANY"){
      $this->SMMEloop($result);
     }elseif($this->classname == "SMME"){
      $this->COMPANYloop($result);
     }
  }
}

function ToView_entity_ALL(){
  $query=$this->master->select_multiple_async($this->TOVIEW1_SELECT1[0], $this->var2);
  if(!$query){
    echo "query 1 error";
    echo implode("", $this->master->connresult);
    exit();
  }else{
    $result=$this->master->getResult();
    if($this->classname == "COMPANY"){
      $this->SMMEAllTable($result);
      
    }elseif($this->classname == "SMME" || $this->classname == "NPO"){
      $this->COMPANYAllTable($result);
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
  echo "<th>Industry</th>";
  echo "<th>View More</th>";
  echo   "<th>Progress</th>";
  echo     "</tr>";
  echo   "</thead>";
  echo "<tbody>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Address"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]['title']."</td>";
    $progress;
    $progress_description;
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

    }
    echo '<td class="table-cell d-flex justify-content-center align-items-center" data-href="">';
    echo "<form method='POST' action='view_more.php?id=".token::encode($result[$i]["ID"])."'>"; 
    echo '<input type="text" name="tk" value=';
    token::get("VIEW_MORE_YASC");
    echo ' required="" hidden>';
    echo "<button type='submit' name='VIEW_MORE' class='btn ' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form></td>"; 
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>";
    echo "<div class='progress'>
            <div class='progress-bar rounded' role='progressbar' style='width: ".$progress."%' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100'><span class='text-center text-dark'>". $progress_description."</span></div>
          </div>";
    echo "</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";


  // echo '<table class="table-responsive">
  // <thead>
  //   <tr>
  //     <th class="text-center" scope= width="25%" style="background-color: #0a2a4efa; color: white;"> 1</th>
  //     <th class="text-center" style="border-top: 2px solid rgba(150, 147, 147, 0.212)" width="25%">2</th>
  //     <th class="text-center" width="25%" style="background-color: #0a2a4efa; color: white;">3</th>
  //     <th class="text-center" style="border-top: 2px solid rgba(150, 147, 147, 0.212)" width="25%">4</th>
  //   </tr>
  //   </thead>
  //   <tbody>';
  // for($i=0; $i<=count($result)-1; $i++){//row
  //   echo "<tr>";
  //  for($j=1; $j<=4; $j++){//cell in row
  //   echo '<td width="25%">';
  //    if($result[$i]['Progress']==$j){
  //     echo '<div class="col-md-12 col-sm-12 col-xs-12 profile_details">
  //     <div class="well profile_view">
  //       <div class="col-sm-12">
  //         <div class="col-xs-12 text-center">
  //         <img src="'. $result[$i]['ext'] .'" alt="" class="img-circle img-responsive">
  //         </div>
  //         <h2 class="brief text-center"><i>'. $result[$i]['Legal_name'].'</i></h2>
  //         <h4>'. $result[$i]['typeOfEntity'] .'</h4>
  //         <div class="left col-xs-12">
            
  //           <p style="width: fit-content;"><strong>Industry: </strong> '. $result[$i]['title'] .' </p>
  //           <ul class="list-unstyled">
  //             <li><i class="fa fa-building"></i> Address: '. $result[$i]['Address'] .' </li>
  //           </ul>
  //         </div>
  //       </div>
  //       <div class="col-xs-12 bottom text-center">
  //         <div class="col-xs-12 col-sm-12 col-md-6 emphasis">
  //           <p class="ratings">
  //             <a>'.$result[$i]['BBBEE_Status'].'</a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star-o"></span></a>
  //           </p>
  //         </div>
  //         <div class="col-xs-12 col-sm-12 col-lg-6 emphasis">
  //         <a href="messages.php?id='. $result[$i]['ID'] .'" class="btn btn-success btn-xs">
  //         <i class="fa fa-user">
  //     </i> <i class="fa fa-comments-o"></i> </a>
  //     <a href="view_more.php?id='. token::encode($result[$i]['ID'] ).'" class="btn btn-primary btn-xs">
  //     <i class="fa fa-user"> </i> View Profile
  //     </a>
  //         </div>
  //       </div>
  //     </div>
  //   </div>';
  //    }
  //   echo "</td>";
  //  }
  //  echo "</tr>";
  // }
  // echo "</tbody>";
  // echo "</table>";
}

// private function Tabloop(array $array3D){
  
//   echo "<div class='align-self-center' style='display: flex; margin:auto'>";
//   echo "<div class='container align-self-center'>";
//   echo   "<ul class='nav nav-pills'>";
//   echo     "<li class='active'><a data-toggle='pill' href='#menu1'>SMME Request</a></li>";
//   echo     "<li><a data-toggle='pill' href='#menu2'>BBBEE Request</a></li>";
//   echo     "<li><a data-toggle='pill' href='#home'>ALL</a></li>";
//   echo     "<li><a data-toggle='pill' href='#menu3'>mySMME Comparative Charts</a></li>";
//   echo   "</ul>";
//   echo   "<div class='tab-content'>";

//   echo     "<div id='menu1' class='tab-pane fade in active'>";
//   echo       "<h3>SMME REQUESTED</h3>";
//   echo       "<button id='Refresh2' class='btn btn-primary'>refresh</button>";
//   echo      "<div id='smmerequest'>";
//              if($this->classname == "COMPANY"){
//               $this->SMMEloop($array3D[1]);
//              }elseif($this->classname == "SMME"){
//               $this->COMPANYloop($array3D[1]);
//              }
//   echo      "</div>";
//   echo     "</div>";


//   echo     "<div id='menu2' class='tab-pane fade'>";
//   echo       "<h3>COMPANY REQUESTED</h3>";
//   echo       "<button id='Refresh3' class='btn btn-primary'>refresh</button>";
//   echo       "<div id='companyrequest'>";
//               if($this->classname == "COMPANY"){
//                 $this->SMMEloop($array3D[2]);
//               }elseif($this->classname == "SMME"){
//                 $this->COMPANYloop($array3D[2]);
//               }
//   echo      "</div>";
//   echo     "</div>";

  
//   echo     "<div id='home' class='tab-pane fade'>";
//   echo       "<h3>ALL</h3>";
//   echo       "<button id='Refresh1' class='btn btn-primary'>refresh</button>";
//   echo       "<div id='ALL'>";
//               if($this->classname == "COMPANY"){
//                 $this->SMMEAllTable($array3D[0]);
//               }elseif($this->classname == "SMME"){
//                 $this->COMPANYAllTable($array3D[0]);
//               }
//   echo       "</div>";
//   echo     "</div>";


//   echo     "<div id='menu3' class='tab-pane fade'>";
//   echo       "<h3>Comparative Chart</h3>";
//   echo          "<div id='compar_chart'></div>";
//   echo     "</div>";
//   echo   "</div>";
//   echo "</div>";
//   echo "</div>";
// }

private function SMMEAllTable(array $result){
echo "<table class='table-responsive table table-striped smme_entity_table' id='dataTable' width='100%' cellspacing='0'>";
echo  "<thead>";
echo     "<tr>";
echo     "<th></th>";
echo     "<th>Legal Name</th>";
echo     "<th>City</th>";
echo   "<th>Province</th>";
echo     "<th>BBBEE Status</th>";
echo     "<th>Profile</th>";
echo     "<th>Initiate</th>";
echo     "</tr>";
echo   "</thead>";
for($i=0; $i<=count($result)-1; $i++){//row
  echo "<tr>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</td>";
  echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Province"]."</td>";
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
  echo "<form method='Post' action='../main/main_notify.php?id=".token::encode($result[$i]["ID"])."'>";
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
}

private function COMPANYAllTable(array $result){
  echo "<table class='table-responsive table table-striped company_entity_table' id='dataTable' width='100%' cellspacing='0'>";
  echo  "<thead>";
  echo     "<tr>";
  echo     "<th></th>";
  echo     "<th>Legal Name</th>";
  echo     "<th>City</th>";
  echo   "<th>Province</th>";
  echo   "<th>Profile</th>";
  echo     "</tr>";
  echo   "</thead>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["city"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Province"]."</td>";
    $id = token::encode($result[$i]["ID"]);
    echo "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><form method='POST' action='view_more.php?id=".$id."'>"; 
    echo '<input type="text" name="tk" value=';
    token::get("VIEW_MORE_YASC");
    echo ' required="" hidden>';
    echo "<button type='submit' name='VIEW_MORE' class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='View More Information'><i class='fa fa-address-card'></i></button></form>";
    echo "<a href='chat.php?url=".token::encode($result[$i]["ID"])."'class='btn btn-primary' type='button'  data-toggle='tooltip' data-placement='top' title='Message'><i class='fa fa-envelope'></i></a></td>";
    echo "<td>";
    echo "<form method='Post' action='../main/main_notify.php?id=".token::encode($result[$i]["ID"])."'>";
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
  }
  echo "</table>";
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
  echo "<th>Industry</th>";
  echo   "<th>Progress</th>";
  echo     "</tr>";
  echo   "</thead>";
  echo "<tbody>";
  for($i=0; $i<=count($result)-1; $i++){//row
    echo "<tr>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''><img src=".$result[$i]["ext"]." width='50' height='50' ></td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Legal_name"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]["Address"]."</td>";
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>".$result[$i]['title']."</td>";
    $progress;
    $progress_description;
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

    }
    echo  "<td class='table-cell d-flex justify-content-center align-items-center' data-href=''>";
    echo "<div class='progress'>
            <div class='progress-bar rounded' role='progressbar' style='width: ".$progress."%' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100'><span class='text-center text-dark'>".$progress_description."</span></div>
          </div>";
    echo "</td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";

  // echo '<table class="table">
  // <thead>
  //   <tr>
  //     <th class="text-center" scope="row" width="25%" style="background-color: #0a2a4efa; color: white;"> 1</th>
  //     <th class="text-center" sstyle="border-top: 2px solid rgba(150, 147, 147, 0.212)" width="25%">2</th>
  //     <th class="text-center" width="25%" style="background-color: #0a2a4efa; color: white;">3</th>
  //     <th class="text-center" style="border-top: 2px solid rgba(150, 147, 147, 0.212)" width="25%">4</th>
  //   </tr>
  //   </thead>
  //   <tbody>';
  // for($i=0; $i<=count($result)-1; $i++){//row
  //   $id = base64_encode($result[$i]["ID"]);
  //   $type_of_entity = base64_encode($result[$i]["typeOfEntity"]); 
  //   echo "<tr>";
  //  for($j=1; $j<=4; $j++){//cell in row
  //   //#########MIGHT BE A PROBLEM #########//
  //    if($result[$i]['Progress']!==$j){
  //     echo '<td  width="25%">';
  //    }else{
  //     echo '<td  width="25%">';
  //     echo '<div class="col-md-12 col-sm-12 col-xs-12 profile_details">
  //     <div class="well profile_view">
  //       <div class="col-sm-12">
  //         <div class="col-xs-12 text-center">
  //         <img src="'. $result[$i]['ext'] .'" alt="" class="img-circle img-responsive">
  //         </div>
  //         <h2 class="brief text-center"><i>'. $result[$i]['Legal_name'].'</i></h2>
  //         <h4>'. $result[$i]['typeOfEntity'] .'</h4>
  //         <div class="left col-xs-12">
            
  //           <p style="width: fit-content;"><strong>Industry: </strong> '. $result[$i]['title'] .' </p>
  //           <ul class="list-unstyled">
  //             <li><i class="fa fa-building"></i> Address: '. $result[$i]['Address'] .' </li>
  //           </ul>
  //         </div>
  //       </div>
  //       <div class="col-xs-12 bottom text-center">
  //         <div class="col-xs-12 col-sm-12 col-md-6 emphasis">
  //           <p class="ratings">
  //             <a> coming soon </a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star"></span></a>
  //             <a href="#"><span class="fa fa-star-o"></span></a>
  //           </p>
  //         </div>
  //         <div class="col-xs-12 col-sm-12 col-lg-6 emphasis">
  //         <a href="messages.php?id='. $result[$i]['ID'] .'" class="btn btn-success btn-xs">
  //         <i class="fa fa-user">
  //     </i> <i class="fa fa-comments-o"></i> </a>
  //     <a href="profile.php?id='. $result[$i]['ID'] .'" class="btn btn-primary btn-xs">
  //     <i class="fa fa-user"> </i> View Profile
  //     </a>
  //         </div>
  //       </div>
  //     </div>
  //   </div>';

  //    }
  //    echo "</td>";
  //  }
  //  echo "</tr>";
  // }
  // echo "</tbody>";
  // echo "</table>";
}

// function update_step(){

//   $params=array(session::get($this->id), session::get());
// $stmt=$this->master->select_prepared_async($this->STEP_SELECT[0],$this->var4, $this->STEP_SELECT[1], $params);
// if(!$stmt){
//   echo "query error";
//   echo implode($this->master->connresult);
// }else{
//   $result=$this->master->getResult();
//   if($result[0]['progress']==session::get('')){
//     echo "Something";
//   // }elseif(){
//   //   echo "Somethingelse";
//   // }
// }

// }




private function products($id){
  $sql1 = $this->PRODUCTS[0];
  $types = $this->PRODUCTS[1];
  if($_SESSION['WHO'] == "SMME" ||  $_SESSION['WHO'] == "NPO"){
    $temp1=DB_NAME_1;
  }
  else{
    $temp1=$this->var2;
  }
  
  $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id));
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
    $query = $this->master->select_prepared_async($sql1, $temp1, $types, array($id));
    if(!$query){
    echo "sql ". $sql1;
    echo "<br>types ". $types;
    echo "<br>temp ". $temp1;
    echo "<br>value ". $id;
    echo $this->classname;
    // header("location: ../home.php?error=failedtofetchmoreinfo");
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
private function fetchMore_info2($id){//this one is for when an smme wants to view smme information
  $sql1 = $this->SMME_tO_SMME_MORE_INFO[0];
  $types = $this->SMME_tO_SMME_MORE_INFO[1];
    $query = $this->master->select_prepared_async($sql1, $this->var, $types, array($id));
    if(!$query){
    echo "sql ". $sql1;
    echo "<br>types ". $types;
    echo "<br>temp ". $temp1;
    echo "<br>value ". $id;
    echo $this->classname;
    // header("location: ../home.php?error=failedtofetchmoreinfo");
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
    echo "sql ". $sql1;
    echo "<br>types ". $types;
    echo "<br>temp ". $temp1;
    echo "<br>value ". $id;
    echo $this->classname;
    // header("location: ../home.php?error=failedtofetchmoreinfo");
    exit();
  }else{
    $result = $this->master->getResult();
    return $result;
  }
  
}

public function view_more_chart($id){
  $sql1 = $this->MORE_INFO_CHART[0];
  $types = $this->MORE_INFO_CHART[1];
  $query = $this->master->select("smmes",$sql1, $types, array($id, session::get($this->id)));
  if(!$query){
    print_r($sql1);
      print_r($types);
      echo $id;
      
    exit();
  }else{
    $result = $this->master->getResult();
    if(empty($result)){
      return -1;
    }else{
      echo json_encode($result);
    }
    
  }
}

private function smme_view($result, $products, $id){
  if(empty($result)&&empty($products)){
    echo "<p class='h3 text-center text-capitalize'>No information available</p>";
  }elseif(empty($result)){
    echo "<p class='h3 text-center text-capitalize'>No information available</p>";
  }else{
    
  $address = $result[0]['city'].", ".$result[0]['Province'];
  // Current avatar --><i class="fa fa-angle-left"></i>
  $display = '
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-lg-3">
                      <img class="img-responsive border-rounded " src="'.$result[0]['ext'].'" alt="Avatar" title="'.$result[0]['Legal_name'].'">
                    </div>
                    <div class="col-md-9 col-sm-9 col-lg-9 justify-content-center align-items-center">
                      <h2  class="text-capitalize profile_title  display-4 ">'.$result[0]['Legal_name'].'</h2>
                    </div>
                </div>
                
          
              ';
  $display .= '<hr><div class="col-md-12 col-sm-12 col-lg-12 ">

                  <ul class="list-unstyled user_data">
                    <li class="text-capitalize">
                      <i class="fa fa-map-marker user-profile-icon"></i> Address -> '.$address.'
                    </li>

                    <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result[0]['foo'].'
                    </li>
                    <li class="text-capitalize text-jusitfy">
                    <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result[0]['title'].'
                    </li>
                    <li class="text-capitalize text-jusitfy">
                    <i class="fa fa-envelope user-profile-icon"></i> Email -> '.$result[0]['Email'].'
                    </li>
                    <li class="text-capitalize text-jusitfy">
                    <i class="fa fa-phone user-profile-icon"></i> Contact -> '.$result[0]['Contact'].'
                    </li>
                  </ul>

                
                </div>
  <!-- start skills -->
    ';
    $display .= '<div class="row" style="width: 100% !important ">

      <h4 class="profile_title h2 col-lg-12 col-md-12 ">Company Statements</h4><br>
      <table class="col-lg-9 col-md-12 col-sm-12`" style="width: 100% !important ">
            <tbody style="width: 100% !important " >
              <tr class="border-bottom" style="width: 100%">
                <td style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3" >Introduction</p></td>
                <td style="padding: 10px !important; margin:5px !important;  "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important;">'.$result[0]['introduction'].'</p></td>
              </tr>
              <tr style="width: 100%" >
                <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3" >Mission</p></td>
                <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important">'.$result[0]['mission'].'</p></td>
              </tr>
              <tr style="width: 100%" >
                <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3" >Vision</p></td>
                <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important">'.$result[0]['vision'].'</p></td>
              </tr>
              <tr style="width: 100%" >
                <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-3 col-md-3 col-sm-3 ">Values</p></td>
                <td  style="padding: 10px !important; margin:5px !important; "><p class="col-lg-9 col-md-9 col-sm-9" style="word-wrap: break-word !important">'.$result[0]['values_'].'</p></td>
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

    if($_SESSION['WHO'] == "COMPANY"){
      $COMP_ID = session::get($this->id);
      $SMME_ID = $id;
      $params = array($COMP_ID, $SMME_ID);
      $query = $this->master->select_prepared_async($this->VALIDATE_CONNECTION[0], DB_NAME_5, $this->VALIDATE_CONNECTION[1], $params);
      $connection = $this->master->getResult();
      $expenses = $this->display_expense($id, 2);//2 symbolising that it is the entity viewing the smme expenses
      // print_r($connection);
      // print_r($params);
      // exit();
        if($expenses !== -1 && !empty($connection)){
        $display .= '<hr><div class=" row ">
          <div class="col-sm-12 col-md-12 col-lg-12">
            <br><h4 class="h2 profile_title col-sm-12 col-md-12 col-lg-12 text-center">Expense Summary</h4><br>
          </div>
        </div>';
        $display .= '<div class="row">
          <h4 class="text-center">Direct Expenses</h4>
        <table class="table table-striped">
        ';
          $display .= $expenses;
          $display .= '
          <!-- start skills -->
            <hr><div class="profile_title row"><h4 class="h2 col-lg-12 col-md-12 text-center">Products</h4></section>
            <section><ul class="list-unstyled user_data">
            ';
          for($i=0; $i<=count($products)-1; $i++){
            $display.= '<li><i class="fa fa-shopping-cart"></i>  '.$products[$i]['product'].'</li>';
          }
          $display .= '</ul><hr>';
          $display .= '<!-- start of user-activity-graph -->
          
          <h2 class="profile_title">Shareholder Information</h2>
  
    <!-- end of user-activity-graph -->';
        }else{
          $display .= '
          <!-- start skills -->
            <hr><div class="profile_title row"><h4 class="h2 col-lg-12 col-md-12 text-center">Products</h4></section>
            <section><ul class="list-unstyled user_data">
            ';
          for($i=0; $i<=count($products)-1; $i++){
            $display.= '<li><i class="fa fa-shopping-cart"></i>  '.$products[$i]['product'].'</li>';
          }
          $display .= '</ul><hr>';



          if(!empty($connection)){
            $display .= '<!-- start of user-activity-graph -->
          
                <h2 class="profile_title">Shareholder Information</h2>
        
          <!-- end of user-activity-graph -->';
          }
        }
    }else{
      $display .= '
      <!-- start skills -->
        <hr><div class="profile_title row"><h4 class="h2 col-md-6">Products</h4></section>
        <section><ul class="list-unstyled user_data">
        ';
      for($i=0; $i<=count($products)-1; $i++){
        $display.= '<li><i class="fa fa-shopping-cart"></i>  '.$products[$i]['product'].'</li>';
      }
      $display .= '</ul><hr>';
      
      // if(!empty($connection)){
      //   $display .= '<!-- start of user-activity-graph -->
      
      //       <h2 class="profile_title">Shareholder Information</h2>
    
        
        
      // <!-- end of user-activity-graph -->';
      // }
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

private function company_view_more($result){

  $address = $result[0]['city'].", ".$result[0]['Province'];

  $display = '<div class="col-md-3 col-sm-3  profile_left">
  <div class="profile_img">
    <div id="crop-avatar">
      <!-- Current avatar -->
      <img class="img-responsive avatar-view" src="'.$result[0]['ext'].'" alt="Avatar" title="Change the avatar">
    </div>
  </div>
  
  </div>
  <div class="col-md-9 col-sm-9 ">
  <h2 class="text-capitalize display-4">'.$result[0]['Legal_name'].'</h2>

  <ul class="list-unstyled user_data">
                    <li class="text-capitalize">
                      <i class="fa fa-map-marker user-profile-icon"></i> Address -> '.$address.'
                    </li>

                    <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-briefcase user-profile-icon"></i> Ownership -> '.$result[0]['foo'].'
                    </li>
                    <li class="text-capitalize text-jusitfy">
                    <i class="fa fa-industry user-profile-icon"></i> Industry -> '.$result[0]['title'].'
                    </li>
                    <li class="text-capitalize text-jusitfy">
                      <i class="fa fa-envelope user-profile-icon"></i> Email -> '.$result[0]['Email'].'
                    </li>
                    <li class="text-capitalize text-jusitfy">
                    <i class="fa fa-phone user-profile-icon"></i> Contact -> '.$result[0]['Contact'].'
                    </li>
                  </ul>

    ';
 

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
public function view_moreInfo($id){
 $this->insert_views($id);
    $result = $this->fetchMore_info($id);
      $products = array();
  if($this->classname == "COMPANY"){
    $products = $this->products($id);
    $this->smme_view($result,$products,$id);
  }else{    
      $this->company_view_more($result); 
  }
    
  
  
  
  

}

public function SMME_TO_SMME_view_moreInfo($id){
  $this->insert_views($id);
  $result = $this->fetchMore_info2($id);
  
  $products = array();
    $products = $this->products($id);
    $this->smme_view($result,$products,$id);
  
}

public function COMPANY_TO_COMPANY_view_moreInfo($id){
  $this->insert_views($id);
  $result = $this->fetchMore_info3($id);
  $this->company_view_more($result);
  
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
        echo "No Expenses Yet";
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
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".strtoupper($array[$i]['product_name'])."</td>";
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['rand_value']."</td>";
    $total+=$array[$i]['rand_value'];
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['frequency']."</td>";
    $amount = $array[$i]['rand_value'] * $array[$i]['frequency'];
    $total_py += $amount;
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".$amount."</td>";
    $layout .= "</tr>";
  }
  $layout.="<tr class='row '><td class=' text-center col-sm-3 col-md-3 col-lg-3'><b>Total<b></td>";
  $layout.="<td class='totals col-sm-3 col-md-3 col-lg-3'>".$total."</td>";
  $layout.="<td class='text-center col-sm-3 col-md-3 col-lg-3'><b>Total Per Year<b></td>";
  $layout.="<td class='totals col-sm-3 col-md-3 col-lg-3'>".$total_py."</td></tr>";
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
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".strtoupper($array[$i]['product_name'])."</td>";
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['rand_value']."</td>";
    $total+=$array[$i]['rand_value'];
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".$array[$i]['frequency']."</td>";
    $amount = $array[$i]['rand_value'] * $array[$i]['frequency'];
    $total_py += $amount;
    $layout.="<td class='col-sm-3 col-md-3 col-lg-3'>".$amount."</td>";
    $layout .= "</tr>";
  }
  $layout.="<tr class='row' ><td ><b>Total<b></td>";
  $layout.="<td class='totals col-sm-3 col-md-3 col-lg-3'>".$total."</td>";
  $layout.="<td class='col-sm-3 col-md-3 col-lg-3'><b>Total Per Year<b></td>";
  $layout.="<td class='totals col-sm-3 col-md-3 col-lg-3'>".$total_py."</td></tr>";
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
  $layout = "<p class='text-capitalize text-center h2' >Direct Expenses</p><br><table id='direct_expenses_summary' class='col-lg-12 col-md-12 col-sm-12'>";
  $layout .= "<tr class='row'>
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Expense</b>
                </th> 
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Amount (R)</b>
                </th>
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Frequency<b>
                </th>
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                  <b>Value</b>
                </th>
                </tr>";
  if(empty($direct_expense_row)){
    echo "<p class='text-capitalize text-center h1' >No Direct Expenses Yet</p>";
    exit();
  }else{
    $layout .= $direct_expense_row;
  }
  $layout .= "<br><p class='text-capitalize text-center h2' >Non-Direct Expenses</p><br><table id='non_direct_expenses_summary' class='col-lg-12 col-md-12 col-sm-12'>";
  $layout .= "<tr class='row'>
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Expense</b>
                </th> 
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Amount (R)</b>
                </th>
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
                <b>Frequency<b>
                </th>
                <th class='heads col-sm-3 col-md-3 col-lg-3 '>
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
      $display = VIEW::myBBBEE($result, $this->PROGRESS_PROCESS_SELECT(1));
      echo $display;
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
  
  // echo "min ". print_r($min);
  // echo "max ". print_r($max);
  // echo "av ". print_r($average);
  // exit();
  $display = VIEW::total_users_stats($smme, $company, $total,$current_day_searches);
  echo $display;
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
  $max = $this->MIN_PAGE_VISITS();
  $average = $this->MIN_PAGE_VISITS();
  // echo "min ". print_r($min);
  // echo "max ". print_r($max);
  // echo "av ". print_r($average);
  // exit();
  $display = VIEW::page_visits($min, $max, $average);
  echo $display;
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
 
  $display = VIEW::search_stats($most_searched_name, $most_searched_industry, $most_searched_product);
  echo $display;
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
      echo json_encode($new_array);
    }
}
public function KEYWORD_PERFORMANCE(){
  $query = $this->master->select_prepared_async($this->KEYWORD_ANALYTICS_SELECT[0], $this->var, $this->KEYWORD_ANALYTICS_SELECT[1], array(session::get($this->id)));
  if(!$query){
      echo "Flop2";
      exit();
    }
    else{
      $keyword_hits = array();
      $result=$this->master->getResult();
      $new_array_percentages = array();
      for($i=0;$i<=count($result)-1;$i++){
        $params = array($result[$i]["keyword"],$result[$i]["keyword"]);
        $query1 = $this->master->select_prepared_async($this->KEYWORD_HITS_SELECT[0], $this->var, $this->KEYWORD_HITS_SELECT[1], $params);
        if(!$query1){
          
          print_r($this->KEYWORD_HITS_SELECT[0]);
          echo "<br>";
          print_r($this->KEYWORD_HITS_SELECT[1]);
          echo "<br>";
          print_r($params);
          exit();
        }else{
          $result2=$this->master->getResult();
          array_push($keyword_hits, $result2[0]["hits"]);
        }
      }
      
      $total = array_sum($keyword_hits);
      $length = count($keyword_hits);
      $i =0;
      while($i < $length){
        $percentage = ceil(($keyword_hits[$i]/$total)*100);
        array_push($new_array_percentages, $percentage);
        $i++;
      }
      // return $new_array_percentages;
      // print_r($new_array_percentages);
      // exit();
      echo json_encode($new_array_percentages);
    }
}
public function analytics_head(){
  $requests_made = $this->requests_made();
  $connections = $this->in_progress();
  $requests_received = $this->requests_received();
  $views = $this->num_views();
  $web_visits = $this->web_visits();
  $finalised = $this->finalised_connections();
  echo ANALYTICS_VIEW::BBBEE_stats($requests_made, $requests_received, $connections,$finalised);
}
public function marketplace_head(){
  $views = $this->num_views();
  $web_visits = $this->web_visits();
  $intention_to_engage = $this->num_views();
  $enganged = $this->web_visits();
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
private function profile_stats_select(){
  $sql = $this->PROFILE_STATS_SELECT[0];
  $types = $this->PROFILE_STATS_SELECT[1];
  $id = session::get($this->id);
  $query = $this->master->select_prepared_async($sql, $this->var, $types, array($id, $id, $id, $id));
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
        $result = "";
        echo ANALYTICS_VIEW::company_profile_stats_view($result);
      }else{
        $result = $this -> profile_stats_select();
        echo ANALYTICS_VIEW::smme_profile_stats_view($result);
      }
      
}
// function ToView1($where){
//   $id = session::get($this->id);
//   if(!$id){
//     echo "no id error";
//     exit();
//   }
//   else{
//     // session::set("limit1", False);
//     // session::set("limit2", False);
//     // $limit1=session::get("limit1");
//     // $limit2=session::get("limit2");
//     // if(!$limit1){
//     //   session::set("limit1",10);
//     //   $sql= $this->TOVIEW1_SELECT[0]."10;";
//     // }elseif($limit1==10 && !$limit2){
//     //   session::set("limit2",10);
//     //   $sql= $this->TOVIEW1_SELECT[0]."10,10;";
//     // }else{
//     //   $l=$limit1+10;
//     //   session::set("limit1",$l);
//     //   $sql= $this->TOVIEW1_SELECT[0].$l.",10;";
//     // }
//     if($where==0 || $where==2){
//       $sql1=$this->TOVIEW1_SELECT1[0];
//       $temp1=$this->var2;
//       $sql2=$this->TOVIEW1_SELECT2[0];
//       $types2=$this->TOVIEW1_SELECT2[1];
//       $sql3=$this->TOVIEW1_SELECT3[0];
//       $types3=$this->TOVIEW1_SELECT3[1];
//     }elseif($where==1){
//       $sql1=$this->TOVIEW1_SELECT_2_1[0];
//       $temp1=$this->var3;
//       $sql2=$this->TOVIEW1_SELECT_2_2[0];
//       $types2=$this->TOVIEW1_SELECT_2_2[1];
//       $sql3=$this->TOVIEW1_SELECT_2_3[0];
//       $types3=$this->TOVIEW1_SELECT_2_3[1];
//     }else{
//       echo "Invalid credentials";
//       exit();
//     }
//     $query1=$this->master->select_multiple_async($sql1, $temp1);
//     if(!$query1){
//       echo "query 1 error";
//       echo implode("", $this->master->connresult);
//       exit();
//     }else{
//       $result1=$this->master->getResult();
//     }
//     $query2=$this->master->select_prepared_async($sql2, $temp1, $types2, array(1, $id));
//     // get the information (smme/company/npo) when smme requested
//     if(!$query2){
//       echo "query 2 error<br>";
//       print_r(implode(" ", $this->master->connresult));
//       echo "<br>";
//       echo "select_prepared_async(".$sql2.", ".$temp1."., ".$types2.", array(1, ".$id."));";
//       echo "<br>";
//       exit();
//     }else{
//       $result2=$this->master->getResult();
//     }
//     $query2_1=$this->master->select_prepared_async($sql2, $temp1, $types2, array(2, $id));
//     // get the information (smme/company/npo) when company requested
//     if(!$query2_1){
//       echo "query 2_1 error<br>";
//       print_r(implode(" ", $this->master->connresult));
//       echo "<br>";
//       echo "select_prepared_async(".$sql2.", ".$temp1."., ".$types2.", array(1, ".$id."));";
//       exit();
//     }else{
//       $result2_1=$this->master->getResult();
//     }
//     // $query3=$this->master->select_prepared_async($sql3, $temp1, $types3, array($id));
//     // if(!$query3){
//     //   echo "query 3 error";
//     //   echo implode($this->master->connresult, array());
//     // }else{
//     //   $result3=$this->master->getResult();
//     // } //uncomment when you have comparitive charts
//     $array3D=array($result1, $result2, $result2_1);//, $result3 //uncomment when you have comparitive charts
//         $x=array();
//         $y=array();
//         for ($j=0; $j<3; $j++) {//make sure when you uncomment the code, you fix the for loop to loop 4 times
//         foreach( $array3D[$j] as $key => $val) {
//           if(!$j==0){
//          $x[$key] = $val["Progress"];
//           }elseif($j==0 && $this->classname=="SMME"|| $j==0 && $this->classname=="NPO"){
//             $x[$key] = $val["city"];
//           }else{
//             $x[$key] = $val["BBBEE_Status"];
//           }
//         }
//         $y=array_push($x);
//       }
//        session::set($this->classname."_Progress", $y);
//         if($where==0){
//           $this->Tabloop($array3D);
//         }elseif($where==1){
//           $this->NPOloop($array3D);
//         }else{
//           $this->Tabloop($array3D);
//         }
//   }
// }

// Private function COMPANYloop(array $result){
//   for($i=0; $i<=count($result)-1; $i++){
//     echo "<div class='row' style='position:relative; left:120px; top:30px;'>";
//       echo "<div class='col-lg-10 col-md-6'>";
//         echo "<div class='card'>";
//           echo "<div class='card-body'>";
//               echo "<img src='".$result[$i]['ext']."'class='img-fluid rounded circle w-50 mb-3'>";
//               echo "<h5 class='card-title'>".$result[$i]['Legal_name']."</h5>";
//               echo "<h5 class='card-text'>".$result[$i]['Province'].", ".$result[$i]['city']."</h5>";
//               echo "<div class='container123'>";
//               echo "<div class='wrapper'>";
//                echo "<div class='arrow-steps clearfix'>";
//                 echo "<div class='step current'> <span> Step 1</span> </div>";
//                 echo "<div class='step'> <span>Step 2</span> </div>";
//                 echo "<div class='step'> <span> Step 3</span> </div>";
//                 echo "<div class='step'> <span>Step 4</span> </div>";
//                echo "</div>";
//              echo "</div>";
//            echo "</div>";
//           echo "</div>";
//         echo "</div>";
//       echo "</div>";
//     echo "</div>";
//   }
// }

// Private function SMMEloop(array $result){
//   for($i=0; $i<=count($result)-1; $i++){
//     echo "<div class='row' style='position:relative; left:120px; top:30px;'>";
//       echo "<div class='col-lg-10 col-md-6'>";
//         echo "<div class='card'>";
//           echo "<div class='card-body'>";
//               echo "<img src='".$result[$i]['ext']."'class='img-fluid rounded circle' w-50 mb-3'>";
//               echo "<h5 class='card-title'>".$result[$i]['Legal_name']."</h5>";
//               echo "<h5 class='card-text'>".$result[$i]['Province'].", ".$result[$i]['city']."</h5>";
//               echo "<p class='card-text'> BBBEE Level: ".$result[$i]['BBBEE_Status']."</p>";
//               echo "<p class='card-text'> Product/Service: ".$result[$i]['products_services']."</p>";
//               echo "<div class='container123'>";
//               echo "<div class='wrapper'>";
//                echo "<div class='arrow-steps clearfix'>";
//                 echo "<div class='step current'> <span> Step 1</span> </div>";
//                 echo "<div class='step'> <span>Step 2</span> </div>";
//                 echo "<div class='step'> <span> Step 3</span> </div>";
//                 echo "<div class='step'> <span>Step 4</span> </div>";
//                echo "</div>";
//              echo "</div>";
//            echo "</div>";
//           echo "</div>";
//         echo "</div>";
//       echo "</div>";
//     echo "</div>";
//   }
// }
}
?>