<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.notification.php');
include_once($filepath.'/../config/sql.notify.config.php');
include_once($filepath.'/../classes/notification_body.class.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../helpers/token.php');
// echo "<pre>";
// var_dump($this->master->getResult());
// echo "</pre>";


class chams { //selects all the notifications for a specific entity
    private $master;
    private $id;
    private $who;
    function __construct(){
        $this->master = new Master(DB_NAME_5);
        if(isset($_SESSION['WHO'])){

            $this->who = $_SESSION['WHO'];

            if($_SESSION['WHO'] == "SMME"){

                $this->id=$_SESSION['SMME_ID'];

            }elseif($_SESSION['WHO'] == "NPO"){

                $this->id=$_SESSION['NPO_ID'];

            }elseif($_SESSION['WHO'] == "COMPANY"){

                $this->id=$_SESSION['COMPANY_ID'];

            }elseif($_SESSION['WHO'] == "CONSULTANT"){

                $this->id=$_SESSION['CONSULTANT_ID'];

            }elseif($_SESSION['WHO'] == "P_COMPANY"){

                $this->id=$_SESSION['P_COMPANY_ID'];

            }elseif($_SESSION['WHO'] == "M_ADMIN"){

                $this->id=$_SESSION['ADMIN_ID'];

            }elseif($_SESSION['WHO'] == "G_ADMIN"){

                $this->id=$_SESSION['ADMIN_ID'];

            }else{

                echo "technical error";
                exit();

            }

        }else{
            echo "technical error";
            exit();
        }

    }
    private function who($id){
        $query = $this->master->select_prepared_async(NOTIFICATION_SELECT_WHO[0], DB_NAME_1, NOTIFICATION_SELECT_WHO[1], [$id,$id,$id,$id]);
        if(!$query){
          echo "its this one 1";
          exit();
        }else{
            return $this->master->getResult()[0]["typeOfEntity"];
        }
      }

     function All(){
        // $smme="SMME";
        // $npo="NPO";
        // $company="COMPANY";
        // $who='';
        $params=array($this->id);
        if($_SESSION['WHO'] == "P_COMPANY"){
            $query=$this->master->select_prepared_async(NOTIFICATION_SELECT_P_COMPANY[0], DB_NAME_5,  NOTIFICATION_SELECT_P_COMPANY[1], $params);
        }else{
            $query=$this->master->select_prepared_async( NOTIFICATION_SELECT_ALL[0], DB_NAME_5,  NOTIFICATION_SELECT_ALL[1], $params);
        }
        if(!$query){
            echo "ERROR database 1";
          exit();
        }
        $result=$this->master->getResult();
        
        $bod = new notification_body();
        $cards='';

        for($i=0; $i<=count($result)-1; $i++){
            $From_entity = $this->who($result[$i]['From_']);
           $notification_id = $result[$i]['NOTIFICATION_ID'];
            if($From_entity == "SMME"){
                $which = DB_NAME_1;
                $sql=SMME_NAME_SELECT[0];
                $types=SMME_NAME_SELECT[1];
                $bodywhich = DB_NAME_1;
                $bodysql = SMME_RREAD_SELECT[0];
                $bodytypes = SMME_RREAD_SELECT[1];
                $dateparams = [$result[$i]['To_'], $result[$i]['From_'], 32];
            }
            elseif($From_entity == "COMPANY"){
                $which = DB_NAME_3;
                $sql=COMPANY_NAME_SELECT[0];
                $types=COMPANY_NAME_SELECT[1];
                $bodywhich = DB_NAME_3;
                $bodysql = COMPANY_RREAD_SELECT[0];
                $bodytypes = COMPANY_RREAD_SELECT[1];
                $dateparams = [$result[$i]['From_'], $result[$i]['To_'], 32];
            }
            elseif($From_entity == "CONSULTANT"){
                $which = DB_NAME_4;
                $sql=CONSULTANT_NAME_SELECT[0];
                $types=CONSULTANT_NAME_SELECT[1];
            }
            else{
                echo $result[$i]['From_']."<br>";
                echo "ERROR database 2";
                  exit();
            }
            $this->master->changedb($which);
            $params=array($result[$i]['From_'], $result[$i]['From_'], $result[$i]['From_']);
           
            $query=$this->master->select('smme_company_events', $sql, $types, $params);
            if(!$query){
                echo "ERROR database 3";
                echo "<br>SQL= $sql";
                echo "<br>Types= $types";
                echo "<br>Which= $which";
                print_r($result[$i]['From_']);
              exit();
            }
            $result2=$this->master->getResult();
             
           
            if($From_entity == "COMPANY" && ($result[$i]['EVENT_ID']== 10 || $result[$i]['EVENT_ID']== 12)){
                $bodysql = COMPANY_RREAD_SELECT[0];
                $bodytypes = COMPANY_RREAD_SELECT[1];
                $bodyparams = array($result[$i]['From_']);
                
                $this->master->changedb($bodywhich);
                // print_r($bodysql);
                // echo"<br>";
                // print_r($bodyparams);
                // echo"<br>";
                // exit();
                $bodyquery = $this->master->select("register", $bodysql, $bodytypes, $bodyparams);
                if(!$bodyquery){
                  echo "Yabadabadoo";
                  exit();
                }
                $bodyarray = $this->master->getResult();
              
                $params1 = array($result[$i]['From_']);
                $query1 = $this->master->select_prepared_async(SMME_COMPANY_DIRECTOR_SELECT[0], DB_NAME_1, SMME_COMPANY_DIRECTOR_SELECT[1], $params1);
                if(!$query1){
                  echo "flop";
                  exit();
                }
                $dogecoin2=$this->master->getResult();
                // print_r($result[$i]);
                // exit();
                if($result[$i]["Active"] == 1){
                $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['time'],$bodyarray, $dogecoin2,null )."<br><br>";
                }
            }elseif(($From_entity == "SMME" || $From_entity == "NPO" )&& ($result[$i]['EVENT_ID']== 11)){
                $bodysql = SMME_RREAD_SELECT[0];
                $bodytypes = SMME_RREAD_SELECT[1];
                $bodyparams = array($result[$i]['From_']);
                
                $this->master->changedb($bodywhich);
                // print_r($bodysql);
                // echo"<br>";
                // print_r($bodyparams);
                // echo"<br>";
                // exit();
                $bodyquery = $this->master->select("register", $bodysql, $bodytypes, $bodyparams);
                if(!$bodyquery){
                  echo "Yabadabadoo";
                  exit();
                }
                $bodyarray = $this->master->getResult();
              
                $params1 = array($result[$i]['From_']);
                $query1 = $this->master->select_prepared_async(SMME_COMPANY_DIRECTOR_SELECT[0], DB_NAME_1, SMME_COMPANY_DIRECTOR_SELECT[1], $params1);
                if(!$query1){
                  echo "flop";
                  exit();
                }
                $dogecoin2=$this->master->getResult();
                // print_r($result[$i]);
                // exit();
                if($result[$i]["Active"] == 1){
                    $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'],  $result[$i]['time'],$bodyarray, $dogecoin2,null)."<br><br>";
                }
            }elseif($result[$i]['EVENT_ID']== 32){
                $this->master->changedb(DB_NAME_5);
                $datequery = $this->master->select("smme_company_events", NOTIFICATION_SELECT_EVENT[0], NOTIFICATION_SELECT_EVENT[1], $dateparams);
                if(!$datequery){
                  echo "Yabadabadoo";
                  exit();
                }
                $datearray=$this->master->getResult();
                if($result[$i]["Active"] == 1){
                $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['time'], [], [], $datearray['event_date'])."<br><br>";
                }
            }elseif($result[$i]['EVENT_ID']== 47 || $result[$i]['EVENT_ID']== 49){
                $datearray=$this->master->getResult();
                if($result[$i]["Active"] == 1){
                    $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['time'], [$result[$i]['Description']], [], null)."<br><br>";
                }
            }else{
                if($result[$i]["Active"] == 1){
                    $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['time'], [], [], null)."<br><br>";
                }
            }
           
            
            
        }
        if(empty($cards)){
            echo "<p class='text-capitalize text-center h1' >No notifications</p>";
            exit();
        }
        echo $cards;
    }
    function single($id){
        $id = token::decode($id);
        $params=array($id);
        $query=$this->master->select_prepared_async(NOTIFICATION_SELECT_ALL[0], DB_NAME_5, NOTIFICATION_SELECT_ALL[1], $params);
        if(!$query){
            echo "ERROR database 1";
          exit();
        }
        $result=$this->master->getResult();
        
        $bod = new notification_body();
        $cards='';

        for($i=0; $i<=count($result)-1; $i++){
            
            $From_entity = $this->who($result[$i]['From_']);
            $notification_id = $result[$i]['NOTIFICATION_ID'];
            if($i!==0)echo "<bd><br><br>";
            if($From_entity == "SMME"){
                $which = DB_NAME_1;
                $sql=SMME_NAME_SELECT[0];
                $types=SMME_NAME_SELECT[1];
                $bodywhich = DB_NAME_1;
                $bodysql = SMME_RREAD_SELECT[0];
                $bodytypes = SMME_RREAD_SELECT[1];
                $dateparams = [$result[$i]['To_'], $result[$i]['From_'], 32];
            }
            elseif($From_entity == "COMPANY"){
                $which = DB_NAME_3;
                $sql=COMPANY_NAME_SELECT[0];
                $types=COMPANY_NAME_SELECT[1];
                $bodywhich = DB_NAME_3;
                $bodysql = COMPANY_RREAD_SELECT[0];
                $bodytypes = COMPANY_RREAD_SELECT[1];
                $dateparams = [$result[$i]['From_'], $result[$i]['To_'], 32];
            }
            elseif($From_entity == "CONSULTANT"){
                $which = DB_NAME_4;
                $sql=CONSULTANT_NAME_SELECT[0];
                $types=CONSULTANT_NAME_SELECT[1];
            }
            else{
                echo $result[$i]['From_']."<br>";
                echo "ERROR database 2";
                  exit();
            }
            $this->master->changedb($which);
            $params=array($result[$i]['From_']);
            $query=$this->master->select('smme_company_events', $sql, $types, $params);
            if(!$query){
                echo "ERROR database 3";
                echo "<br>SQL= $sql";
                echo "<br>Types= $types";
                echo "<br>Which= $which";
                print_r($result[$i]['From_']);
              exit();
            }
            $result2=$this->master->getResult();

             

            if($From_entity == "SMME" && ($result[$i]['EVENT_ID']== 10 || $result[$i]['EVENT_ID']== 12)){
                $bodyparams = array($result[$i]['From_']);
                
                $this->master->changedb($bodywhich);
                $bodyquery = $this->master->select("register", $bodysql, $bodytypes, $bodyparams);
                if(!$bodyquery){
                 
                  exit();
                }
                $bodyarray=$this->master->getResult();
                $params1 = array($result[$i]['From_']);
                $query1 = $this->master->select_prepared_async(SMME_COMPANY_DIRECTOR_SELECT[0], "smmes", SMME_COMPANY_DIRECTOR_SELECT[1], $params1);
                if(!$query1){
                  
                  exit();
                }
                $dogecoin2=$this->master->getResult();
                if($result[$i]["Active"] == 1){
                    $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['time'], $bodyarray, $dogecoin2, null)."<br><br>";
                }
                
            }elseif($result[$i]['EVENT_ID']== 32){
                $this->master->changedb(DB_NAME_5);
                $datequery = $this->master->select("smme_company_events", NOTIFICATION_SELECT_EVENT[0], NOTIFICATION_SELECT_EVENT[1], $dateparams);
                if(!$datequery){
                 
                  exit();
                }
                $datearray=$this->master->getResult();
                if($result[$i]["Active"] == 1){
                 $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'],$result[$i]['time'], [], [], $datearray['event_date'] )."<br><br>";
                }
            }else
            if($result[$i]["Active"] == 1){
                $cards.=$bod->notification_contents($notification_id, $result[$i]['EVENT_ID'], $result2['Legal_name'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['time'], [], [], null)."<br><br>";
            }
        }
        if(empty($cards)){
            echo "<p class='text-capitalize text-center h1' ><u>No notifications</u></p>";
            exit();
        }
        echo $cards;
    }
}
