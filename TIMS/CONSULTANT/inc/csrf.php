<?php 
session_start();
include_once('dictionary/dictionary.php');
if (isset($_SESSION["WHO"]) && isset($_SESSION["Name"])
&& isset($_SESSION["CONSULTANT_ID"])
&& !isset($_SESSION["SMME_ID"])) {

  unset($_SESSION['PSEUDO_ID']);
  unset($_SESSION['PSEUDO_TIME']);
  unset($_SESSION['COMPANY_ID']);
  unset($_SESSION['P_COMPANY_LINK']);
  unset($_SESSION['P_COMPANY_ID']);

  $_SESSION["WHO"] == "CONSULTANT";
  if($_SESSION["WHO"] == "P_COMPANY"){
    $_SESSION["WHO"] = "CONSULTANT";
    }
    elseif ($_SESSION["WHO"] !== "CONSULTANT") {
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