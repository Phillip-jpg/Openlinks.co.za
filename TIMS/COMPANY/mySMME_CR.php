<?php 
require 'inc/csrf.php';
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
    <link rel="icon" href="../Images/fav.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/font-awesome.min.css" rel="stylesheet">
    <!-- <link href="../CSS/Vendor/pnotify.css" rel="stylesheet"> -->
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
   
  </head>


  <body class="nav-md change">
  <style>
      .table-responsive>.table>tbody>tr>td,
       .table-responsive>.table>tbody>tr>th,
        .table-responsive>.table>tfoot>tr>td,
         .table-responsive>.table>tfoot>tr>th,
          .table-responsive>.table>thead>tr>td,
           .table-responsive>.table>thead>tr>th {
    white-space: unset !important;
}
    </style>
  <?php $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/../helpers/token.php');?>
    <input type="text" name="tk" id="tk" value="<?php echo token::get("MYSMME_COMPANY_REQUESTED_YASC");?>" required="" hidden>
    <div class="container body">
      <div class="main_container">
        
      <?php require 'inc/sidebar.php';?>
      <div class="top_nav">
          <div class="nav_menu topheader">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $_SESSION["ext"];?>" alt=""><span class="text-capitalize"><?php echo $_SESSION["Name"]; ?></span>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">

                    <li><a href="login.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <li role="presentation" class="dropdown">
                  <a href="messages.php" class="dropdown-toggle info-number" id="unrdmssgs">
                  </a>
                  
                </li>
              </ul>
            </nav>
          </div>
        </div>
   
  <div class="right_col" role="main">
          <div class="">
          <div class="page-title">
              <div class="title_left">
                <h3>CSR HUB</h3>
              </div>

              <?php require 'inc/search.php';?>
              <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                
                  <div class="x_title">
                    <!-- <small>Where all your connections are made</small>
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
                    </ul>  -->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" >
                    <br />
                    <div id="mySMME">

                    </div>
                  </div>
                
              </div>
             </div>
          </div>
        </div>
</div>
<footer class="topheader">
          <div class="pull-left">
            2021 Copyright: OpenLinks
          </div>
          <div class="clearfix"></div>
        </footer>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <!-- <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script> -->
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/mySMMEAjax.js" async></script>
    <script src="../Javascript/custom.js"></script>
    
    </body>
</html>

