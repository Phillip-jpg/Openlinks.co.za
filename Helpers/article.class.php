<?php
include_once("config.php");
class article {
    private $conn;
    function __construct(){
        $this->conn =new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if($this->conn->connect_errno > 0){
            header("location: https://openlinks.co.za/?Error=databaseerror1");
            exit();
        }
    }
    function get(int $id){//html_entity_decode($str);
    $this->val_id($id);
    $sql = "SELECT img, heading, author, date_published, article FROM articles WHERE id=?;";
    $stmt=$this->conn->stmt_init();
    if(!$stmt->prepare($sql)){
        // throw new Error('databaseerror');
        header("location: https://openlinks.co.za/?Error=databaseerror2");
        exit();
    }
    else {
        $this->conn->real_escape_string($id);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if(!$stmt){
            header("location: https://openlinks.co.za/?Error=databaseerror3");
            exit();
        }else{
            // If the query returns >= 1 assign the number of rows to numResult
            $result = $stmt->get_result();
            $connresult=array();
            while($row=$result->fetch_assoc()){
                $connresult=$row;
            }
            if(empty($connresult)){
                header("location: https://openlinks.co.za/");
                exit();
            }
             return $connresult;
        }
        }
    }

//id, headline, img, date_published, article, author
    function getnext($id){
        $this->val_id($id);
        $sql = "SELECT headline, img, heading, author, date_published, url  FROM articles WHERE NOT id=?;";
        $stmt=$this->conn->stmt_init();
        if(!$stmt->prepare($sql)){
            header("location: https://openlinks.co.za/?Error=databaseerror");
            exit();
        }
        else {
            $this->conn->real_escape_string($id);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            if(!$stmt){
                header("location: https://openlinks.co.za/?Error=databaseerror");
                exit();
            }else{
                $meta = $stmt->result_metadata();
                $row = array();
                while ( $rows = $meta->fetch_field() ) {
                    $parameters[] = &$row[$rows->name];
                }
                call_user_func_array(array($stmt, 'bind_result'), $parameters);
                while ( $stmt->fetch() ) {
                    $x = array();
                    foreach( $row as $key => $val ) {
                       $x[$key] = $val;
                    }
                    $arr_results[] = $x;
                }
                return $arr_results;
            }
        
        }
    }

    function getall(){
        $sql = "SELECT headline, img, heading, author, date_published, url FROM articles";
        $stmt =$this->conn->query($sql);
        $result=array();
        if(!$stmt){
            header("location: https://openlinks.co.za/?Error=databaseerror");
            exit();
        }else{
                // Loop through the query results by the number of rows returned
                for($i = 0; $i < $stmt->num_rows; $i++){
                    $r = $stmt->fetch_array();
                    $key = array_keys($r);
                    for($x = 0; $x < count($key); $x++){
                        // Sanitizes keys so only alphavalues are allowed
                        if(!is_int($key[$x])){
                            if(!empty($r)){
                                $result[$i][$key[$x]] = $r[$key[$x]];
                         }else{
                             $result = null;
                         }
                     }
                    }
                }
                return $result;
            }
    }

    
    private function val_id($id){
        if(!is_int($id) && strlen((string)$id)!==4){
            header("location: https://openlinks.co.za/");
            exit();
        }
    }
}