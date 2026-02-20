
<?php

$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../helpers/token.php');
include_once($filepath.'/../classes/consultant.connection.php');
include_once($filepath.'/../classes/company.connection.php');
include_once($filepath.'/../helpers/token.php');
if (isset(VARIABLEY['tk']) && token::val(VARIABLEY['tk'], 'ESTABLISH_CONNECTION_YASC')){
    $temp= new consultant_connection();
    $texty = $temp->handle_connection(VARIABLEY["url"]);
}else{
    echo "flop";
}



