<?php
function getUser($id, $e = false){
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/messages.class.php');
    $temp = new messages;
    
    return $temp->dynamicUser($id, $e);
}

function seen($id){
    $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/../classes/messages.class.php');
    $messages = new messages;
    $messages->seen($id);
}