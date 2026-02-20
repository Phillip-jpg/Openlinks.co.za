<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.extra.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../lib/Session.php');

class extra {
protected $master;
protected $id;
protected $who;
function __construct(){
    $who='';
    $db='';
    $SEARCH_INSERT = '';
    if(isset($_SESSION['SMME_ID'])){
        $who = 'SMME';
        $db = DB_NAME_1;
    }elseif(isset($_SESSION['COMPANY_ID'])){
        $who = 'COMPANY';
        $db = DB_NAME_3;
    }elseif(isset($_SESSION['ADMIN_ID'])){
        $who = 'COMPANY';
        $db = DB_NAME_3;
    }else{
        echo "ERROR not in session";
        exit();
    }
    $this->master = new Master($db);
}

public function offices($id){
    if(session::get("WHO")=="NPO"){
        $this->NPO_offices($id);
        exit();
    }
    $query = $this->master->select_prepared_async(OFFICES_SELECT[0], DB_NAME_5, OFFICES_SELECT[1], [$id]);
    if(!$query){
        echo "naughty";
        exit();
    }else{
        $result = $this->master->getResult();
        for($i=0; $i<=count($result)-1; $i++){
            if($i==0)echo "<option value='' disabled selected> Select your specific industry </option>";
            echo "<option value='".$result[$i]["TITLE_ID"]."'>".$result[$i]["title"]."</option>";
        }
    }
}

public function officesnr($id){

    $query = $this->master->select_prepared_async(OFFICES_NR_SELECT[0], DB_NAME_5, OFFICES_NR_SELECT[1], [$id]);
    if(!$query){
        echo "naughty";
        exit();
    }else{
        $result = $this->master->getResult();
        for($i=0; $i<=count($result)-1; $i++){
            if($i==0)echo "<option value='' selected> --blank-- </option>";
            echo "<option value='".$result[$i]["title"]."'>".$result[$i]["title"]."</option>";
        }
    }
}

private function NPO_offices($id){
    $query = $this->master->select_prepared_async(NPO_OFFICES_SELECT[0], DB_NAME_5, NPO_OFFICES_SELECT[1], [$id]);
    if(!$query){
        echo "naughty";
        exit();
    }else{
        $result = $this->master->getResult();
        for($i=0; $i<=count($result)-1; $i++){
            if($i==0)echo "<option value='' disabled selected> Select your specific industry </option>";
            echo "<option value='".$result[$i]["NPO_TITLE_ID"]."'>".$result[$i]["title"]."</option>";
        }
    }
}

}