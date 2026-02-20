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

    <title> Dashboard </title>



    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link rel="stylesheet" href="../CSS/spin.css">
    <style>
      .analytics_statics{
        margin-bottom: 10px !important;
        padding-bottom: 10px !important;
        height: fit-content !important;
      }
    </style>
    </head>
  <body class="nav-md">

  <div class="container body">
      <div class="main_container">

      
      <?php 
        require 'inc/sidebar.php';
        require 'inc/header.php';
      ?>

        <!-- page content -->
        <?php $filepath = realpath(dirname(__FILE__));include_once($filepath.'/../helpers/token.php');?>
    <input type="text" name="tk" id="tk" value="<?php echo token::get("ADMIN_ANALYTICS_YASC");?>" required="" hidden>

        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row col-lg-12 col-md-12 col-sm-12" style="display: inline-block;" >
          <div class="tile_count" id="summary_analytics_header">
            

          </div>
        </div>
          <!-- /top tiles -->

          <div class="row" style="margin-bottom: 50px !important;
        padding-bottom: 50px !important;
        height: fit-content !important;">
              <div class="col-md-12 col-sm-12 ">
                <div class="dashboard_graph">
  
                  <div class="row x_title">
                    <div class="col-md-6">
                      <h3>Progress Process</h3>
                    </div>
                    
                  </div>
  
                  <div class="col-md-9 col-sm-9 col-lg-9 ">
                    <div  class="demo-placeholder">
                      <canvas id="progress_process_chart" style="position: relative; height:inheret; width:inheret; margin-top: 10px; padding:10px;"></canvas>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-3 col-lg-3  bg-white">
                    <div class="x_title">
                      <h2>Process Summary</h2>
                      <div class="clearfix"></div>
                    </div>
  
                      <div class="col-md-12 col-sm-12 " id="process_summary">
                        
                      </div>
  
                    
                  </div>
                </div>
  
                  <div class="clearfix"></div>
              </div>
            </div>

          <div class="row" style="margin-bottom: 50px !important;
        padding-bottom: 50px !important;
        height: fit-content !important;">
            <div class="col-md-12 col-sm-12 ">
              <div class="dashboard_graph">

                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Search Results</h3>
                  </div>
                  
                </div>

                <div class="col-md-9 col-sm-9 col-lg-9 ">
                  <div  class="demo-placeholder">
                    <!-- SEARCH CHART --><canvas id="search_chart"></canvas>
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3  bg-white">
                  <div class="x_title">
                    <h2>Search Summary</h2>
                    <div class="clearfix"></div>
                  </div>
                    <div class="col-md-12 col-sm-12 " id="search_summary">
                      <!-- SEARCH SUMMARY -->
                    </div>
                </div>

                </div>

                <div class="clearfix"></div>
              </div>
            </div>

            

              <!-- <div class="row">
                <div class="col-md-12 col-sm-12 ">
                  <div class="dashboard_graph">
    
                    <div class="row x_title">
                      <div class="col-md-6">
                        <h3>Page Visits</h3>
                      </div>
                      
                    </div>
    
                    <div class="col-md-9 col-sm-9 col-lg-9 ">
                      <div id="page_visits_chart" class="demo-placeholder"></div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-lg-3  bg-white">
                      <div class="x_title">
                        <h2>Page Visits Summary</h2>
                        <div class="clearfix"></div>
                      </div>
    
                      <div class="col-md-12 col-sm-12 " id="visits_summary">
                          
                      </div>
    
                    </div>
    
                    </div>
    
                    <div class="clearfix"></div>
                  </div>
                </div> -->

                <!-- <div class="row">
                  <div class="col-md-12 col-sm-12 ">
                    <div class="dashboard_graph">
      
                      <div class="row x_title">
                        <div class="col-md-6">
                          <h3>Search Results</h3>
                        </div>
                        
                      </div>
      
                      <div class="col-md-9 col-sm-9 col-lg-9 ">
                        <div id="search_chart" class="demo-placeholder"></div>
                      </div>
                      <div class="col-md-3 col-sm-3 col-lg-3  bg-white">
                        <div class="x_title">
                          <h2>Search Summary</h2>
                          <div class="clearfix"></div>
                        </div>
      
                          <div class="col-md-12 col-sm-12 ">
                            <div>
                              
                            </div>
                          </div>
      
                        
                      </div>
      
                      </div>
      
                      <div class="clearfix"></div>
                    </div>
                  </div> -->

                  <!-- <div class="row">
                    <div class="col-md-12 col-sm-12 ">
                      <div class="dashboard_graph">
        
                        <div class="row x_title">
                          <div class="col-md-6">
                            <h3>Search Results</h3>
                          </div>
                          
                        </div>
        
                        <div class="col-md-9 col-sm-9 col-lg-9 ">
                          <div id="search_chart" class="demo-placeholder"></div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-lg-3  bg-white">
                          <div class="x_title">
                            <h2>Search Summary</h2>
                            <div class="clearfix"></div>
                          </div>
        
                            <div class="col-md-12 col-sm-12 ">
                              <div>
                               
                              </div>
                            </div>
        
                          
                        </div>
        
                        </div>
        
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div> -->

 
        <!-- footer content -->
        <?php 
        require 'inc/footer.php';
      ?>
        <!-- footer content -->
      </div>
    </div>

  <script src="../Javascript/Gentellela/jquery.js"></script> 
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/nprogress.js"></script>
  <script src="../Javascript/Vendor/Chart.js/dist/Chart.min.js"></script>
  <script src="../Javascript/Ajax_header.js"></script>
  
  <script src="../JavaScript/analytics_admin.js"></script>
  <script src="../Javascript/custom.js"></script>
	
  </body>
</html>

