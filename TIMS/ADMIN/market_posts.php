<?php 
require 'inc/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Openlinks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    <link href="../CSS/formcontrol.css" rel="stylesheet">
  </head>

<style>
    :root {
      --primary-color: #007bff;
      --primary-hover: #0056b3;
      --dark-blue: #172D44; /* From your original CSS */
      --light-blue: #67b7d1; /* From your original CSS */
      --accent-color: #0dc0ff; /* From your .btn-period */
      --page-bg: #f4f7f6;
      --card-bg: #ffffff;
      --text-dark: #333;
      --text-light: #777;
      --border-color: #e9ecef;
      --border-radius: 8px;
      --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
      --card-hover-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    /* --- General Page Layout --- */
    .right_col {
      background-color: var(--page-bg) !important;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .x_panel {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      padding: 1.5rem;
    }
    
    .x_content {
      /* Override the old grey background */
      background-color: var(--card-bg) !important;
    }

    .page-title h3 {
      font-weight: 600;
      color: var(--dark-blue);
    }
    
    /* --- Header Buttons --- */
    .x_title .btn {
      border-radius: 25px; /* Pill shape */
      font-weight: 600;
      padding: 10px 20px;
      font-size: 1rem;
      transition: all 0.3s ease;
      margin-top: 5px;
    }

    .x_title .btn-success {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: #fff;
    }
    .x_title .btn-success:hover {
      background-color: var(--primary-hover);
      border-color: var(--primary-hover);
      color: #fff;
    }

    /* --- Card Container --- */
    .pricing_features {
      display: flex;
      flex-wrap: wrap;
      justify-content: center; /* Center the cards */
      gap: 2rem; /* Modern spacing */
      padding: 1.5rem 0;
      background-color: transparent;
      border: none; /* Remove old border */
    }

    /* Title *inside* the container (if any) */
    .pricing_features > p {
      background-color: var(--dark-blue);
      color: white;
      text-align: center;
      width: 100%;
      margin: 0 0 1rem 0;
      padding: 0.75rem;
      font-size: 1.25rem;
      font-weight: 600;
      border-radius: var(--border-radius);
    }

    /* --- Individual Card --- */
    .pricing {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      text-align: center;
      width: 320px; /* Set a consistent width */
      flex-basis: 320px;
      flex-grow: 0;
      flex-shrink: 1;
      overflow: hidden; /* For border-radius on title */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .pricing:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-hover-shadow);
    }

    /* Card Header */
    .pricing .title {
      background-color: var(--dark-blue);
      color: white;
      padding: 1.5rem;
      font-size: 1.3rem;
      font-weight: 600;
    }

    /* Card Body/Description */
    .pricing p {
      background-color: transparent;
      color: var(--text-light);
      padding: 1.5rem;
      margin-bottom: 0;
      width: auto;
      font-size: 1rem;
      line-height: 1.6;
      border-bottom: 1px solid var(--border-color);
    }

    /* Card Tag List */
    .pricing .list-unstyled {
      font-size: 15px;
      padding: 1.5rem;
      width: 100%;
      list-style: none;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 8px; /* Modern spacing for tags */
      margin: 0;
    }

    .pricing .list-unstyled li {
      margin: 0; /* gap handles spacing */
      width: auto;
      background-color: #e9ecef; /* Light grey tag bg */
      color: #495057;
      padding: 5px 12px;
      border-radius: 15px; /* Pill-shaped tags */
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    /* Card Footer / Button Area */
    .pricing .pricing_footer {
        padding: 1.5rem;
        background-color: #fdfdfd;
        border-top: 1px solid var(--border-color);
    }

    .btn-period {
      background-color: var(--accent-color);
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 25px;
      text-transform: uppercase;
      transition: background-color 0.3s ease;
    }
    .btn-period:hover,
    .btn-period:focus {
      background-color: #0ba9e0; /* Darker accent */
      color: white;
    }
    
    .period-item {
  width: 40px;             /* Set a consistent width */
  height: 10px;            /* Set a consistent height */
  border-radius: 25px;     /* Gives it the rounded "squircle" shape */
  /*background-color: #0dc0ff; */
  
  /* Your light blue color 
  
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
            </div>
            
            <div class="clearfix"></div>
            
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  
                  <div class="x_title">
                    <br>
                    <br>
                   <a href="create_post.php?t=3"><button type="button" class="btn btn-success btn-lg">New Post +
                   </button></a>
                   <a href="send_post.php?t=3"><button type="button" class="btn btn-success btn-lg">Send Jobs <i class="fa fa-paper-plane" aria-hidden="true"></i>
                   </button></a>
                    
                    
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
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
                    $temp->displayMarketPosts($id);
                      
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
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
  
  </body>
  
</html>