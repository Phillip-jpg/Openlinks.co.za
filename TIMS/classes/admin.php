<?php
use PHPMailer\PHPMailer\Exception;

// require 'mail.extend.php';

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.admin.php');
include_once($filepath.'/../config/config.php');
include_once($filepath.'/../view/admin_view/view.php');

abstract class admin2 {
protected $master;
function __construct(){
    $this->id = $_SESSION[$this->idname];
    $this->master = new Master($this->db);
}

public function pool(){
    $query=$this->master->select_multiple_async($this->POOL_SELECT[0], DB_NAME_5);
    if(!$query){
        echo "flop";
        exit();
    }
    else{
     $result=$this->master->getResult();
    }
}
public function mypool(){
    $params = array();
    $query=$this->master->select_prepared_async($this->MYPOOL_SELECT[0], DB_NAME_5, $this->MYPOOL_SELECT[1], $params);
    if(!$query){
        echo "flop";
        exit();
    }
    else{
     $result=$this->master->getResult();
    }
}

public function take_enq($id){
    $params=array(2, $id);
    $query=$this->master->update('', $this->ENQ_UPDATE[0], $this->ENQ_UPDATE[1], $params);
    if(!$query){
        "Flop";
        exit();
      }
      else{
          Echo "taken...";
      }
}

public function make_enq($id){
    $params=array($id);
    $query=$this->master->insert('', $this->ENQ_INSERT[0], $this->ENQ_INSERT[1], $params);
    if(!$query){
        "Flop";
        exit();
      }
      else{
          Echo "Made...";
      }
}

public function throw_back_enq($id){
    $params=array(1, $id);
    $query=$this->master->update('', $this->ENQ_UPDATE[0], $this->ENQ_UPDATE[1], $params);
    if(!$query){
        "Flop";
        exit();
      }
      else{
          Echo "Thrown Back...";
      }
}

public function complete_enq($id){
    $params=array(3, $id);
    $query=$this->master->update('', $this->ENQ_UPDATE[0], $this->ENQ_UPDATE[1], $params);
    if(!$query){
        "Flop";
        exit();
      }
      else{
          Echo "Completed...";
      }
}

public function get_enq($id){
    $params=array($id);
    $query=$this->master->SELECT('', $this->ENQ_UPDATE[0], $this->ENQ_UPDATE[1], $params);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}
//analytics
public function PROGRESS_PROCESS_SELECT(){
    
    $query=$this->master->select_multiple_async($this->PROGRESS_PROCESS_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }else{
        $result=$this->master->getResult();
      }
}
public function PROCESS_AVERAGE_TIME_SELECT(){
 
    $query=$this->master->select_multiple_async($this->PROCESS_AVERAGE_TIME_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}
public function PAGE_VISITS_GRAPGH(){
    $query=$this->master->select_multiple_async($this->PAGE_VISITS_GRAPGH[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}
public function page_visits(){
    $min = $this->MIN_PAGE_VISITS_SELECT;
    $max = $this->MIN_PAGE_VISITS_SELECT;
    $average = $this->MIN_PAGE_VISITS_SELECT;
    $display = VIEW::page_visits($min, $max, $average);
    return $display;
}
private function MAX_PAGE_VISITS_SELECT(){
    $query=$this->master->select_multiple_async($this->MAX_PAGE_VISITS_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
        return $result['Visits'];
      }
}
private function MIN_PAGE_VISITS_SELECT(){
    $query=$this->master->select_multiple_async($this->MIN_PAGE_VISITS_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
        return $result['Visits'];

      }
}
private function AVERAGE_PAGE_VISITS_SELECT(){
    $query=$this->master->select_multiple_async($this->AVERAGE_PAGE_VISITS_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
        return $result['average_visits'];

      }
}
public function SEARCH_GRAPGH_SELECT(){
    $query=$this->master->select_multiple_async($this->SEARCH_GRAPGH_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}
public function search_terms(){
    $most_searched_name = $this->MOST_SEARCHED_NAME_SELECT();
    $most_searched_industry = $this->MOST_SEARCHED_INDUSTRY();
    $most_searched_product = $this->MOST_SEARCHED_PRODUCT();
    $display = VIEW::search_stats($most_searched_name, $most_searched_industry, $most_searched_product);
    return $display;
}
private function MOST_SEARCHED_NAME_SELECT(){
    $query=$this->master->select_multiple_async($this->MOST_SEARCHED_NAME_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
        return $result;
      }
}
private function MOST_SEARCHED_INDUSTRY(){
    $query=$this->master->select_multiple_async($this->MOST_SEARCHED_INDUSTRY[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
        return $result;
      }
}
private function MOST_SEARCHED_PRODUCT(){
    $query=$this->master->select_multiple_async($this->MOST_SEARCHED_PRODUCT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
        return $result;
      }
}
public function CURRENT_DAY_SEARCHES_SELECT(){
    $query=$this->master->select_multiple_async($this->CURRENT_DAY_SEARCHES_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}
public function ALL_EMAILS_SENT_SELECT(){
    $query=$this->master->select_multiple_async($this->ALL_EMAILS_SENT_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}
public function ALL_CLICKED_EMAILS_SELECT(){
    $query=$this->master->select_multiple_async($this->ALL_CLICKED_EMAILS_SELECT[0], $this->var5);
    if(!$query){
        "Flop";
        exit();
      }
      else{
        $result=$this->master->getResult();
      }
}

}