<?php 
session_start();
$filepath = realpath(dirname(__FILE__));
include_once('dictionary/dictionary.php');
include_once($filepath.'/../../helpers/token.php');
if (isset($_SESSION["WHO"]) && isset($_SESSION["Name"])
&& isset($_SESSION["CONSULTANT_ID"]) && !isset($_SESSION["SMME_ID"])) {

  if (!($_SESSION["WHO"] == "CONSULTANT" || $_SESSION["WHO"] == "P_COMPANY")) {
    include_once('dictionary/dictionary.php');
    session_destroy();
    header("location: ../CONSULTANT/login.php?e=notconsultant");
    exit();
  }
  if(isset($_SESSION["COMPANY_ID"])
  && isset($_SESSION["PSEUDO_ID"])
  && isset($_SESSION["PSEUDO_TIME"])
  && isset($_SESSION["P_COMPANY_LINK"])){
    if(((round(abs(strtotime($_SESSION["PSEUDO_TIME"]) - strtotime(date("H:i"))) / 60, 2)) > 10)){
      unset($_SESSION['PSEUDO_ID']);
      unset($_SESSION['PSEUDO_TIME']);
      unset($_SESSION['COMPANY_ID']);
      unset($_SESSION['P_COMPANY_LINK']);
      $_SESSION["WHO"] == "CONSULTANT";
      header("location: ../CONSULTANT/login.php?e=timeexpired");
      exit();
    }
    if(!$_SESSION["PSEUDO_ID"] == token::decode('10011010181'))
    $_SESSION["PSEUDO_TIME"] = date("H:i");
  }
} else {
  include_once('dictionary/dictionary.php');
  session_destroy();
  header("location: ../CONSULTANT/login.php?e=notloggedin");
  exit();
}