<?php
class pass_recovery{

    private function Gen_tokens(){
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        $tokens = array($selector, $token);
        return $tokens;
    }

    public function Save_passwords($password, $password_repeat, $selector, $validator){
        if(empty($password)|| empty($password_repeat)){//checking empty inputs
            echo "Inputs can not be empty!";
            exit();
        }
        else{
            if($password !== $password_repeat){//checking password match
                echo "Passwords have to match";
                exit();
            }
            else{//match
                $validator_status = $this->check_tokens($selector, $validator);
                if(!$validator_status){
                    echo "validator status is false meaning it could not validate tokens in db.";
                }else{//tokens do exist, now check if they have not expired yet
                    $result = $this->check_date($selector);
                    if(empty($result)){
                        echo "date has expired restart process";
                        exit();
                    }else{
                            $db_token = $result["token"];
                            $token_bin = hex2bin($validator);
                            $validator_check = password_verify($token_bin, $db_token);
                            if($validator_check === false){
                                echo "Oops please restart process, tokens are not the same.";
                            }else if($validator_check === true){
                                $token_email = $result["email"];
                                $email_check = $this->check_email($token_email);
                                if(!$email_check){
                                    echo "this email does not exist in sign up db.";
                                    exit();
                                }else{
                                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                    $this->update_password($hashed_password, $email);
                                }
                            }else{
                                echo "you're not even meant to be here this is a huge flop if you are.";
                                exit();
                            }
                        }      
                    }
                }
            }
        }
        private function check_tokens($selector, $validator){
        $sql = $this->VALIDATOR_CHECK_SELECT[0];
        $types = $this->VALIDATOR_CHECK_SELECT[1];
        $params = array($selector, $validator);
        $query = $this->select("password_recovery", $sql, $types, $params);//select * from password_recovery to validate whether the tokens exist
        if(!$query){
            echo "Seems to be an error with checking tokens";
            exit();
        }
        else{
            $result=$this->master->getResult();
            $xi=$this->master->numRows();
            if($xi==0){
                echo "Seems to be no tokens found. Restart the process";
                return false;
            }else{
                return true;
            }
        }
    }
    private function check_date($selector){
        $currentDate = date("u");
        $sql = $this->DATE_CHECK_SELECT[0];
        $types = $this->DATE_CHECK_SELECT[1];
        $params = array($selector, $currentDate);
        $query = $this->select("password_recovery", $sql, $types, $params);
        if(!$query){
            echo "Seems to be an error with checking tokens";
            exit();
        }
        $xi=$this->master->numRows();
        if($xi==0){
            echo "Seems to be no tokens found. Restart the process";
            exit();
        }else{
            $result = $this->master->getResult();
            return $result;
        }
    }
    private function email_check($email){
        $sql = $this->EMAIL_SELECT[0];
        $types = $this->EMAIL_SELECT[1];
        $params = array($email);
        $query = $this->select("password_recovery", $sql, $types, $params);
        if(!$query){
            echo "Seems to be an error with checking tokens";
            exit();
        }else{
            $xi=$this->master->numRows();
            if($xi==0){
                echo "Seems to be no tokens found. Restart the process";
                return false;
            }else{
                return true;
            }
        }
    }
    private function update_password($password, $email){
        $sql = $this->PASSWORD_UPDATE[0];
        $types = $this->PASSWORD_UPDATE[1];
        $params = array($password, $email);
        $query = $this->update("password_recovery", $sql, $types, $params);
        if(!$query){
            echo "Seems to be an error with updating password";
            exit();
        }
        else{
            header('"location: ../update_succesful"');
          exit();
        }
    }

}


    
