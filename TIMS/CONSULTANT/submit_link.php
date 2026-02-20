<?php 
require 'inc/csrf.php';
$filepath = realpath(dirname(__FILE__));
include_once($filepath.'/../helpers/token.php');
$variabley = ["tk" => token::get_ne("ESTABLISH_CONNECTION_YASC"), "url" => isset($_GET["url"]) ? $_GET["url"] : ''];
define("VARIABLEY", $variabley);
require '../Main/Main_connection_establish.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  
    <title>Openlinks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="../CSS/Vendor/pnotify.css" rel="stylesheet"> -->
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    
  </head>

  <body>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">

  <?php require 'inc/sidebar.php';?>
  <?php require 'inc/header.php';?>

  <div class="right_col" role="main">
          <div class="">
  <div class="page-title">
              <div class="title_left">
                <h3>My company</h3>
              </div>
              
              <?php require 'inc/search.php';?>
              </div>
              <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2 class="text-capitalize">Connect with your company</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                        <span id="links"><?php echo $texty;?></span>
                    <br />
                  </div>
                </div>
              </div>
             </div>
             </div>
          </div>
          </div>
          <?php 
        require 'inc/footer.php';
      ?>
 </div>
             </div>

  <script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <!-- <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script> -->
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
    </body>
</html>