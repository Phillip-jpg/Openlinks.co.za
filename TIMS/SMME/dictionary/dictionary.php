<?php

function dictionary($id = NULL){
    $return = '';
    $dictionary = array(
        210220200 => "admin_info.php",
        210220210 => "company_data.php",
        210220220 => "company_dir.php",
        210220230 => "company_info.php",
        210220240 => "company_statement.php",
        210220250 => "expense_summary.php",
        210220260 => "index.php",
        210220270 => "messages.php",
        210220290 => "notifications.php",
        210220300 => "search.php",
        210220310 => "settings.php",
        210220320 => "SMME_userProfile.php",
        210220330 => "myBBBEE_ALL.php",
        210220340 => "myBBBEE_Charts.php",
        210220350 => "myBBBEE_CR.php",
        210220360 => "myBBBEE_SR.php"
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
        $return = 210220260;
    }


    }else{
        $return = $dictionary[$id];
        if($return == ''){
        $return = $dictionary[210220260];
        }
    }
    return $return;
}