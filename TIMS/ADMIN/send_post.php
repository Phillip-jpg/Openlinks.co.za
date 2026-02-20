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
      <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
   
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    <link href="../CSS/formcontrol.css" rel="stylesheet">
  
  </head>

<style>
   .select2_multiple {
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: block; 
}


h2 {
    margin-top: 0;
    margin-bottom: 10px; /* Control the space below the header */
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
                                     
                                                    
                                                    <h3 style="text-align:center; color:#26b99a ">Assign Jobs to Companies</h3>
                                                    <hr>
                                                     <p style="text-align:center; color:#337ab7; font-weight:bold" >hold Ctrl key to select mutiple</p>
                                                    
                                                    
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </li>
                                                
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
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
                                        <div class="x_content" style="background-color:white">
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
                                                $temp->displaySMMEsandJobs();
                                            ?> 
                                        </div>
                                    </div>

</div>
</div>
</div>

            
             </div>
             </div>
             <?php require 'inc/footer.php'; ?>
</div>

            
             </div>
          
           <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
<script>
$(document).ready(function() {
    $('.select2_multiple').select2({
        placeholder: "Select options",
        allowClear: true
    });

    $('#post_select').on("change", function() {
        var selectedItems = $(this).select2("data");
        var list = $("#selected_posts");
        list.empty();
        $.each(selectedItems, function(i, item) {
            list.append("<li>" + item.text + "</li>");
        });
    });

    $('#smme_select').on("change", function() {
        var selectedItems = $(this).select2("data");
        var list = $("#selected_smmes");
        list.empty();
        $.each(selectedItems, function(i, item) {
            list.append("<li>" + item.text + "</li>");
        });
    });
});


</script>
  
    </body>
    
</html>
