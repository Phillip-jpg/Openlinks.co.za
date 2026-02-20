
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/search.class.php');
include_once($filepath.'/../lib/Session.php');
    $search_term = '';
    $legalname = $_POST['legalname'];
    $industry = $_POST['industry'];
    $products = $_POST['products'];
    $foo = $_POST['foo'];
    $office = $_POST['office'];
    $entity = JSON_DECODE($_POST['entity']);
    $temp = new search();
    $temp->search($search_term, $legalname, $industry, $products, $foo, $office, $entity);
    