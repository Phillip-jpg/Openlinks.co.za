<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../lib/master.php');
include_once($filepath.'/../config/sql.msg.config.php');
include_once($filepath.'/../config/config.php');


class messages {
protected $master;
protected $id;
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

            }elseif($_SESSION['WHO'] == "ADMIN"){

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

function getchat($to){
    $from = $this->id;
    //$to = $this->decode($to); 
    $sql = GETCHAT_SELECT[0];
    $types = GETCHAT_SELECT[1];
    $params = array($from, $to, $to, $from, $from, $to, $to, $from, $from, $to, $to, $from, $from, $to, $to, $from);
    // echo $to;
    // echo $sql;
    $query=$this->master->select_prepared_async($sql, DB_NAME_5, $types, $params);
    if(!$query){
    echo "error error error";
    exit();
    }else{
    $result1=$this->master->getResult();
    $xi=$this->master->numRows();
    $output = "";
    if(!empty($result1)){
        for($i=0; $i<=count($result1)-1; $i++){
            if($i==0){
                $output .= '<div class="datey">
                <p>'. $this->datetime($result1[$i]['date']) .'</p>
                </div>';
            }elseif($result1[$i]['date']!==$result1[$i-1]['date']){
                $output .= '<div class="datey">
                <p>'. $this->datetime($result1[$i]['date']) .'</p>
                </div>'; 
            }
      if($result1[$i]['From_'] === $from){
            $output .= '<div class="chat outgoing">
            <div class="details">
            <p>'. $result1[$i]['message'] .'<s>  '. $this->time($result1[$i]['time']) .'</s></p>
            </div>
            </div>';
          }else{
              $output .= '<div class="chat incoming">
              <img src="'.$result1[$i]['ext'].'" alt="">
              <div class="details">
              <p>'. $result1[$i]['message'] .'<s class="timey">  '. $this->time($result1[$i]['time']) .'</s></p>
              </div>
              </div>';
          }}
      }else{
      $output .= '<br><div class="text">No messages are available. Once you send message they will appear here.</div>';
      
    }
  echo $output;
  $this->seen($to);
  }
}

function insertchat($to, $from, $message){
    //$to = $this->decode($to);
    if($message == ""){
    exit();
    }
    $params=array($to, $from, $message);
    $query=$this->master->Insert('messages', INSERTCHAT_INSERT[0], INSERTCHAT_INSERT[1], $params);
    if(!$query){
        echo "error error";
        exit();
      }
}

function search($from, $search){
    $output = "";
    $params=array($from, $from, $from, $search, $from, $from, $from, $search, $from, $from, $from, $search, $from, $from, $from, $search);
    $query=$this->master->select_prepared_async(SEARCHCHAT_SELECT[0], DB_NAME_5, SEARCHCHAT_SELECT[1], $params);
    if(!$query)
    {
        echo "error";
        exit();
    }else
    {
        $sets=[];
        $result=$this->master->getResult();
        $deleted=0;
        for($i=0; $i<=count($result)-1; $i++){
            $currSet =[
                $result[$i]["To_"] / $result[$i]["From_"],// x/y = a/b
                $result[$i]["From_"] / $result[$i]["To_"],// y/x = b/a
                $result[$i]["To_"] - $result[$i]["From_"],// x-y = a-b
                $result[$i]["From_"] - $result[$i]["To_"]//  y-x = b-a
                ];
            for($j=0; $j<=count($sets)-1; $j++){
                if(empty(array_diff($currSet, $sets[$j]))){
                    array_splice($result, $i, 1);
                    $deleted++;
                    $i--;
                }
            }
            array_push(
                $sets,
                $currSet
            );
        }
        if(!empty($result))
        {
            $output .= $this->data($result, $from);
            
        }
        else
        {
            $output .= 'No user found related to your search term';
        }
    }
    echo $output;
}

private function datetime($date, $time = NULL){
    $today = date("Y-m-d");
    $ctime = date("h:i:s");
    $yesterday = date("Y-m-d", strtotime("yesterday"));
    $return = "";
    if ($date == $today){
        $return .= "Today";
    }elseif($date == $yesterday){
        $return .= "Yesterday";
    }else{
        if(date("Y", strtotime($today)) == date("Y", strtotime($date))){
            // if(date("m", strtotime($today)) == date("m", strtotime($date))){
            //     $return .= date("N jS", strtotime($date));
            // }else{
            //     $return .= date("jS F", strtotime($date));
            // }
            $return .= date("jS F", strtotime($date));
        }else{
            $return .= date("j F Y", strtotime($date));
        }
    }
    if($time !== NULL){
        $return .= "<br>". $this->time($time);
    }
    return $return;
}

private function time($time){
    return date("H:i", strtotime($time));
}

function getusers($from){
    $output = "";
    $params=array($from, $from, $from, $from, $from, $from, $from, $from, $from, $from, $from, $from);
    $query=$this->master->select_prepared_async( GETUSERSCHAT_SELECT[0], DB_NAME_5, GETUSERSCHAT_SELECT[1], $params);
    if(!$query)
    {
        echo "error";
        exit();
    }else
    {
        $sets=[];
        $result=array_reverse($this->master->getResult());
        
        $deleted=0;
        for($i=0; $i<=count($result)-1; $i++){
            $currSet =[
                $result[$i]["To_"] / $result[$i]["From_"],// x/y = a/b
                $result[$i]["From_"] / $result[$i]["To_"],// y/x = b/a
                $result[$i]["To_"] - $result[$i]["From_"],// x-y = a-b
                $result[$i]["From_"] - $result[$i]["To_"]//  y-x = b-a
                ];

            for($j=0; $j<=count($sets)-1; $j++){
                if(empty(array_diff($currSet, $sets[$j]))){
                    array_splice($result, $i, 1);
                    $deleted++;
                    $i--;

                }
            } 

            array_push(
                $sets,
                $currSet
            );
        }
        if(!empty($result))
        {
            $output .= $this->data($result, $from);

        }
        else
        {
            $output .= "No users are available to chat<br>";
        }
    }   
     echo $output;
}

function dynamicUser($id, $decrypt){
    // if($decrypt){
    // $id = $this->decode($id);
    // }
    $params=array($id, $id, $id, $id);
    $this->master->changedb(DB_NAME_1);
    $query = $this->master->select("signup", DYNAMICUSER_SELECT[0], DYNAMICUSER_SELECT[1], $params);
    if(!$query){
        echo "<b>Error in Database</br>";
        exit();
      }$xi=$this->master->numRows();
      if($xi==0 || $xi==NULL)
      {
        echo "<b>Result empty</b>";
        exit();
      }
    $result=$this->master->getResult();
    return $result;
}

function seen($id){
    //$id = $this->decode($id);
    $params=array(1, 0, $this->id, $id);
    $query = $this->master->update("messages", SEEN_UPDATE[0], SEEN_UPDATE[1], $params);
    if(!$query){
        echo "<b>Error in Database</br>";
        exit();
      }
}

function mark_all_as_read(){
    $params=array(1, 0, $this->id);
    $query = $this->master->update("messages", MAAR_UPDATE[0], MAAR_UPDATE[1], $params);
    if(!$query){
        echo "<b>Error in Database</br>";
        exit();
      }
}

function header_unread(){
    $query3 = $this->master->select("signup", HEADER_UNREAD_SELECT[0], HEADER_UNREAD_SELECT[1], array($this->id));
    $status=$this->master->getResult();
    $s = $status['status'];
    if($s == 0){
        return '<i class="fa fa-envelope-o"></i>';
    }else{
        return '<i class="fa fa-envelope-o"></i>
        <span class="badge bg-green">'.$status['status'].'</span>';
    }
}

private function data(array $result, $from){
    if(empty($result)){
        return "Oops! Seems like there are no chats yet.";
        exit();
    }
    $strarr = [];
    $return = '';
    for($i=0; $i<=count($result)-1; $i++){
        $query2 = $this->master->select("signup", DATACHAT_SELECT[0], DATACHAT_SELECT[1], array($result[$i]['To_'], $result[$i]['To_'], $result[$i]['From_'], $result[$i]['From_']));
        $row2 = $this->master->getResult();
        $xi=$this->master->numRows(); 
        ($row2["date"] == date("Y-m-d")) ? $timey = $this-> time($row2["time"]) : $timey = $this->datetime($row2["date"]);
        ($xi > 0) ? $message = $row2['message'] : $message ="No message available";
        (strlen($message) > 28) ? $msg =  substr($message, 0, 28) . '...' : $msg = $message;
        if(isset($row2['From_'])){
            ($from == $row2['From_']) ? $you = "You: " : $you = "";
        }else{
            $you = "";
        }
        
        // ($result[$i]['status'] == "Offline now") ? $offline = "offline" : $offline = "";
        $offline='';#remove this
        ($result[$i]['To_'] == $this->id) ? $id = $result[$i]['From_'] : $id = $result[$i]['To_'];
        $query3 = $this->master->select("signup", DATA_UNREAD_SELECT[0], DATA_UNREAD_SELECT[1], array($this->id, $id));
        $status=$this->master->getResult();
        include_once "../helpers/token.php";
        $encode = token::encode($id);
        ($status['status'] > 0) ? $dotty = '<div class="dotty">'.$status['status'].'</div>' : (($status['status'] > 99) ? $dotty = '<div class="dotty">99+</div>' : $dotty = '');
        $output = '<a href="chat.php?url='.  $encode .'">
                    <div class="content">
                    <img src="'. $result[$i]['ext'] .'" alt="">
                    <div class="details">
                        <span>'. $result[$i]['Legal_name'].'</span>
                        <p>'. $you . $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot '. $offline .'"><small><small class="timey">'.$timey.'<br></small></small>
                    '.$dotty.'</div>
                </a>';
                
                if (empty($strarr)){ // assuming strarr is empty
                    array_push($strarr, array(
                        "date" => $row2["date"],
                        "time" => $row2["time"],
                        "div"  => $output
                    ));
                }else{
                
        for($j=0; $j<=count($strarr)-1; $j++){

            if ($row2["date"] > $strarr[$j]["date"] || ($row2["date"] == $strarr[$j]["date"] && $row2["time"] > $strarr[$j]["time"])){
                //if this one happened more recently than the current strarr
                $replacement=array(
                    array(  
                    "date" => $row2["date"],
                    "time" => $row2["time"],
                    "div"  => $output
                ));
                $jplus = $j;
                $res = array_merge(
                array_slice($strarr, 0, $jplus),
                $replacement,
                array_slice($strarr, $jplus, count($strarr)-$jplus)
                );
                $strarr = $res;
                break;
            } 
            if($j==count($strarr)-1){
                array_push($strarr, array(
                    "date" => $row2["date"],
                    "time" => $row2["time"],
                    "div"  => $output
                ));
                break;
            }
        }
        
        }


    }
    foreach ($strarr as $value){
        $return .= $value["div"];
    }
    return $return;
}

private static function safe_b64encode($string='') {
    $data = base64_encode($string);
    $data = str_replace(['+','/','='],['-','_',''],$data);
    return $data;
}

private static function safe_b64decode($string='') {
    $data = str_replace(['-','_'],['+','/'],$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

private function encode($value){ 
    $iv_size = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($iv_size);
    $crypttext = openssl_encrypt($value, 'aes-256-cbc', 'eb6b3684a6291e3832e266fd3da713a15aba2c2f91bb1c92126d1a030bf58d7e', OPENSSL_RAW_DATA, $iv);
    return self::safe_b64encode($iv.$crypttext); 
}

private function decode($value){
    $crypttext = self::safe_b64decode($value);
    $iv_size = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($crypttext, 0, $iv_size);
    $crypttext = substr($crypttext, $iv_size);
    if(!$crypttext) return false;
    $decrypttext = openssl_decrypt($crypttext, 'aes-256-cbc', 'eb6b3684a6291e3832e266fd3da713a15aba2c2f91bb1c92126d1a030bf58d7e', OPENSSL_RAW_DATA, $iv);
    return rtrim($decrypttext);
}


}