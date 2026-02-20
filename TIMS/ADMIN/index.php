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


    <title> Openlinks | Dashboard </title>
    <link rel="icon" href="../Images/fav.ico">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link href="../CSS/spin.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
  </head>
  <body class="nav-md">
  

  <?php 
  $filepath = realpath(dirname(__FILE__));
 
    include_once($filepath.'/../helpers/token.php');?>
    <input type="text" name="tk" id="tk" value="<?php echo token::get("ADMIN_ANALYTICS_YASC");?>" required="" hidden>
    <input type="text" name="tk2" id="tk2" value="<?php echo token::get("ADMIN_five_Day_wait_YASC");?>" required="" hidden>
  <div class="container body">
      <div class="main_container">

      
      <?php 
        require 'inc/sidebar.php';
        require 'inc/header.php';
      ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3> Dashboard </h3>
              </div>

              <?php require 'inc/search.php';?>
            </div>

            <div class="clearfix"></div>
            <p class="h2 text-center text-capitalize">User Data</p>
            <div class="row tile_count text-center" id="summary_analytics_header">
              
            </div>

          

            <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Number of Connections<br><small>My Connections Vs Average Connections</small></h2>
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

                  <div  class="demo-placeholder">
                      <canvas id="progress_process_chart" style="position: relative; height:inheret; width:inheret; margin-top: 10px; padding:10px;"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>BBBEE PROGRESS SUMMARY</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    
                    <div class="clearfix"></div>
          
                  </div>
                  <div class="x_content ">
                  <div class="col-md-12 col-sm-12 " id="process_summary">
                        
                        </div>
                  </div>
                </div>
              </div>
            <!-- </div> -->

 <div class="clearfix"></div>

            <hr>
            <p class="h2 text-center text-capitalize">Market Place analytics</p>
            <div class="row tile_count text-center" id="company_marketplace_header">
                          
              </div>
  
            
  
              <div class="row" >
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>Search </h2>
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
                     
                      <canvas id="search_chart"></canvas>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-6 col-sm-6 col-xs-12" >
                  <div class="x_panel">
                    <div class="x_title">
                      <h2> Keyword Performance</h2>
                      <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                      </ul>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content ">
                  
                      <div class="col-md-12 col-sm-12 " id="search_summary">
                          
                      </div>
                      
                    </div>
                  </div>
                </div>

              </div>
            </div>

</hr>
<!-- <div class="clearfix"></div>
            <p class="h2 text-center text-capitalize">BBBEE analytics</p>
            <div class="row tile_count text-center" id="summary_analytics_header">
              
            </div>

          

            <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Number of Connections<br><small>My Connections Vs Average Connections</small></h2>
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
                    <canvas id="connections_chart"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Profile</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    
                    <div class="clearfix"></div>
                    <i class='fa fa-info-circle'></i> Filling in these forms improves your chances of making more connections and being found by others easier. 
                  </div>
                  <div class="x_content ">
                    <div id="company_profile_stats" >

                    </div>
                  </div>
                </div>
              </div> -->
            <!-- </div> -->

 <div class="clearfix"></div>

<hr>
        <!-- page content -->

        <!-- footer content -->
        <?php 
        require 'inc/footer.php';
      ?>
        <!-- footer content -->
      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <script src="../Javascript/Gentellela/jquery.js"></script> 
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/nprogress.js"></script>
  <script src="../Javascript/Vendor/Chart.js/dist/Chart.min.js"></script>
  <script src="../Javascript/Ajax_header.js"></script>
 <script src="../Javascript/analytics_admin.js"></script>
  <!-- <script src="../Javascript/Admin_Notify.js" defer></script> -->
  <script src="../Javascript/custom.js"></script>
  
  </body>
</html>
