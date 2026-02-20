<?php
// define("DB_HOST", "localhost");
// define("DB_PORT", 3306);
// define("DB_USER", "yasccoza_openlinks_user");
// define("DB_PASS", "FJwYZbLijwM5UR4");
// define("DB_NAME", "yasccoza_openlink_prelaunch");
define("DB_HOST", "localhost");
define("DB_PORT", 3307);
define("DB_USER", "yasccoza_openlinks_user");
define("DB_PASS", "bO!@42I1#m}1");
define("DB_NAME", "yasccoza_openlink_prelaunch");


$conn =new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if($conn->connect_errno > 0){
    header("location: https://openlinks.co.za/?Error=databaseerror0");
    exit();
}