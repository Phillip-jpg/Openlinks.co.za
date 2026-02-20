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
/* Container grid layout */
.post-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

/* Card base */
.job-card {
  background: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  border: 1px solid #e0e0e0;
  transition: all 0.3s ease;
  padding: 1.5rem;
}

.job-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.12);
}

/* Header section */
.job-header {
  border-bottom: 2px solid #007bff;
  padding-bottom: 0.8rem;
  margin-bottom: 1rem;
}

.job-header p {
  margin: 0;
  font-size: 0.95rem;
  color: #333;
}

.job-header h3 {
  font-weight: 600;
  color: #007bff;
  font-size: 1.2rem;
  margin-top: 0.5rem;
}

/* Status label */
.status {
  font-weight: 600;
  padding: 0.3rem 0.7rem;
  border-radius: 0.5rem;
  display: inline-block;
  font-size: 0.85rem;
}

.status-done {
  background-color: #e6f7e8;
  color: #28a745;
}

.status-pending {
  background-color: #e3f2fd;
  color: #0dc0ff;
}

/* Meta info */
.job-meta {
  font-size: 0.85rem;
  color: #666;
  margin-bottom: 1rem;
}

.job-meta span {
  font-weight: 500;
  color: #333;
}

/* Table */
.job-details {
  width: 100%;
  font-size: 0.9rem;
  border-collapse: collapse;
}

.job-details td {
  padding: 0.25rem 0.5rem;
  vertical-align: top;
  color: #444;
}

/* Action button */
.view-btn {
  display: inline-block;
  background-color: #007bff;
  color: #fff;
  font-weight: 600;
  border-radius: 0.5rem;
  padding: 0.4rem 1rem;
  text-decoration: none;
  transition: background-color 0.2s ease;
}

.view-btn:hover {
  background-color: #0056b3;
}

/* Timeline style */
.timeline {
  list-style: none;
  padding-left: 0;
}

.timeline li {
  position: relative;
  padding-left: 1.2rem;
  margin-bottom: 1rem;
}

.timeline li::before {
  content: "•";
  color: #007bff;
  position: absolute;
  left: 0;
  font-size: 1.5rem;
  top: -2px;
}

/* Responsive fix */
@media (max-width: 768px) {
  .job-card {
    padding: 1rem;
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
                                     <a href="create_post.php?t=3"><button type="button" class="btn btn-success btn-lg">New Post +
                                                    </button></a>
                                            
                                                    
                                                    
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </li>
                                                
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="x_content">
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
                                                $temp->displayPeriodPosts($_GET['p'],$id);
                                            ?> 
                                            
                                        </div><a  class="btn btn-primary" href="market_posts.php?t=3">Back</a>
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
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
  
    </body>
    
</html>
