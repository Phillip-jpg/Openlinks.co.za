<?php 
require 'inc/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>

    <!-- Meta, title, CSS, favicons, etc. -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="../style.css"> -->
   
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    <link href="../CSS/formcontrol.css" rel="stylesheet">
    <link href="../Javascript/dropzone/dist/min/dropzone.min.css" rel="stylesheet">
   <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 0;  /* this affects the margin in the printer settings */
        }
    </style>
  </head>


  <body class="nav-md">
 

           
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                        <div class="x_title" style="page-break-inside: avoid">
                                           
                                            <img src="../../Images/OpenLinks Black Logo 2400x1800.jpg" width="200" style="width:250px; height:180px">
                                            
                                              <button id="printButton" class="btn btn-success">Print</button>

                                            <h3>Job Response </h3>
                                            <br>
                                        <br>                                                    
                                            <div class="clearfix"></div>
                                        </div>
                                      
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
                                                                        include_once($filepath.'/../classes/ADMIN.class.php');
                                                                        $temp = new Admin();
                                                                        break;
                                                                }
                                                                $id = session::get($temp->id);
                                                                $type= $temp->classname;
                                                                $filepath = realpath(dirname(__FILE__, 2));
                                                                include_once($filepath.'/MARKET/USER.php'); 
                                                                $temp= new USER($id, $type);
                                                                $yess=1;
                                                                $temp->ResponseForm($_GET['s'], $_GET['p'],$yess);
                                                                    
                                                                
                                                            }
                                                        
                                         ?>
 
                 
                                    </div>
                                    
                                 
 </div>
  </div>
  
  

            
             
             <?php require 'inc/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
 
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script>
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
  <script src="../Javascript/dropzone/dist/min/dropzone.min.js"></script>
  <script src="../Javascript/modal.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
<script>
    document.getElementById('printButton').addEventListener('click', function() {
        // Hide the button
        document.getElementById('printButton').style.display = 'none';

        // Triggering the print action
        window.print();
    });
</script>
 
  
    </body>
    
</html>




