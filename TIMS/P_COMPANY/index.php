<?php 
require 'inc/csrf.php';
// print_r(password_hash("admin123", PASSWORD_DEFAULT));
// exit();
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
  <?php $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/../helpers/token.php');?>
  <input type="text" name="tk" id="tk" value="<?php echo token::get("COMPANY_ANALYTICS_YASC");?>" required="" hidden>
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
            <p class="h2 text-center text-capitalize">BBBEE analytics</p>
            <div class="row tile_count text-center" id="company_analytics_header">
              
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
              </div>
            <!-- </div> -->

 <div class="clearfix"></div>

<hr>
<p class="h2 text-center text-capitalize">Market Place analytics</p>
 <div class="row tile_count text-center" id="company_marketplace_header">
              
              </div>
  
            
  
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>Search Performance</h2>
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
                      <!-- <ul style='list-style-type: none;' >
                        <li style='float:left; margin: 5px' ><div  style='background-color:#113382 ; border:1px solid black; width:15px;height:10px'></div>Name</li>
                        <li style='float:left;margin: 5px'><div  style='background-color:#6932a8 ; border:1px solid black; width:15px;height:10px'></div>Keywords</li>
                        <li style='float:left;margin: 5px'><div  style='background-color:#a83232; border:1px solid black; width:15px;height:10px'></div>Industry</li>
                        <li style='float:left;margin: 5px'><div  style='background-color:#36a832; border:1px solid black; width:15px;height:10px'></div>Form Of Ownership</li>
                      </ul> -->
                      <canvas id="company_search_chart">
                        
                      </canvas>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-6 col-sm-6 col-xs-12">
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
                    <!-- <ul style='list-style-type: none;' >
                        <li class="col-3" style='float:left; margin: 5px' ><div  style='background-color:#113382 ; border:1px solid black; width:15px;height:10px'></div>Keyword 1</li>
                        <li class="col-3" style='float:left;margin: 5px'><div  style='background-color:#6932a8 ; border:1px solid black; width:15px;height:10px'></div>Keyword 2</li>
                        <li class="col-3" style='float:left;margin: 5px'><div  style='background-color:#a83232; border:1px solid black; width:15px;height:10px'></div>Keyword 3</li>
                        <li class="col-3" style='float:left;margin: 5px'><div  style='background-color:#36a832; border:1px solid black; width:15px;height:10px'></div>Keyword 4</li>
                      </ul> -->
                      <canvas id="keyword_chart" ></canvas>
                      
                    </div>
                  </div>
                </div>
            <!-- <div class="row tile_count text-center">
            <h2>Keywords Performance</h2>
              <div class="col-md-3 col-sm-6 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-link"></i> Keyword 1 </span>
                <div class="count text-center">17</div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-envelope"></i> Keyword 2</span>
                <div class="count text-center"> 67% </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-envelope-open"></i> Keyword 3 </span>
                <div class="count text-center"> 10 </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-trophy"></i> Keyword 4</span>
                <div class="count text-center"> 3 </div>
              </div>
          </div> -->
           
            <!-- <div class="row">
            <div class="col-md-6">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Notifications <small>Recent</small></h2>
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
                </div> -->


                
                <!-- <div class="x_content">
                  <ul class="list-unstyled msg_list">
                    <li>
                      <a>
                        <span class="image">
                          <img src="images/img.jpg" alt="img" />
                        </span>
                        <span>
                          <span>Phillay Incorparated</span>
                          <span class="time">Yesterday</span>
                        </span>
                        <span class="message">
                        Phillay incorparated has set a meeting for 14 July 2021, the details have been sent to your email
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image">
                          <img src="images/img.jpg" alt="img" />
                        </span>
                        <span>
                          <span>Nasa</span>
                          <span class="time">Yesterday</span>
                        </span>
                        <span class="message">
                            Classified
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image">
                          <img src="images/img.jpg" alt="img" />
                        </span>
                        <span>
                          <span>Nascar</span>
                          <span class="time">6 June 2021</span>
                        </span>
                        <span class="message">
                            Nascar has requested a link with you
                        </span>
                      </a>
                    </li>
                  </ul>
                </div> -->

              <!-- </div>
            </div>
            </div> -->
            


          </div>
        </div>
        <!-- page content -->

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
  <script src="../Javascript/company_analytics.js"></script>
  <script src="../Javascript/custom.js"></script>
	
  </body>
</html>
