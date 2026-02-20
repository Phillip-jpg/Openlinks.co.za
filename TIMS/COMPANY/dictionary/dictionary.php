<?php

function dictionary($id = NULL){
    $return = '';
    $dictionary = array(
        480210230 => "company_info.php",
        480210260 => "index.php",
        480210270 => "messages.php",
        480210280 => "mySMME.php",
        480210290 => "notifications.php",
        480210300 => "search.php",
        480210310 => "settings.php",
        480210320 => "SMME_userProfile.php",
        480210330 => "consultant.php",
        480210340 => "mySMME_ALL.php",
        480210350 => "mySMME_Chart.php",
        480210360 => "mySMME_CR.php",
        480210370 => "mySMME_SR.php"
    );
    
    if($id == NULL && !is_int($id)){
    $url = explode("/",$_SERVER['PHP_SELF']);
    $page = array_pop($url);
    foreach($dictionary as $key => $val){
        if(preg_match("/".$val."/i",$page)){
            $return = $key;
            break;
        }
    }
    if($return == ''){
        $return = 480210260;
    }


    }else{
        $return = $dictionary[$id];
        if($return == ''){
        $return = $dictionary[480210260];
        }
    }
    return $return;
}