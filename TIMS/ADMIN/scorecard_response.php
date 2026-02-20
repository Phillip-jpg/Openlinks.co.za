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
    <link href="../Javascript/dropzone/dist/min/dropzone.min.css" rel="stylesheet">
  </head>
  
<style>
    /* ===== Modern Responsive Styles for Response View ===== */

    .accordion {
        margin: 30px auto;
        width: 90%;
        max-width: 900px;
        border-radius: 12px;
    }

    .panel {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .panel:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .panel-heading {
        background: linear-gradient(135deg, #0d47a1, #1976d2);
        color: #fff;
        padding: 16px 20px;
        cursor: pointer;
    }

    .panel-heading h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.3px;
    }

    .panel-body {
        padding: 20px;
        background-color: #f9fafc;
    }

    .msg_list li {
        list-style: none;
        margin-bottom: 15px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        transition: all 0.2s ease;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .msg_list li:hover {
        border-color: #1976d2;
        background: #e3f2fd;
    }

    .msg_list p {
        color: #0d47a1;
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 10px;
    }

    .msg_list ol {
        padding-left: 10px;
    }

    .msg_list li li {
        background: #1976d2;
        color: #fff;
        border-radius: 8px;
        margin-top: 10px;
        padding: 10px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        word-wrap: break-word;
    }

    .msg_list li li input[type="radio"] {
        flex-shrink: 0;
        margin-right: 10px;
        transform: scale(1.2);
    }

    .msg_list li li label {
        flex: 1;
        color: #fff;
        font-size: 0.95rem;
        line-height: 1.4;
        margin: 0;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 12px;
        border: 1px solid #ccc;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 2px rgba(25,118,210,0.2);
        outline: none;
    }

    .btn {
        display: inline-block;
        font-weight: 600;
        border-radius: 10px;
        padding: 12px 30px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        margin-top: 10px;
    }

    .btn-success {
        background-color: #2e7d32;
        color: white;
    }

    .btn-success:hover {
        background-color: #1b5e20;
    }

    .btn-primary {
        background-color: #1565c0;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0d47a1;
    }

    .control-label {
        color: #0d47a1;
        font-weight: 600;
        font-size: 0.95rem;
    }

    hr {
        border-top: 1px solid #ccc;
    }

    .email-label,
    .post-id {
        color: #e53935;
        font-weight: bold;
        font-size: 15px;
        text-decoration: underline;
        word-wrap: break-word;
    }

    /* ===== Responsive Layout Adjustments ===== */

    @media (max-width: 992px) {
        .accordion {
            width: 75%;
        }
        .panel-body {
            padding: 15px;
        }
        .msg_list li li label {
            font-size: 0.7rem;
        }
        
        .hiey{
            font-size: 15px;
        }
    }

    @media (max-width: 768px) {
        .panel-heading {
            padding: 14px 16px;
        }

        .panel-heading h4 {
            font-size: 1rem;
        }
        
        .hiey{
            font-size: 15px;
        }

        .msg_list li {
            padding: 12px;
        }

        .msg_list li li {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .msg_list li li label {
            margin-top: 6px;
            font-size: 0.7rem;
            width: 100%;
            white-space: normal;
            word-wrap: break-word;
        }

        .form-control {
            font-size: 0.9rem;
            padding: 8px 10px;
        }

        .btn {
            width: 100%;
            padding: 10px 0;
            font-size: 0.95rem;
        }

        .control-label {
            display: block;
            width: 100%;
            margin-bottom: 8px;
        }
    }

    @media (max-width: 480px) {
        .accordion {
            width: 100%;
            margin: 15px auto;
        }

        .panel-heading h4 {
            font-size: 0.95rem;
        }
        
        .hiey{
            font-size: 15px;
        }

        .panel-body {
            padding: 10px;
        }

        .msg_list p {
            font-size: 11px;
        }

        .msg_list li li {
            padding: 8px 10px;
            font-size: 0.9rem;
        }

        .msg_list li li label {
            font-size: 0.9rem;
        }

        .btn {
            font-size: 0.9rem;
            padding: 10px;
        }

        .post-id, .email-label {
            font-size: 11px;
        }
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
                            
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
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
                                        <div class="x_title">
                                            <h3>Post Response </h3>
                                            <br>
                                        <br>                                                    
                                                    
                                            <a href="print.php?p=<?php echo htmlspecialchars($_GET['p']); ?>&s=<?php echo htmlspecialchars($_GET['s']); ?>&t=<?php echo htmlspecialchars($_GET['t']); ?>" class="btn btn-success">Print Screen</a>

                                            
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
                                                                $yess=0;
                                                                
                                                                $temp->ResponseForm($_GET['s'], $_GET['p'],$yess);
                                                            }
                                                        
                                         ?>
 
                 
                                    </div>

</div>
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
$(document).ready(function(){
  // Hide Step 1 when Step 2 is shown
  $('#myTab a[href="#profile"]').on('shown.bs.tab', function (e) {
    $('#home').removeClass('show active');
  });

  // Hide Step 2 when Step 1 is shown
  $('#myTab a[href="#home"]').on('shown.bs.tab', function (e) {
    $('#profile').removeClass('show active');
  });
});
</script>
  
    </body>
    
</html>
