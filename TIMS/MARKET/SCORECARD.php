<?php 
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../config/sql.config.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/CRITERIA.php');

class SCORECARD{
    public $title;
    public $description;
    public $type;
    public $criteria;
    public $date;
    public $ID;
private $other;
    protected $master;

    function __construct($name, $desc, $date, $id){
        $this->criteria = array();
        $this->title = $name;
        $this->description = $desc;
        $this->date = $date;
        $this->ID = $id;
        $this->master=new Master("yasccoza_openlink_market");
        $this->buildScoreCard();
    }



    protected function UploadFile($form,$fileName,$fileTmpName,$fileSize,$fileError){
      
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg','jpeg','png','pdf');
        $images = array('jpg','jpeg','png');
        if(in_array($fileActualExt, $allowed)){
          
            if($fileError== 0){
              
                if($fileSize < 2000000){
                  
                    $fileNameDelete = token::encode1($fileName).token::encode1(session::get($this->id))."_".$form.".".$fileActualExt;
                    
                    $fileNameNew = token::encode1($fileName).token::encode1(session::get($this->id))."_".$form.".".$fileActualExt;
                    
                    if(in_array($fileActualExt, $images)){
                      $fileDestination = '../STORAGE/IMAGES/'.$fileNameNew;
                      
                    }else{
                      $fileDestination = '../STORAGE/FILES/'.$fileNameNew;
                    }
                    
                    if(file_exists($fileNameDelete)){
                      unlink($fileNameDelete);
                    }
                    
                    move_uploaded_file($fileTmpName, $fileDestination);
                    
                    
                    return $fileNameNew;
                }
                else{
                    return "too large";
                }
            }
            else{
                return "file error" ;
            }
        }
        else{
            return "not right file";
        }
    }


    private function buildScoreCard(){
        $criterias = $this->fetchCriteria();
        for($i = 0; $i < count($criterias); $i++){
            $temp = new CRITERIA($criterias[$i]['Description'],$criterias[$i]['Name'],$criterias[$i]['Weighting'], $criterias[$i]['CRITERIA_ID']);
            $this->addToCriteria($temp);
        }
    }
    public function getCriteria(){
        $display ="";
        $count = 0;
        if(empty($this->criteria))$display .= "None yet";
        for($i = 0; $i < count($this->criteria); $i++){
            if($i +1 == count($this->criteria)) $display .= $this->criteria[$i]->type;
            else $display .= $this->criteria[$i]->type . ", "; 
            $count++;
        }
        return $count;
    }
    private function fetchCriteria(){
        $sql = "SELECT * FROM criteria c,scorecard_criteria sc WHERE c.CRITERIA_ID = sc.CRITERIA_ID AND sc.SCORECARD_ID=?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($this->ID);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    
    public function getSimpleDisplay(){
        
        $display = '<table class="table"><tbody>
        <tr><td>Title:  </td><td>'.$this->title.'</td></tr>
        <tr><td>Other Information:  </td><td>'.$this->description.'</td></tr>
           <tr><td>Date of Expiry:</td><td>'.$this->date.'</td></tr>
        </tbody></table>';
        return $display;
    }

    private function fetch($table, $sql, $types, $params){
        $query = $this->master->select_prepared_async($sql, $table, $types, $params);
        if(!$query){

        }else{
            $result = $this->master->getResult();
            return $result;
        }
    }
    private function save($sql, $types, $params, $table){
        $query = $this->master->insert($table,$sql, $types, $params);
        if(!$query){
            return -1;
        }else{
            return 1;
        }
    }
    private function addToCriteria($criteria_){
        array_push($this->criteria, $criteria_);
    }

    public function DisplayScoreCard($id){
        //displays all a users scorecards

    }
    private function createScoreCard(){

    }


   
}