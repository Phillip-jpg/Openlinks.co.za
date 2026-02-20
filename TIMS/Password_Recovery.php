<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
</head>
<body>
<?php

if (isset($_GET['r_url']) && isset($_GET['w']) && (!isset($_GET['s']) && !isset($_GET['v']))) {
    $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/classes/Password_R.php');
    $temp = new pass_r($_GET['w']);
    
    if ($temp->checkFromLogin($_GET['r_url'])) {
        $temp->getFromLogin();
    }else{
        header("location: index.php");
        exit();
    }

} elseif (!isset($_GET['r_url']) && isset($_GET['w']) && (isset($_GET['s']) && isset($_GET['v']))) {
    $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/classes/Password_R.php');
    $temp = new pass_r($_GET['w']);
    
    if ($temp->checkFromEmail($_GET['s'], $_GET['v'])) {
        $temp->getFromEmail();
    }else{
        header("location: index.php");
        exit();
    }

}else {
    
    header("location: index.php");
    exit();
}
?>
    
</body>
</html>

<!-- http://localhost/BBBEE_project/Project%20One/Password_Recovery.php?r_url=12345&w=s
http://localhost/Project%20One/Password_Recovery.php?r_url=12345&w=c
http://localhost/Project%20One/Password_Recovery.php?r_url=12345&w=n -->
