<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
session_start();
if(isset($_GET['lk'])){
require "classes/consultant.connection.php";
$temp = new consultant_connection;
$temp -> handle_connection($_GET['lk']);
}else{
    header("location: Home.php");
    exit();
}
?>
    
</body>
</html>