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



private $mysqliconnection ;
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
        $this->mysqliconnection = $this->conn;
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

private function prepare2($sql){
   
    
    $stmt=$this->conn->stmt_init();
    $this->myQuery = $sql; // Pass back the SQL
    if(!$stmt->prepare($sql)){
        return false;
    }else{
        if($stmt->execute()){
            return $stmt;
        }else{
            return false;
        }
    }
}

public function getError() {
    return $this->conn->error;
}

// private function tableExists($table){
//     $sql="SHOW TABLES FROM '?';";
//     $params=array($table);
//     $stmt=$this->prepare($sql, "s", $params);
//     if(!$stmt){
//         header("location: ../home.php?error=databaseErrorNotPrepare");
//         exit();
//     }
//         $tablesInDb = $stmt->get_result();
//     if($tablesInDb){
//         if($stmt->num_rows() == 1){
//             return true; // The table exists
//         }else{
//             array_push($this->connresult,$table." does not exist in this database");
//             return false; // The table does not exist
//         }
//     }
// }

    // protected function sql($sql){
    //     $this->conn =new mysqli($this->servername, $this->dbUsername, $this->dpPassword, $this->dbName);
    //     $vararray=array();
    //     $types="";
    //     $stmt=$this->prepare($sql, $types, $vararray);
    //         // If the query returns >= 1 assign the number of rows to numResults
    //         $this->numResults = $stmt->num_rows();
    //         $result = $stmt->get_result();
	// 		// Loop through the query results by the number of rows returned
	// 		for($i = 0; $i < $this->numResults; $i++){
	// 			$r = $result->fetch_array();
    //            	$key = array_keys($r);
    //            	for($x = 0; $x < count($key); $x++){
    //            		// Sanitizes keys so only alphavalues are allowed
    //                	if(!is_int($key[$x])){
    //                		if($stmt->num_rows() >= 1){//?
    //                			$this->connresult[$i][$key[$x]] = $r[$key[$x]];
	// 					}else{
	// 						$this->result = null;
	// 					}
	// 				}
	// 			}
	// 		}
	// 		return true; // Query was successful
    // }


    // select($table, $rows, $join, $where, $order, $limit)
    // it is IMPORTANT that they are in THIS ORDER!


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
    public function select_non_params($sql){
        //mysqli_report(MYSQLI_REPORT_ALL);
       // echo $table;
       $this->myQuery = $sql; // Pass back the SQL
       // Check to see if the table exists
       // if($this->tableExists($table)){ 
           // The table exists, run the query
           $stmt = $this->prepare2($sql);
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
                
    }

    

    public function insert($table, $sql, $types, $params){
        $this->connresult = array();
        //mysqli_report(MYSQLI_REPORT_ALL);
            $this->myQuery = $sql; // Pass back the SQL
            // Make the query to insert to the database
            //print_r($params);
            //exit();
            $stmt = $this->prepare($sql, $types, $params);
            if(!$stmt){
                array_push($this->connresult,$this->conn->error);
                return false; // The data has not been inserted
            }else{
                array_push($this->connresult, $this->conn->insert_id);
                return true; // The data has been inserted
            }
    }
    
    
public function selectalot($sql, $types, $params){
    // Count the number of parameters to create the correct number of placeholders
    $placeholders = implode(',', array_fill(0, count($params), '?'));
    
    // Replace a single placeholder in the original SQL with multiple placeholders
    $sql = str_replace("IN (?)", "IN ($placeholders)", $sql);

    // Prepare the SQL statement
    $stmt = $this->conn->prepare($sql);
    if ($stmt === false) {
        trigger_error('SQL Error: ' . $this->conn->error, E_USER_ERROR);
        return false;
    }

    // The array that will be used in call_user_func_array must be an array of references
    $bind_names = [];
    $bind_names[] = $types;
    foreach ($params as $key => $value) {
        $bind_names[] = &$params[$key];
    }

    // Bind the parameters dynamically
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    } else {
        $stmt->close();
        return false; // Handle error or return an empty array based on your application's needs
    }
}


    
    
    
    /* 
    For the transaction code below, we use the variable x to keep track of the success of each 
    */
    public function transactionInsert($insertTables, $insertSQL, $insertTypes, $insertParams){
        $x = 0;
        $number = count($insertTables);
        mysqli_begin_transaction($this->mysqliconnection);
        for($i = 0; $i < $number; $i++){
            $res = $this->insert($insertTables[$i], $insertSQL[$i], $insertTypes[$i], $insertParams[$i]);
            if($res){
                $x += 1;
            }
        }
        mysqli_commit($this->mysqliconnection);
        if($x < $number){
            return false;
        }else{
            return true;
        }
    }

    public function transactionSelect($selectTables, $selectSQL, $selectTypes, $selectParams){
        $x = 0;
        $result = array();
        $number = count($selectTables);
        //print_r($selectSQL);
        //exit();
        mysqli_begin_transaction($this->mysqliconnection);
        for($i = 0; $i < $number; $i++){
            $res = $this->select_prepared_async($selectSQL[$i], $selectTables[$i], $selectTypes[$i], $selectParams[$i]);
            if($res){
                
                $data = $this->getResult();
                array_push($result, $data);
                $x += 1;
            }
        }
        mysqli_commit($this->mysqliconnection);
        if($x < $number){
            return false;
        }else{
            //print_r($result);
            return $result;
        }
    }
    public function transactionUpdate($selectTables, $selectSQL, $selectTypes, $selectParams){
        $x = 0;
        $result = array();
        $number = count($selectTables);
        // print_r($selectParams);
        // exit();
        //mysqli_report(MYSQLI_REPORT_ALL);
        mysqli_begin_transaction($this->mysqliconnection);
        for($i = 0; $i < $number; $i++){
            $res = $this->update($selectTables[$i],$selectSQL[$i], $selectTypes[$i], $selectParams[$i]);
            if($res){
                
                $data = $this->getResult();
                array_push($result, $data);
                $x += 1;
            }
        }
        mysqli_commit($this->mysqliconnection);
        if($x < $number){
            return false;
        }else{
            //print_r($result);
            return true;
        }
    }
     public function update($table, $sql, $types, $params, $status=null){
    	// Check to see if table exists
    	// if($this->tableExists($table)){
            // Make query to database
            if($status == 1){
                $this->changedb("openlink_association_db");
            }
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
        public function getLastID(){
            $id = mysqli_insert_id($this->conn);
            return $id;
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
