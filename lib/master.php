<?php
//master selects, inserts and updates from database, extends dbh
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/config.php');
class Master {

private $servername = DB_HOST;
private $dbUsername = DB_USER;
private $dpPassword = DB_PASS;
private $whichdb;


private $connbool=false;
private $conn;
public $connresult=array();
private $c;
private $myQuery;
private $numResults;




function __construct($which){
    $this->whichdb=$which;
    $connect=$this->connect($which);
    if(!$connect && $this->c){//failed to connect
            $rel=end($this->c);
            header("location: ../home.php?error=databaseError=".$rel);
            exit();
    }
    elseif(!$connect && $this->whichdb==null){// database name is wrong
        header("location: ../home.php?error=databaseErrorWhichdbWrong=".$this->whichdb);
        exit(); 
    }
    elseif(!$connect){//somehow the database fails to load without anything catching it
        header("location: ../home.php?error=databaseErrorUnIdentified");
        exit();
    }
}

function changedb($which){
    $this->disconnect();
    $this->connect($which);
}

public function makeArray(){
    $a = array();
    return $a;
  }

private function connect($which){
    if(!$this->connbool){
        $this->conn =new mysqli($this->servername, $this->dbUsername, $this->dpPassword, $which);
        if($this->conn->connect_errno > 0){
            array_push($this->c,$this->conn->connect_error);
            header("location: ../home.php?error=databaseError=".$this->conn->connect_error);
            exit();
            //return false;
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
        if($this->conn->close()){
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


private function prepare($sql, $types, array $vararray){
    $tmp = array();
    foreach($vararray as $key => $value){
    $tmp[$key] = &$vararray[$key];}
    $tmp=array_values($tmp);
    array_unshift($tmp,$types);
    $stmt=$this->conn->stmt_init();
    $this->myQuery = $sql; // Pass back the SQL
    if(!$stmt->prepare($sql)){
        return false;
    }else{
        call_user_func_array(array($stmt, 'bind_param'), $tmp);
        if($stmt->execute()){
            return $stmt;
        }else{
            return false;
        }
    }
}


    public function selectnonquery($table, $sql, $types, $params){
        // echo $table;
        $this->myQuery = $sql; // Pass back the SQL
		// Check to see if the table exists
        // if($this->tableExists($table)){
        	// The table exists, run the query
        	$stmt = $this->prepare($sql, $types, $params);
			if(!$stmt){
                array_push($this->connresult,$this->conn->error);
                return false; // No rows where returned
            }
            else{
                $stmt->store_result();
				// If the query returns >= 1 assign the number of rows to numResults
                $this->numResults = $stmt->num_rows;
				return true; // Query was successful
			
			}
     	// }else{
      	// 	return false; // Table does not exist
    	// }
    }

    public function select($table, $sql, $types, $params){
         //mysqli_report(MYSQLI_REPORT_ALL);
        // echo $table;
        $this->myQuery = $sql; // Pass back the SQL
		// Check to see if the table exists
        // if($this->tableExists($table)){ 
        	// The table exists, run the query
        	$stmt = $this->prepare($sql, $types, $params);
			if(!$stmt){
                array_push($this->connresult,$this->conn->error);
                return false; // No rows where returned
            }else{
				// If the query returns >= 1 assign the number of rows to numResult
                $result = $stmt->get_result();
                $this->numResults = $result->num_rows;
                while($row=$result->fetch_assoc()){
                    $this->connresult=$row;
                } return true;
			
			}
      	// }else{
      	// 	return false; // Table does not exist
    	// }
    }

    public function select_multiple_async($sql, $which){
        $this->changedb($which);
        // echo $table;
        $this->myQuery = $sql; // Pass back the SQL
		// Check to see if the table exists
        // if($this->tableExists($table)){
        	// The table exists, run the query
            $stmt =$this->conn->query($sql);
            
			if(!$stmt){
                array_push($this->connresult,$this->conn->error);
                return false; // No rows where returned
            }else{
                    // // If the query returns >= 1 assign the number of rows to numResults
                    $this->numResults = $stmt->num_rows;
                    // Loop through the query results by the number of rows returned
                    for($i = 0; $i < $this->numResults; $i++){
                        $r = $stmt->fetch_array();
                        $key = array_keys($r);
                        for($x = 0; $x < count($key); $x++){
                            // Sanitizes keys so only alphavalues are allowed
                            if(!is_int($key[$x])){
                                if(!empty($r)){
                                    $this->connresult[$i][$key[$x]] = $r[$key[$x]];
                             }else{
                                 $this->connresult = null;
                             }
                         }
                        }
                    }
                    return true; // Query was successful


				// // If the query returns >= 1 assign the number of rows to numResults
                // $this->numResults = $stmt->num_rows;
                // // $result = $stmt->get_result();
                // if($row=$stmt->fetch_assoc()){
                //     foreach($row as $key => $value){
                //     $tmp[$key] = &$row[$key];}
                //     $tmp=array_values($tmp);
                //     $this->connresult=$tmp;
                //     return true; // Query was successful
                // }else{
                // array_push($this->connresult,"  ","Naughty Naughty","  ", $stmt->num_rows,"  ", $sql);
                // return false;
                // }
				
			
			}
      	// }else{
      	// 	return false; // Table does not exist
    	// }
    }

    public function select_prepared_async($sql, $which, $types, $params){
        //mysqli_report(MYSQLI_REPORT_ALL);
        $this->changedb($which);
        // echo $table;
        $this->myQuery = $sql; // Pass back the SQL
		// Check to see if the table exists
        // if($this->tableExists($table)){
        	// The table exists, run the query
            $stmt = $this->prepare($sql, $types, $params);
			if(!$stmt){
                array_push($this->connresult, $this->conn->error);
                return false; // No rows where returned
            }else{
                    // // If the query returns >= 1 assign the number of rows to numResults
                    $this->numResults = $stmt->num_rows;###### how sure are we that this works
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
                    if(isset($arr_results)){
                        $this->connresult= $arr_results;
                        return true;
                    }else{
                        return true;
                    }
 

                 }

                    // $a=null; $b=null; $c=null; $d=null; $e=null; $f=null; $g=null; $h=null; $j=null; $k=null; $l=null; $m=null; $n=null; $o=null; $p=null;
                    // $stmt->bind_result($a, $b, $c, $d, $e, $f, $g, $h, $j, $k, $l, $m, $n, $o, $p);
                    // Loop through the query results by the number of rows returned
                    // for($i = 0; $i < $this->numResults; $i++){
                    //     while($r = $stmt->fetch()){
                    //     $key = array_keys($r);
                    //     for($x = 0; $x < count($key); $x++){
                    //         // Sanitizes keys so only alphavalues are allowed
                    //         if(!is_int($key[$x])){
                    //             if(!empty($r)){
                    //             $this->connresult[$i][$key[$x]] = $r[$key[$x]];
                    //          }else{
                    //              $this->connresult = null;
                    //              return false;
                    //          }
                    //      }
                    //     }
                    // }
                    // }
                    // return true; // Query was successful

			// }
      	// }else{
      	// 	return false; // Table does not exist
    	// }
    }


    // protected function selectmultiple($table, $sql, $types, $params){
    //     // echo $table;
    //     $this->myQuery = $sql; // Pass back the SQL
	// 	// Check to see if the table exists
    //     if($this->tableExists($table)){
    //     	// The table exists, run the query
    //     	$stmt = $this->prepare($sql, $types, $params);
	// 		if(!$stmt){
    //             array_push($this->connresult,$this->conn->error);
    //             return false; // No rows where returned
    //         }else{
	// 			// If the query returns >= 1 assign the number of rows to numResults
    //             $this->numResults = $stmt->num_rows;
    //             $result = $stmt->get_result();
	// 			// Loop through the query results by the number of rows returned
	// 			for($i = 0; $i < $this->numResults; $i++){
	// 				$r = $result->fetch_array();
    //             	$key = array_keys($r);
    //             	for($x = 0; $x < count($key); $x++){
    //             		// Sanitizes keys so only alphavalues are allowed
    //                     if(!is_int($key[$x])){
    //                         if($stmt->num_rows() >= 1){//?
    //                             $this->connresult[$i][$key[$x]] = $r[$key[$x]];
    //                      }else{
    //                          $this->connresult = null;
    //                      }
    //                  }
	// 				}
	// 			}
	// 			return true; // Query was successful
			
	// 		}
    //   	}else{
    //   		return false; // Table does not exist
    // 	}
    // }

    public function insert($table, $sql, $types, $params){
        $this->connresult = array();
        // mysqli_report(MYSQLI_REPORT_ALL);
            $this->myQuery = $sql; // Pass back the SQL
            // Make the query to insert to the database
            $stmt = $this->prepare($sql, $types, $params);
            if(!$stmt){
                array_push($this->connresult,$this->conn->error);
                return false; // The data has not been inserted
            }else{
                array_push($this->connresult, $this->conn->insert_id);
                return true; // The data has been inserted
            }
    }

    public function update($table, $sql, $types, $params){
    	// Check to see if table exists
    	// if($this->tableExists($table)){
            // Make query to database
            $stmt = $this->prepare($sql, $types, $params);
            $this->myQuery = $sql; // Pass back the SQL
            if($stmt){
            	array_push($this->connresult,$this->conn->affected_rows);
            	return true; // Update has been successful
            }else{
            	array_push($this->connresult,$this->conn->error);
                return false; // Update has not been successful
            }
        // }else{
        //     return false; // The table does not exist
        // }
    }

    public function delete($table, $sql, $types, $params){
    	// Check to see if table exists
    	// if($this->tableExists($table)){
            // Make query to database
            $stmt = $this->prepare($sql, $types, $params);
            $this->myQuery = $sql; // Pass back the SQL
            if($stmt){
            	array_push($this->connresult,$this->conn->affected_rows);
            	return true; // Update has been successful
            }else{
            	array_push($this->connresult,$this->conn->error);
                return false; // Update has not been successful
            }
        // }else{
        //     return false; // The table does not exist
        // }
    }


    	// Public function to return the data to the user
        public function getResult(){
            $val = $this->connresult;
            $this->connresult = array();
            return $val;
        }
    
        //Pass the SQL back for debugging
        public function getSql(){
            $val = $this->myQuery;
            $this->myQuery = '';
            return $val;
        }
    
        //Pass the number of rows back
        public function numRows(){
            $val = $this->numResults;
            $this->numResults = NULL;
            return $val;
        }
    
        // Escape your string
        public function escapeString($data){
            return $this->conn->real_escape_string($data);
        }


}
