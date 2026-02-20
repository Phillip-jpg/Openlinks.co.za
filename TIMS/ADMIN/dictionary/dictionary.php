<?php

function dictionary($id = NULL){
    $return = '';
    $dictionary = array(
        210920263 => "index.php",
        210920273 => "messages.php",
        210920293 => "notifications.php",
        210920303 => "search.php",
        210920313 => "settings.php"
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
        $return = 210920263;
    }


    }else{
        
        $return = $dictionary[$id];
        if($return == ''){
        $return = $dictionary[210920263];
        }
    }
    return $return;
}