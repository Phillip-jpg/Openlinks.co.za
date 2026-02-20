<?php 

class CRITERIA{
    public $description;
    public $type;
    public $questions;

    
    public $ID;

    protected $master;

    function __construct($desc, $name, $id){
        
        $this->description = $desc;
        $this->type = $name;
        $this->ID =$id;
        $questions = array();
        $this->master=new Master("yasccoza_openlink_market");
    }

    private function initQuestionsArray(){
        $questions = $this->fetchQuestions($this->ID);
        foreach($questions as $question){
            $this->addToQuestions($question);
        }
    }
    public function saveCriteria($type, $descr){
        $this->type = $type;
        $this->description = $descr;
        $sql = "INSERT INTO CRITERIA() VALUES()";
        $types="";
        $params = array($type, $descr);
        $table ="";
        $status = $this->save($table, $sql, $type, $params);
        return $status;
    }
    protected function UploadFile($form,$fileName,$fileTmpName,$fileSize,$fileError){
      
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $allowed = array('jpg','jpeg','png','pdf');
        $images = array('jpg','jpeg','png');
        if(in_array($fileActualExt, $allowed)){
          
            if($fileError== 0){
              
                if($fileSize < 2000000){
                  
                    $fileNameDelete = token::encode1($fileName).token::encode1(session::get($this->ID))."_".$form.".".$fileActualExt;
                    
                    $fileNameNew = token::encode1($fileName).token::encode1(session::get($this->ID))."_".$form.".".$fileActualExt;
                    
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
    public function addQuestion($question, $weight, $id){
        //get all the users scorecards
        //display them and wait for a response
        //catch response and trigger createCriteria
        for($i = 0; $i < count($question); $i++){
            $question = new QUESTION($question[$i], $weight[$i], $id);
            $question->createQuestion();
            $this->addToQuestions($question);
        }
         
    }
    public function getSimpleDisplay(){
        $display = '<table class="table"><tbody>
        <tr><td>Name: </td><td>'.$this->type.'</td></tr>
        <tr><td>Supporting Document:  </td><td>'.$this->description.'</td></tr>
        </tbody></table>';
        return $display;
    }
    private function save($sql, $types, $params, $table){
        $query = $this->master->insert($table,$sql, $types, $params);
        if(!$query){
            return -1;
        }else{
            return 1;
        }
    }
    private function addToQuestions($question){
        array_push($this->questions, $question);
    }
    private function fetchQuestions($id){
        $sql = "SELECT * FROM yasccoza_openlink_market.question WHERE CRITERIA_ID = ?";
        $table = "yasccoza_openlink_market";
        $types="i";
        $params=array($id);
        $result = $this->fetch($table, $sql, $types, $params);
        return $result;
    }
    public function questions(){
        $questions = $this->fetchQuestions($this->ID);
        $display = "";
        for($i = 0; $i < count($questions); $i++){
            $display .= "
                ".$questions[$i]['Question']." - ".$questions[$i]['Weighting']."%
            ";
        }
        $count = count($questions);
        return $count;
    }
    private function fetch($table, $sql, $types, $params){
        $query = $this->master->select_prepared_async($sql, $table, $types, $params);
        if(!$query){

        }else{
            $result = $this->master->getResult();
            return $result;
        }
    }
}