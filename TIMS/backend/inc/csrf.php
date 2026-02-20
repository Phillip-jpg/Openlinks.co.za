<?php 
session_start();
include_once('dictionary/dictionary.php');
if (isset($_SESSION["WHO"]) && isset($_SESSION["Name"]) && isset($_SESSION["ADMIN_ID"]) && !isset($_SESSION["COMPANY_ID"])) {
  if (!($_SESSION["WHO"] == "ADMIN" || $_SESSION["WHO"] == "M_ADMIN" ||$_SESSION["WHO"] == "G_ADMIN" )) {
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