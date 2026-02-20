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
<style>
    .pricing {
        background-color: #67b7d1;
    }
    .pricing .title {
        background-color: #172D44;
        color: white;
        font-size: 20px;
    }
    .x_content {
        background-color: #7c7d80;
    }
    .pricing_features {
        background-color: #EDEDED;
        border: 3px solid lightgrey;
        display: flex; /* Flex container */
        flex-wrap: wrap; /* Allows items to wrap */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
        text-align: center; /* Center text */
        padding: 10px;
    }
    .pricing_features p {
        background-color: #172D44;
        color: white;
        text-align: center;
        width: 100%; /* Ensures the paragraph spans the full width */
        margin-top: 0;
    }
    .list-unstyled {
        font-size: 15px;
        padding: 0;
        width: 100%; /* Full width to align with flex parent */
        list-style: none; /* Removes bullet points */
        display: flex; /* Flex container */
        justify-content: center; /* Center horizontally */
        flex-wrap: wrap; /* Allows list items to wrap */
    }
    .list-unstyled li {
        margin: 5px 10px;
        width: auto; /* Auto width based on content */
    }
    .btn-period {
        background-color: #0dc0ff;
    }
</style>

  <body class="nav-md">
    <div class="container body">
        <div class="main_container">
        
        <?php require 'inc/sidebar.php';?>
        <?php require 'inc/header.php';?>
    
            <div class="right_col" role="main">
                <div class="">
                    <div class="page-title">
                        <div class="title_left">
                            <h3>Market Place Posts </h3>
                        </div>

                        <?php require 'inc/search.php';?>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                            
                                        <div class="x_title">
                                            
                                            <br>
                                        <br>
                                                    <a href="market_posts.php?t=1"><button type="button" class="btn btn-success btn-lg"> My Industry Posts <i class="fa fa-inbox"></i>
                                                    </button></a> 
                                                    
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </li>
                                                
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>

 <p style='color:red; font-size:20px'>The periodic posts below are all posts in the system</p>
                                        <div class="x_content">
                                            <div class="row">
                                                
                                             
   <!-- Modal content-->
    <div class="modal fade" id="myModal" role="dialog">
    
    <div class="modal-dialog">
      
          <div class="modal-content">
        <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body">
                <p id="textmodal" style="text-align:center"></p>
             </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             </div>
          </div>

         </div>
      </div>
    <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">Open Modal</button>
<!-----end of content modal---->
                                                <div class="col-md-12">
                                                    <div class="row" style="background-color:white">
                                                    
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
                                                                  if ($_GET['t'] == 1){
                                                                      
                                                                               $temp->displayMarketPosts();

                                                                                       
                                                                }else{
                                                           
                                                                }
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
             </div>
</div>

          <?php require 'inc/footer.php'; ?>  
             </div>
             
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="../Javascript/links.js"></script>
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script>
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
  <script src="../Javascript/rpa_modal.js"></script>
   <script src="../Javascript/gaff.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
  
    </body>
    
</html>
