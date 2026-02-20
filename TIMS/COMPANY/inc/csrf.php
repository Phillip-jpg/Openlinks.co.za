<?php 
session_start();
include_once('dictionary/dictionary.php');
if (isset($_SESSION["WHO"]) && isset($_SESSION["Name"])
&& isset($_SESSION["COMPANY_ID"]) && !isset($_SESSION["SMME_ID"])
&& !isset($_SESSION["CONSULTANT_ID"])) {
  if($_SESSION["WHO"] == "P_COMPANY"){
    $_SESSION["WHO"] = "COMPANY";
  }
 if ($_SESSION["WHO"] !== "COMPANY") {
    include_once('dictionary/dictionary.php');
    session_destroy();
    $temp = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(empty(parse_url($temp, PHP_URL_QUERY))){
      $url = "location: login.php?r=".dictionary();
    }else{
      $url = "location: login.php?r=".dictionary()."&".parse_url($temp, PHP_URL_QUERY);
    }
    header($url);
    exit();
  }
} else {
  include_once('dictionary/dictionary.php');
  session_destroy();
  $temp = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  if(empty(parse_url($temp, PHP_URL_QUERY))){
    $url = "location: login.php?r=".dictionary();
  }else{
    $url = "location: login.php?r=".dictionary()."&".parse_url($temp, PHP_URL_QUERY);
  }
  header($url);
  exit();
}