<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.posting.php');
include_once($filepath.'/../config/config.php');


abstract class posting {
protected $master;
function __construct(){
    $this->master = new Master($this->db);

}

public function post($description){
    $this->id = $_SESSION[$this->idname];
    $params = array($this->id, $description);
    echo $this->db;
    $this->master->changedb($this->db);
    mysqli_report(MYSQLI_REPORT_ALL);
    
    $query=$this->master->Insert('posting', $this->POSTING_INSERT[0], $this->POSTING_INSERT[1], $params);
    if(!$query){
      echo "flop 2";
      exit();
    }
}
public function viewpostings(){
    $output = "";
    $query = $this->master->select_multiple_async($this->POSTING_SELECT[0], $this->db);
    if(!$query){
        echo "naughty";
        exit();
    }else{
        $result = $this->master->getResult();
        if(empty($result)){
            $output .= "<b>Oops, seems like theres no one here yet</b>";
        }else{
        for($i=0; $i<=count($result)-1; $i++){
            $output .= "<div>";
            $output .= "<div>";
            if($this->who == "cons")$name=$result[$i]["First_Name"];
            else $name=$result[$i]["Legal_name"];
            $output .= $name;
            $output .= "</div>";
            $output .= "<div>";
            $output .= $result[$i]["description"];
            $output .= "</div>";
            $output .= "</div>";
            $output .= "<br>";
        }
    }
        echo $output;
    }
}
}