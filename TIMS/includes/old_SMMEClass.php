<?php
// class SMME1 
// {
//     // Properties
//     public $SMMEID;
  
//     // Methods
//     function defaultProfile($conn, $username){
//         $sql = "SELECT * FROM signup WHERE Username=?;";
//         $stmt=mysqli_stmt_init($conn);
//         mysqli_stmt_store_result($stmt);
//         $resultcheck = mysqli_stmt_num_rows($stmt);
//         if(!mysqli_stmt_prepare($stmt, $sql)){
//             header("location: ../home.php?error=databaseError3");
//             exit();  
//         } //elseif ($resultcheck==0){
//            // header("location: ../home.php?error=databaseError8");
//             //exit();}
//         else {
//             mysqli_stmt_bind_param($stmt, "s",$username);// putting username into the placeholder, s represents the number of stings you finna put in there
//             mysqli_stmt_execute($stmt);
//             $result = mysqli_stmt_get_result($stmt);
//             if ($row = mysqli_fetch_assoc($result)){
//                 $SMMEID=$row['SMME_ID'];
//                 $sqlins = "INSERT INTO pimg (SMME_ID) VALUES (?);";//5 placeholders= 5 strings
//                 $stmtq=mysqli_stmt_init($conn);
//                 if(!mysqli_stmt_prepare($stmtq, $sqlins)){
//                     header("location: ../home.php?error=databaseError2");
//                     exit();
//                 }
//             else {
//                 mysqli_stmt_bind_param($stmtq, "s", $SMMEID);
//                 mysqli_stmt_execute($stmtq);
//                     mysqli_stmt_close($stmt);
//                     mysqli_stmt_close($stmtq);
//             }
//         }    else{
//             header("location: ../home.php?error=databaseError7");
//             exit();
//         }                        
//     }
//     }


//     function checkempty($inputs){
//         foreach($inputs as $check){
//             if(empty($check)){
//                 header("location: ../home.php?error=emptyfields");
//                 exit();//checking empty inputs
//             }
//         }
//     }
//     function checkemailusername($email, $username){
//         if(!filter_var($email, FILTER_VALIDATE_EMAIL)&&!preg_match("/^[a-zA-Z0-9]*$/", $username)){
//             header("location: ../home.php?error=invalidemailusername");
//             //checks for ivalid email and username if the user enters both
//         }
//     }
//     function checkusername($username){
//         if(!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
//             header("location: ../home.php?error=invalidusername");
//             exit();//checking invalid username only
//         }
//     }
//     function checkemail($email){
//         if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//             header("location: ../home.php?error=invalidemail");
//             exit();//checking invalid email only
//         }
//     }
//     function checkpasswords($password, $passwordRepeat){
//         if($password !== $passwordRepeat){
//             header("location: ../home.php?error=passwordcheck");
//             //checking if the passwords are not equal
//         }
//     }




//     function signup($sname, $surname, $username, $email, $password, $passwordRepeat, $conn){

//         if (empty($sname) || empty($surname) || empty($username) || empty($email) || empty($password) || empty($passwordRepeat))
//         {
//           header("location: ../dashboard.html?error=emptyfields&Name=".$sname."&surname=".$surname."&username=".$username."&email=".$email);
//           exit();//checking empty inputs
//         }
//     elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)&&!preg_match("/^[a-zA-Z0-9]*$/", $username)){
//         header("location: ../home.php?error=invalidemailusername&Name=".$sname."&Surname=".$surname);
//         //checks for ivalid email and username if the user enters both
//     }
//     elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         header("location: ../home.php?error=invalidemail&Name=".$sname."&Surname=".$surname."&Username=".$username);
//         exit();//checking invalid email only
//     }
//     elseif(!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
//         header("location: ../home.php?error=invalidemail&Name=".$sname."&Surname=".$surname."&email".$email);
//         exit();//checking invalid username only
//     }
//     elseif($password !== $passwordRepeat){
//         header("location: ../home.php?error=passwordcheck&Name=".$sname."&Surname=".$surname."&Username=".$username."&email".$email);
//         //checking if the passwords are not equal
//     }
//     else{//checking if the username is not unique in the database
//         $sql = "SELECT Username FROM signup WHERE Username=?;";//question mark is a placeholder, security check
//         $stmt=mysqli_stmt_init($conn);
//         if(!mysqli_stmt_prepare($stmt, $sql)){
//             header("location: ../home.php?error=databaseError");
//             exit();
//         }
//         else {
//             mysqli_stmt_bind_param($stmt, "s",$username);// putting username into the placeholder, s represents the number of stings you finna put in there
//             mysqli_stmt_execute($stmt);
//             mysqli_stmt_store_result($stmt);//stores results in $stmt
//             $resultcheck = mysqli_stmt_num_rows($stmt);
//             if ($resultcheck >0){
//                 header("location: ../home.php?error=usernametaken&email= ".$email);
//                 exit();}
//             else{
//                     $sql = "INSERT INTO signup (First_Name, Surname, Username, Email, Pwd) VALUES (?, ?, ?, ?, ?);";//5 placeholders= 5 strings
//                     $stmt=mysqli_stmt_init($conn);
//                     if(!mysqli_stmt_prepare($stmt, $sql)){
//                         header("location: ../home.php?error=databaseError2");
//                         exit();
//                     }
//                 else {
//                     $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
//                     mysqli_stmt_bind_param($stmt, "sssss",$sname, $surname, $username, $email, $hashedPwd);
//                     mysqli_stmt_execute($stmt);
//                     $this->defaultProfile($conn, $username);
//                         mysqli_stmt_close($stmt);
//                         header("location: ../Home.php?signup=success");//temporary
//                         exit();
//                 }
//             }
        
//         }
//     }
//     }


    






//     function Login($Username, $password, $conn) {
//         if(empty($Username) || empty($password)){
//           header("location: ../Home.php?error=emptyfields");
//           exit();
//       }
//       else{
//           $sql = "SELECT * FROM signup WHERE Username=?;";
//           $stmt=mysqli_stmt_init($conn);
          
//           if (!mysqli_stmt_prepare($stmt, $sql)) {//is the statement prepared for excecution
//               header("location: ../Home.php?error=databaseerror");
//               exit();
//           }
//           else{
//               mysqli_stmt_bind_param($stmt, "s",$Username);
//               mysqli_stmt_execute($stmt);
//               $result = mysqli_stmt_get_result($stmt);
//               if ($row = mysqli_fetch_assoc($result)){
//                $pwdcheck= password_verify($password, $row['Pwd']);
//                if ($pwdcheck == false){
//                   header("location: ../Home.php?error=wrongpassword");
//                   exit();
//                }
//                elseif($pwdcheck == true) {
//                    session_start();
//                    $_SESSION['ID'] =$row['SMME_ID'];
//                    $array =$this->pimg($conn, $row['SMME_ID']);
//                    if($array=='error'){
//                     $_SESSION['Status']=1;
//                     $_SESSION['profileerror']='error';
//                    }
//                    else{
//                     $_SESSION['Status']=$array['Status'];
//                     if($array['ext']!==null && $_SESSION['Status']==0){
//                     $_SESSION['ext']=$array['ext'];
//                     }
//                    }
//                    header("location: ../SMME_userProfile.php?login=success&Status=".$_SESSION['Status']."&ext=".$_SESSION['ext']."&id=".$_SESSION['ID']."&pe=".$_SESSION['profileerror']);
//                    exit();
//                }
//                else {
//                   header("location: ../Home.php?error=wrongpwd");
//                   exit();
//                }
//               }//fetch a row with an associative array
  
//               else{
//                   header("location: ../Home.php?error=nouser");
//               exit();
//               }
//           }
//       }
//       }
//       function pimg($conn, $SMMEID){
//         $error='error';
//         $sql = "SELECT * FROM pimg WHERE SMME_ID=?;";
//         $stmt=mysqli_stmt_init($conn);
        
//         if (!mysqli_stmt_prepare($stmt, $sql)) {//is the statement prepared for excecution
//             return $error;
//         }
//         else{
//             mysqli_stmt_bind_param($stmt, "i",$SMMEID);
//             mysqli_stmt_execute($stmt);
//             $result = mysqli_stmt_get_result($stmt);
//             if ($row = mysqli_fetch_assoc($result))
//             {
//                 return $row;
//             }
//             else{
//                 return $error;
//             }
//         }

//       }


//     function admin($name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup, $other, $conn) {
//         if (empty($name) || empty($surname) || empty($IdType) || empty($email) || empty($IDNumber)) {
//           header("location: ../dashboard.html?error=emptyfields&Name=".$name."&Surname=".$surname."&IDnumber=".$IDNumber."&email=".$email);
//           exit();//checking empty inputs
//       }
//       elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//           header("location: ../dashboard.html?error=invalidemail&Name=".$name."&Surname=".$surname."&IDNumber=".$IDNumber);
//           exit();//checking invalid email only
//       }

//       else{//checking if the IDNumber is not unique in the database xxxxxxxxx actually, smmeid is better than id number...
//           $sql = "SELECT SMME_ID FROM admin WHERE SMME_ID=?";//question mark is a placeholder, security check
//           $stmt=mysqli_stmt_init($conn);
//           if(!mysqli_stmt_prepare($stmt, $sql)){
//               header("location: ../dashboard.html?error=databaseError");
//               exit();
//           }
//           else {
//               mysqli_stmt_bind_param($stmt, "i",$IDNumber);// putting username into the placeholder, s represents the number of stings you finna put in there
//               mysqli_stmt_execute($stmt);
//               mysqli_stmt_store_result($stmt);//stores results in $stmt
//               $resultcheck = mysqli_stmt_num_rows($stmt);
//               if ($resultcheck >0){
//                   header("location: ../dashboard.html?error=IDNumberTaken&name= ".$name."&Surname=".$surname."&email=".$email);
//                   exit();}
              
//               else{

//                 if($EthnicGroup=='OtherR'){
//                     $sql = "INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group) VALUES (?, ?, ?, ?, ?, ?, ?);";//5 placeholders= 5 strings
//                       $stmt=mysqli_stmt_init($conn);
//                       if(!mysqli_stmt_prepare($stmt, $sql)){
//                           header("location: ../dashboard.html?error=databaseError");
//                           exit();
//                       }
              
//                   else {
//                       mysqli_stmt_bind_param($stmt,"sssisss",$name, $surname, $IdType, $IDNumber, $Gender, $email, $other);//other in ethnic group
//                       mysqli_stmt_execute($stmt);
//                       mysqli_stmt_store_result($stmt);//stores results in $stmt
//                       $resultcheck = mysqli_stmt_num_rows($stmt);
//                       //if ($resultcheck!=1){
//                         //header("location: ../dashboard.html?error=Databaseerror&name= ".$name."&Surname=".$surname."&email=".$email."&resultcheck=".$resultcheck);
//                         //exit();}
//                   }
//                 }



//                 else{
//                 $sql = "INSERT INTO admin (first_name, Surname, Identification_Type, ID_Number, Gender, Email, Ethnic_Group) VALUES (?, ?, ?, ?, ?, ?, ?);";//5 placeholders= 5 strings
//                       $stmt=mysqli_stmt_init($conn);
//                       if(!mysqli_stmt_prepare($stmt, $sql)){
//                           header("location: ../dashboard.html?error=databaseError");
//                           exit();
//                       }
              
//                   else {
//                       mysqli_stmt_bind_param($stmt,"sssisss",$name, $surname, $IdType, $IDNumber, $Gender, $email, $EthnicGroup);
//                       mysqli_stmt_execute($stmt);
//                       mysqli_stmt_store_result($stmt);//stores results in $stmt
//                       $resultcheck = mysqli_stmt_num_rows($stmt);
//                       //if ($resultcheck!=1){
//                         //header("location: ../dashboard.html?error=Databaseerror&name= ".$name."&Surname=".$surname."&email=".$email."&resultcheck=".$resultcheck);
//                         //exit();}
//                   }
//                 }




//               }
          
//           }
//       }
//       mysqli_stmt_close($stmt);
//       }



//     //function that adds the companys missions, vision, values, goals and objectives to the database
//     function addCompanyStatements($introduction, $vision, $mission, $values, $goals_objectives, $products_services, $conn){
//         if (empty($introduction) || empty($vision) || empty($mission) || empty($values) || empty($goals_objectives) || empty($products_services)) {
//             header("location: ../signup.php?error=emptyfields&introduction=".$introduction."&vision=".$vision."&mission=".$mission."&values=".$values."&goals_objectives=".$goals_objectives."&products_services=".$products_services);
//             exit();//checking empty inputs
//         }


//             else {
//                 $sql = "INSERT INTO company_profile ( introduction, vision, mission, values, goals_objectives, products_services) VALUES (?, ?, ?, ?, ?, ?)";//6 placeholders= 5 strings
//                 $stmt=mysqli_stmt_init($conn);
//                 if(!mysqli_stmt_prepare($stmt, $sql)){
//                     header("location: ../signup.php?error=databaseError");
//                     exit();
//                 }
//                 else {
//                         mysqli_stmt_bind_param($stmt, "ssssss", $introduction, $vision, $mission, $values, $goals_objectives, $products_services);
//                         mysqli_stmt_execute($stmt);
//                         mysqli_stmt_store_result($stmt);//stores results in $stmt
//                         $resultCheck = mysqli_stmt_num_rows($stmt);
//                         header("location: ../Home.php?login=success&resultCheck.$resultCheck");
//                         exit();
//                     }
//                     mysqli_stmt_close($stmt);
//                     mysqli_stmt_close($conn);
//                 }

        
//     }


//     function Directors($name, $surname, $IdType, $IDNumber, $Gender, $EthnicGroup, $other, $conn) {
//         if (empty($name) || empty($surname) || empty($IDNumber)) {
//           header("location: ../dashboard.html?error=emptyfields&Name=".$name."&Surname=".$surname."&IDnumber=".$IDNumber);
//           exit();//checking empty inputs
//       }

//       else{//checking if the IDNumber is not unique in the database
//           $sql = "SELECT ID_Number FROM company_director WHERE ID_Number=?";//question mark is a placeholder, security check
//           $stmt=mysqli_stmt_init($conn);
//           if(!mysqli_stmt_prepare($stmt, $sql)){
//               header("location: ../dashboard.html?error=databaseError");
//               exit();
//           }
//           else {
//               mysqli_stmt_bind_param($stmt, "i",$IDNumber);// putting username into the placeholder, s represents the number of stings you finna put in there
//               mysqli_stmt_execute($stmt);
//               mysqli_stmt_store_result($stmt);//stores results in $stmt
//               $resultcheck = mysqli_stmt_num_rows($stmt);
//               if ($resultcheck >0){
//                   header("location: ../dashboard.html?error=IDNumberTaken&name= ".$name."&Surname=".$surname);
//                   exit();}
              
//               else{

//                 if($EthnicGroup=='OtherR'){
//                     $sql = "INSERT INTO company_director (Name, Surname, Identification_Type, ID_Number, Ethnic_Group, Gender) VALUES (?, ?, ?, ?, ?, ?);";//5 placeholders= 5 strings
//                       $stmt=mysqli_stmt_init($conn);
//                       if(!mysqli_stmt_prepare($stmt, $sql)){
//                           header("location: ../dashboard.html?error=databaseError");
//                           exit();
//                       }
              
//                   else {
//                       mysqli_stmt_bind_param($stmt,"sssiss",$name, $surname, $IdType, $IDNumber, $other, $Gender);//other in ethnic group
//                       mysqli_stmt_execute($stmt);
//                       mysqli_stmt_store_result($stmt);//stores results in $stmt
//                       $resultcheck = mysqli_stmt_num_rows($stmt);
//                       //if ($resultcheck!=1){
//                         header("location: ../CD?Form=Success");
//                         exit();
//                     //}
//                   }
//                 }



//                 else{
//                 $sql = "INSERT INTO company_director (Name, Surname, Identification_Type, ID_Number, Ethnic_Group, Gender) VALUES (?, ?, ?, ?, ?, ?);";//5 placeholders= 5 strings
//                       $stmt=mysqli_stmt_init($conn);
//                       if(!mysqli_stmt_prepare($stmt, $sql)){
//                           header("location: ../dashboard.html?error=databaseError");
//                           exit();
//                       }
              
//                   else {
//                       mysqli_stmt_bind_param($stmt,"sssiss",$name, $surname, $IdType, $IDNumber, $EthnicGroup, $Gender);
//                       mysqli_stmt_execute($stmt);
//                       mysqli_stmt_store_result($stmt);//stores results in $stmt
//                       $resultcheck = mysqli_stmt_num_rows($stmt);
//                       //if ($resultcheck!=1){
//                         //header("location: ../dashboard.html?error=Databaseerror&name= ".$name."&Surname=".$surname."&email=".$email."&resultcheck=".$resultcheck);
//                         //exit();}
//                         //header("location: ../CD?Form=Success");
//                         //exit();
//                   }
//                 }




//               }
          
//           }
//       }
//       mysqli_stmt_close($stmt);
//       }





//     //company document function that adds the information to the database
//     function addCompanyDocuments($Number_Shareholders, $Number_Black_Shareholders, $Number_White_Shareholders, $Black_Ownership_Percentage, $Black_Female_Percentage, $White_Ownership_percentage, $BBBEE_Status, $Date_Of_Issue, $Expiry_Date, $conn){
//             if (empty($Number_Shareholders) || empty($Number_Black_Shareholders) || empty($Number_White_Shareholders) || empty($Black_Ownership_Percentage) || empty($Black_Female_Percentage)  || empty($White_Ownership_percentage) || empty($BBBEE_Status) || empty($Date_Of_Issue) || empty($Expiry_Date) ) {
//                 header("location: ../home.php?error=emptyfields&Number_shareholders=".$Number_Shareholders."&Number_Black_shareholders=".$Number_Black_Shareholders."&Number_White_Shareholders=".$Number_White_Shareholders."&Black_Ownership_Percentage=".$Black_Ownership_Percentage."&Black_Female_Percentage=".$Black_Female_Percentage."&White_Ownership_percentage=".$White_Ownership_percentage."&BBBEE_Status=".$BBBEE_Status."&Date_Of_Issue=".$Date_Of_Issue."&Expiry_Date=".$Expiry_Date);
//                 exit();//checking empty inputs
//              }
//              //elseif(date_diff('yyyy-mm-dd',$Date_Of_Issue,$Expiry_Date)<0){
//                 //header("location: ../home.php?error=expirydatebeforedateofissue&Number_shareholders=".$Number_Shareholders."&Number_Black_shareholders=".$Number_Black_Shareholders."&Number_White_Shareholders=".$Number_White_Shareholders."&Black_Ownership_Percentage=".$Black_Ownership_Percentage."&Black_Female_Percentage=".$Black_Female_Percentage."&White_Ownership_percentage=".$White_Ownership_percentage."&BBBEE_Status=".$BBBEE_Status);
//                 //exit();//checking empty inputs
//             //}//compare dates

//                 else{
//                     $sql = "INSERT INTO company_documentation (Number_shareholders, Number_Black_Shareholders, Number_White_Shareholders, Black_Ownership_Percentage, Black_Female_Percentage, White_Ownership_percentage, BBBEE_Status, Date_Of_Issue, Expiry_Date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";//5 placeholders= 5 strings
//                     $stmt=mysqli_stmt_init($conn);
//                     if(!mysqli_stmt_prepare($stmt, $sql)){
//                         header("location: ../signup.php?error=databaseError");
//                         exit();
//                     }
//                     else {
//                         mysqli_stmt_bind_param($stmt, "iiiiiisss", $Number_Shareholders, $Number_Black_Shareholders, $Number_White_Shareholders, $Black_Ownership_Percentage, $Black_Female_Percentage, $White_Ownership_percentage, $BBBEE_Status, $Date_Of_Issue, $Expiry_Date);
//                         mysqli_stmt_execute($stmt);
//                         mysqli_stmt_store_result($stmt);//stores results in $stmt
//                         $resultCheck = mysqli_stmt_num_rows($stmt);
//                     }
//                 }
//         mysqli_stmt_close($stmt);
//    }


   
// function expensesummary($serviceprovider, $productname, $productspecification, $randvalue, $frequency, $type, $session, $conn)
// {
//     for ($a = 0; $a < count($serviceprovider); $a++)
//     {
//         $sql = "INSERT INTO expense_summary (service_provider, product_name, product_specification, rand_value, frequency, type_of_expense, SMME_ID	) VALUES (?,?,?,?,?,?,?)";
//         $stmt=mysqli_stmt_init($conn);
//         if(!mysqli_stmt_prepare($stmt, $sql))
//         {
//           header("location: ../Expense_summary.php?error=databaseError");
//           exit();
//         }
//         else {
//             mysqli_stmt_bind_param($stmt, "sssiiii",$serviceprovider[$a], $productname[$a], $productspecification[$a], $randvalue[$a], $frequency[$a], $type, $session);
//             mysqli_stmt_execute($stmt);
//             mysqli_stmt_store_result($stmt);//stores results in $stmt
//             //$resultCheck = mysqli_stmt_num_rows($stmt);
            
//         }
//         if($a==count($serviceprovider)-1){
//                 header("location: ../Expense_summary.php?registration=success");
//                 exit();
//         }
//     }
// }
// function temp($conn, $SMMEID, $ext){
//     //temp changes 1 to 0 or if 0 deletes current picture  from root directory only!
//       $cur=$this->pimg($conn, $SMMEID);
//       if ($cur!=='error')
//       {
//           $Status=$cur['Status'];
//           if($Status == 0){
//               $oldpic="../".$_SESSION['ext'];
//             $what=unlink($oldpic);
//               if(!$what){
//                 header("location: ../SMME_userProfile.php?error=Failedtodelete");
//                 exit();
//               }
//               else{
//                 $sql = "UPDATE pimg SET  ext='?'  WHERE SMME_ID='?';";
//                 $stmt=mysqli_stmt_init($conn);
//                 if(!mysqli_stmt_prepare($stmt, $sql)){
//                     header("location: ../home.php?error=databaseError10");
//                     exit();
//                 }
//             else {
//                 mysqli_stmt_bind_param($stmt, "si", $ext, $SMMEID);
//                 mysqli_stmt_execute($stmt);
//                 $_SESSION['ext']=$ext;
//                 }
//               }
//           }
//           else{
//               $bool=$this->zooom($conn, $ext, $SMMEID);
//               if($bool==1){
//                 header("location: ../home.php?error=databaseErrorZoom");
//                 exit();
//               }elseif($bool==2){
//                 $_SESSION['Status']=0;
//                 $_SESSION['ext']=$ext;
//               }
//               else{
//                 header("location: ../home.php?error=databaseErrorZoomNoResult");
//                 exit();
//               }
//           }
// }
// else{
//     header("location: ../home.php?error=pimgerror");
//     exit();
//   }
// }


// function zoom($conn, $ext, $SMMEID){
//     $sql = "UPDATE pimg SET Status= 0, ext='?' WHERE SMME_ID='?';";
//     $stmt=mysqli_stmt_init($conn);
//     if(!mysqli_stmt_prepare($stmt, $sql)){
//         return 1;
//     }
// else {
//     mysqli_stmt_bind_param($stmt, "si", $ext, $SMMEID);
//     mysqli_stmt_execute($stmt);
//     mysqli_stmt_store_result($stmt);
//     $resultcheck = mysqli_stmt_num_rows($stmt);
//     if($resultcheck==0){
//         return 3;
//     }else {
//         return 2;
//     }
//     }
// }

// function zooom($conn, $ext, $SMMEID){
//     $sql = "UPDATE pimg SET Status= 0, ext='$ext' WHERE SMME_ID='$SMMEID';";
//     if (mysqli_query($conn, $sql)) {
//         return 2;
//     } else {
//         return 3;
//     }
// }






// function UploadProfilePic($fileName,$fileTmpName,$fileSize,$fileError, $SMMEID, $conn){
//     $fileExt = explode('.', $fileName);
//     $fileActualExt = strtolower(end($fileExt));

//     $allowed = array('jpg','jpeg','png');

//     if(in_array($fileActualExt, $allowed)){
//         if($fileError== 0){
//             if($fileSize < 2000000){
//                 $fileNameNew = $SMMEID.".".$fileActualExt;
//                 $fileDestination = '../Uploads/'.$fileNameNew;
//                 $filedelete="../Uploads/".$SMMEID.".*";
//                 $fileSession = 'Uploads/'.$fileNameNew;
//                 $this->temp($conn, $SMMEID, $fileSession, $filedelete);
//                 move_uploaded_file($fileTmpName, $fileDestination);
//                 header("Location: ../SMME_userProfile.php?upload=successful&status=".$_SESSION['Status']);
//                 exit();
//             }
//             else{
//                 header("location: ../SMME_userProfile.php?error=YourFileIsTooBig");
//                 exit();
//             }
//         }
//         else{
//             header("location: ../SMME_userProfile.php?error=ThereWasAnErrorUploadingYourFile");
//             exit();
//         }
//     }
//     else{
//         header("location: ../SMME_userProfile.php?error=YouCannotUploadThisTypeOfFiles");
//         exit();
//     }
    
// }

// function UploadFile($file,$fileName,$fileTmpName,$fileSize,$fileError,$fileType){


//     $fileExt = explode('.', $fileName);
//     $fileActualExt = strtolower(end($fileExt));

//     $allowed = array('jpg','jpeg','png','pdf');

//     if(in_array($fileActualExt, $allowed)){
//         if($fileError== 0){
//             if($fileSize < 2000000){
//                 $fileNameNew = uniqid('', true).".".$fileActualExt;
//                 $fileDestination = '../Uploads/'.$fileNameNew;
//                 move_uploaded_file($fileTmpName, $fileDestination);
                
//                 header("Location: ../SMME_userProfile.php?upload=successful");
//             }
//             else{
//                 header("location: ../SMME_userProfile.php?error=YourFileIsTooBig");
//                 exit();
//             }
//         }
//         else{
//             header("location: ../SMME_userProfile.php?error=ThereWasAnErrorUploadingYourFile");
//             exit();
//         }
//     }
//     else{
//         header("location: ../SMME_userProfile.php?error=YouCannotUploadThisTypeOfFiles");
//         exit();
//     }
    
// }












// function register($name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email, $session, $conn)
//       {
//         if (empty($name) || empty($Address) || empty($Province) || empty($email) || empty($RegNum) || empty($Postal) || empty($City) || empty($Contact)) 
//         {
//           header("location: ../dashboard.html?error=emptyfields&Name=".$name."&Address=".$Address."&Province=".$Province."&email=".$email."&RegNum=".$RegNum."&Postal=".$Postal."&City=".$City."&Contact=".$Contact);
//           exit();//checking empty inputs
//         }
//         elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//             header("location: ../dashboard.html?error=invalidemail&Name=".$name."&Address=".$Address."&Province=".$Province."&RegNum=".$RegNum."&Postal=".$Postal."&City=".$City."&Contact=".$Contact);
//             exit();//checking invalid email
//         }
//         else{//checking if the RegNum is not unique in the databaseXXXXXXX ACTUALLY, SMME_ID IS BETTER
//             $sql = "SELECT SMME_ID FROM register WHERE SMME_ID=?";//question mark is a placeholder, security check
//             $stmt=mysqli_stmt_init($conn);
//             if(!mysqli_stmt_prepare($stmt, $sql))
//             {
//                 header("location: ../dashboard.html?error=databaseError");
//                 exit();
//             }
//             else {
//                 mysqli_stmt_bind_param($stmt, "s",$RegNum);// putting username into the placeholder, s represents the number of stings you finna put in there
//                 mysqli_stmt_execute($stmt);
//                 mysqli_stmt_store_result($stmt);//stores results in $stmt
//                 $resultcheck = mysqli_stmt_num_rows($stmt);
//                 if ($resultcheck >0){
//                     header("location: ../dashboard.html?error=CCRegistrationNumbertaken");
//                     exit();}
                
//                 else{
//                         $sql = "INSERT INTO register (Legal_name, CC_Registration_Number, Adress, Post_Code, city, Province, Contact, Email, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";//5 placeholders= 5 strings
//                         $stmt=mysqli_stmt_init($conn);
//                         if(!mysqli_stmt_prepare($stmt, $sql))
//                         {
//                             header("location: ../dashboard.html?error=databaseError%session=".$session);
//                             exit();
//                         }
                
//                     else {
//                         mysqli_stmt_bind_param($stmt, "sisissisi",$name, $RegNum, $Address, $Postal, $City, $Province, $Contact, $email, $session);
//                         mysqli_stmt_execute($stmt);
//                         mysqli_stmt_store_result($stmt);//stores results in $stmt
//                         $resultCheck = mysqli_stmt_num_rows($stmt);
                        
//                         header("location: ../SMME_userProfile.php?registration=success");
//                         exit();
//                     }
//                 }
            
//             }
//             mysqli_stmt_close($stmt);
//         }

//     }





// function registerCompanyData($legal_name, $trading_name, $registration_number, $business_type, $business_subType, $financial_year, $session, $conn){
//         if (empty($legal_name) || empty($trading_name) || empty($registration_number) || empty($business_type) || empty($business_subType) || empty($financial_year)) 
//         {
//           header("location: ../dashboard.html?error=emptyfields&Name=".$legal_name."&registration_number=".$registration_number."&business_type=".$business_type."&business_subType=".$business_subType);
//           exit();//checking empty inputs
//         }
//         else{//checking if the RegNum is not unique in the database XXXXX SMMEID RATHER
//             $sql = "SELECT SMME_ID FROM company_data WHERE SMME_ID=?";//question mark is a placeholder, security check
//             $stmt=mysqli_stmt_init($conn);
//             if(!mysqli_stmt_prepare($stmt, $sql))
//             {
//                 header("location: ../dashboard.html?error=databaseError");
//                 exit();
//             }
//             else {
//                 mysqli_stmt_bind_param($stmt, "i",$session);// putting username into the placeholder, s represents the number of stings you finna put in there
//                 mysqli_stmt_execute($stmt);
//                 mysqli_stmt_store_result($stmt);//stores results in $stmt
//                 $resultcheck = mysqli_stmt_num_rows($stmt);
//                 if ($resultcheck >0){
//                     header("location: ../home.php?error=Companyalreadysubmitted&legalname=".$legal_name."&tradingname=".$trading_name."&registrationnumber=".$registration_number."&businesstype=".$business_type."&businesssubtype=".$business_subType);
//                     exit();}
                
//                 else{
//                         $sql = "INSERT INTO company_data (Legal_name, CC_Registration_Number, Trading_Name, Financial_Year, Business_Type, Business_Sub_Type, SMME_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";//5 placeholders= 5 strings
//                         $stmt=mysqli_stmt_init($conn);
//                         if(!mysqli_stmt_prepare($stmt, $sql))
//                         {
//                             header("location: ../dashboard.html?error=databaseError");
//                             exit();
//                         }
                
//                     else {
//                         mysqli_stmt_bind_param($stmt,"sissssi",$legal_name, $registration_number, $trading_name, $financial_year, $business_type, $business_subType, $session);
//                         mysqli_stmt_execute($stmt);
//                         mysqli_stmt_store_result($stmt);//stores results in $stmt
//                         $resultCheck = mysqli_stmt_num_rows($stmt);
//                         if($resultCheck==2){
//                             header("location: ../SMME_userProfile.php?registration=successbutduplicate");
//                             exit();
//                         }else{
//             `           header("location: ../SMME_userProfile.php?registration=success");
//                         exit();
//                     }
//                     }
//                 }
            
//             }
//             mysqli_stmt_close($stmt);
//         }

//     }
// } 