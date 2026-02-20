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
                            <h3>Job Order Verification </h3>
                        </div>

                        <?php require 'inc/search.php';?>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                               
                                    
                                    <div class="x_content">
                                       
                                       
    <div class='align-self-center tab-content' style='display: flex; margin:auto ;'>
        <div id="Direct_expense_table" class='tab-pane fade in active' style="width: 50vw !important; margin:auto">
            <form id="behave" action="../Main/Main.php" method="POST">

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
    <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none"></button>
<!-----end of content---->

             
        </div>
        
</div>
</div>


</div>
</div>
</div>
<a href="print_job.php?d=<?php echo htmlspecialchars($_GET['d']); ?>&q=<?php echo htmlspecialchars($_GET['q']); ?>" class="btn btn-success">Print Screen</a>
            <div class="x_panel" >
            <h3 class="text-center">Job Order information</h3>
     
              
                
            <?php 
                  $temp = "";
                  $filepath = realpath(dirname(__FILE__));
                  
                        include_once($filepath.'/../classes/ADMIN.class.php');
                        $temp = new Admin();
                        $id = session::get($temp->id);
                        $type= $temp->classname;
                        $filepath = realpath(dirname(__FILE__, 2));
                        include_once($filepath.'/MARKET/USER.php'); 
                        $temp= new USER($id, $type);
                        $noz=0;
                        $temp->displayJobOrder($_GET['d'], $_GET['q'],$noz);
                      
                  ?>


          
              </div>
            
            
            </div>
            
             </div>
             <?php require 'inc/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="../Javascript/questions.js"></script>
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
