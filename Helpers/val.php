<?php 
class val {

    static function checkempty($inputs){
        foreach($inputs as $check){
            if(empty($check)){
                header("location: ../home.php?error=emptyfields");
                exit();//checking empty inputs
            }
        }
    }

    static function checkemailusername($email, $username){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)&&!preg_match("/^[a-zA-Z0-9]*$/", $username)){
            header("location: ../home.php?error=invalidemailusername");
            exit();
            //checks for ivalid email and username if the user enters both
        }
    }
    static function checkusername($username){
        if(!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
            header("location: ../home.php?error=invalidusername");
            exit();//checking invalid username only
        }
    }
    static function checkemail($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("location: ../home.php?error=invalidemail");
            exit();//checking invalid email only
        }
    }
    static function checkpasswords($password, $passwordRepeat){
        if($password !== $passwordRepeat){
            header("location: ../home.php?error=passwordcheck");
            exit();
            //checking if the passwords are not equal
        }
    }
}