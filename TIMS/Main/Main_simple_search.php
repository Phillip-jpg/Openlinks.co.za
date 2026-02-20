
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../classes/search.class.php');
include_once($filepath.'/../lib/Session.php');
    $search_term = $_POST['simple_searchTerm'];
    $temp = new search();
    $temp->search($search_term);
