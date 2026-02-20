<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/config.php');
?>
<?php
class dbh{
private $servername = DB_HOST;
private $dbUsername = DB_USER;
private $dpPassword = DB_PASS;
private $dbName1 = DB_NAME_1;
private $dbName2 = DB_NAME_2;
private $dbName3 = DB_NAME_3;
private $dbName4 = DB_NAME_4;
private $whichdb;


public $connbool=false;
public $conne;

function __constuct($whichdb){
    $this->whichdb=$whichdb;
    $connect=$this->connect();
    if(!$connect && $this->connresult){//failed to connect
            $rel=end($this->connresult);
            header("location: ../home.php?error=databaseError=".$rel);
            exit();
    }
    elseif(!$connect && $this->whichdb==null){// database name is wrong
        header("location: ../home.php?error=databaseErrorWhichdbWrong");
        exit(); 
    }
    elseif(!$connect){//somehow the database fails to load without anything catching it
        header("location: ../home.php?error=databaseErrorUnIdentified");
        exit();
    }
}
private function connect(){
    if($this->whichdb!==$this->dbName1|| $this->whichdb!==$this->dbName2|| $this->whichdb!==$this->dbName3|| $this->whichdb!==$this->dbName4){
    $this->whichdb=null;
    return false;
}
    if(!$this->connbool){
        
        $this->conne =new mysqli($this->servername, $this->dbUsername, $this->dpPassword, $this->whichdb);
        if($this->conne->connect_errno > 0){
            array_push($this->connresult,$this->conne->connect_error);
            return false;
        }else{
            $this->connbool = true;
            return true;
        }
    }else{  
        return true; // Connection has already been made return TRUE 
    }
}

public function disconnect(){
    if($this->connbool){
        // We have found a connection, try to close it
        if($this->conne->close()){
            // We have successfully closed the connection, set the connection variable to false
            $this->connbool = false;
            // Return true tjat we have closed the connection
            return true;
        }else{
            // We could not close the connection, return false
            return false;
        }
    }

}
}