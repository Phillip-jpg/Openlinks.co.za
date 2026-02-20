<?php

function dictionary($id = NULL){
    $return = '';
    $dictionary = array(
        214227200 => "admin_info.php",
        214227210 => "company_data.php",
        214227220 => "company_dir.php",
        214227230 => "company_info.php",
        214227240 => "company_statement.php",
        214227250 => "expense_summary.php",
        214227260 => "index.php",
        214227270 => "messages.php",
        214227290 => "notifications.php",
        214227300 => "search.php",
        214227310 => "settings.php",
        214227320 => "NPO_userProfile.php",
        214227330 => "myBBBEE_ALL.php",
        214227340 => "myBBBEE_Charts.php",
        214227350 => "myBBBEE_CR.php",
        214227360 => "myBBBEE_NR.php"
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
        $return = 214227260;
    }


    }else{
        $return = $dictionary[$id];
        if($return == ''){
        $return = $dictionary[214227260];
        }
    }
    return $return;
}