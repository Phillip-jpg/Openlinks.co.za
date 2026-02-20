<?php

function dictionary($id = NULL){
    $return = '';
    $dictionary = array(
        710120230 => "company_info.php",
        710120250 => "consultant_info.php",
        710120260 => "index.php",
        710120270 => "messages.php",
        710120290 => "notifications.php",
        710120300 => "search.php",
        710120310 => "settings.php",
        710120320 => "consultant_mode.php",
        710120420 => "companies.php",
        710120540 => "submit_link.php"
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
        $return = 710120260;
    }


    }else{
        $return = $dictionary[$id];
        if($return == ''){
        $return = $dictionary[710120260];
        }
    }
    return $return;
}