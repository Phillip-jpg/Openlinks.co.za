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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="../style.css"> -->
   
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    <link href="../CSS/formcontrol.css" rel="stylesheet">
  </head>


  <body class="nav-md">
    <div class="container body">
        <div class="main_container">
        
        <?php require 'inc/sidebar.php';?>
        <?php require 'inc/header.php';?>
    
            <div class="right_col" role="main">
                <div class="">
                    <div class="page-title">
                        <div class="title_left">
                            <h3>Post Response </h3>
                        </div>

                        <?php require 'inc/search.php';?>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                            
                                        <div class="x_title">
                                            
                                            <br>
                                        <br>                                                    
                                                    
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </li>
                                                <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                       role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
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
                                            <div class="row">

                                                <div class="col-md-12">
                                                    <div class="row">
                                                    
                                                        <?php 
                                                        $temp = "";
                                                        $filepath = realpath(dirname(__FILE__));
                                                        
                                                            if(isset($_GET['t'])){
                                                                switch($_GET['t']){
                                                                    case 1:
                                                                        include_once($filepath.'/../classes/SMME.class.php');
                                                                        $temp = new SMME();
                                                                        break;
                                                                    case 2:
                                                                        include_once($filepath.'/../classes/COMPANY.class.php');
                                                                        $temp = new COMPANY();
                                                                        break;
                                                                    case 3:
                                                                        include_once($filepath.'/../classes/Admin.class.php');
                                                                        $temp = new Admin();
                                                                        break;
                                                                }
                                                                $id = session::get($temp->id);
                                                                $type= $temp->classname;
                                                                $filepath = realpath(dirname(__FILE__, 2));
                                                                include_once($filepath.'/MARKET/USER.php'); 
                                                                $temp= new USER($id, $type);
                                                                $temp->ResponseForm($_GET['s']);
                                                            }
                                                        
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

</div>
</div>
</div>

            
             </div>
             <?php require 'inc/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="../Javascript/links.js"></script>
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script>
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
  <script src="../Javascript/modal.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
  
    </body>
    
</html>
