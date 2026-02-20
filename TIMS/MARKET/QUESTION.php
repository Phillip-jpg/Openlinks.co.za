<?php 
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');

class QUESTION{
    private $question;
    private $weight;
    protected $master;
    private $criteria;

    function __construct($quest, $rating, $criteria_id){
        $question = $quest;
        $weight = $rating;
        $criteria = $criteria_id;
        $this->master=new Master("yasccoza_openlink_market");
    }
    public function createQuestion(){
        $sql = "INSERT INTO yasccoza_openlink_market.question(QUESTION, WEIGHT, CRITERIA) VALUES (?, ?, ?)";
        $types = "sii";
        $table = "question";
        $params = array($this->question, $this->weight, $this->criteria);
        $status = $this->save($sql, $types, $table, $params);
        return $status;
    }
    private function save($sql, $types, $params, $table){
        $query = $this->master->insert($table,$sql, $types, $params);
        if(!$query){
            return -1;
        }else{
            return 1;
        }
    }
}