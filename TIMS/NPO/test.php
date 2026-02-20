<html lang="en" class=" "><head>
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
    </head>
  <body class="nav-md" onload="loadusers()">

  <div class="container body">
      <div class="main_container">

      
      
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.php" class="site_title"><img src="../Images/con2.png" alt="Logo Image" height="50" width="50"><span>OPENLINKS</span></a>
            </div>

            <div class="clearfix"></div>
            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
              </div>
              <div class="profile_info">
                <span>Welcome</span>
                <!-- place a name here -->
              </div>
              <div class="clearfix"></div>
            </div>
            <!-- /menu profile quick info -->

            <br>

<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section active">
                <h3>General</h3>
                <ul class="nav side-menu" style="">

                  <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>

                  <li class=""><a><i class="fa fa-edit"></i> Registration <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu" style="display: none;">
                      <li><a href="admin_info.php">Admin Information</a></li>
                      <li><a href="company_data.php">Company Data</a></li>
                      <li><a href="company_info.php">Company Information</a></li>
                      <li><a href="company_dir.php">Company Director</a></li>
                      <li><a href="company_statement.php">Company Statements</a></li>
                      <li><a href="expense_summary.php">Expense Summary</a></li>
                    </ul>
                  </li>

                  <li><a href="myBBBEE.php"><i class="fa fa-link"></i> myBBBEE </a></li>

                  <li><a href="search.php"><i class="fa fa-search"></i> Search </a></li>

                  <li><a href="notifications.php"><i class="fa fa-bell"></i> Notifications </a></li>

                  <li class="current-page"><a href="messages.php"><i class="fa fa-envelope"></i> Messages </a></li>

                  <li><a href="settings.php"><i class="fa fa-cog"></i> Settings </a></li>

                </ul>

            </div>
            </div>

            <!-- <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div> -->
            <!-- /menu footer buttons -->
          </div>
</div>

      <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="http://localhost/Project%20One/Images/Profiles/profile_image.png" alt=""><span class="text-capitalize">kwakho</span>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="javascript:;"> Profile</a></li>
                    <li>
                      <a href="javascript:;">
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li>
                    <li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <li role="presentation" class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">6</span>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image"></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image"></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image"></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <a>
                        <span class="image"><img src="images/img.jpg" alt="Profile Image"></span>
                        <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                        <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="text-center">
                        <a>
                          <strong>See All Alerts</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->
 
        <!-- page content -->
        <div class="right_col" role="main" style="min-height: 581px;">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3> Messages </h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>
            <div class="leach">

              <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
                    <img src="http://localhost/Project%20One/Images/Profiles/profile_image.png" alt="">
          <div class="details">
            <span>Kwakho Liquors</span>
          </div>
        </div>
      </header>
      <div class="search">
        <span class="text">Select an user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fa fa-search"></i></button>
      </div>
      <div class="users-list"><a href="chat.php?url=MjAwMDAwMDAwNg">
                    <div class="content">
                    <img src="http://localhost/Project%20One/Images/Profiles/profile_image.png" alt="">
                    <div class="details">
                        <span>NASCAR</span>
                        <p>You: </p>
                    </div>
                    </div>
                    <div class="status-dot "><small><small class="timey">22nd June<br></small></small>
                    <div class="dotty">2</div></div>
                </a><a href="chat.php?url=MjAwMDAwMDAwNw">
                    <div class="content">
                    <img src="http://localhost/Project%20One/Images/Profiles/profile_image.png" alt="">
                    <div class="details">
                        <span>Sheild</span>
                        <p>You: test</p>
                    </div>
                    </div>
                    <div class="status-dot "><small><small class="timey">22nd June<br></small></small>
                    <div class="dotty">4</div></div>
                </a><a href="chat.php?url=MjAwMDAwMDAwNQ">
                    <div class="content">
                    <img src="http://localhost/Project%20One/Images/Profiles/profile_image.png" alt="">
                    <div class="details">
                        <span>SpaceX</span>
                        <p>You: yes</p>
                    </div>
                    </div>
                    <div class="status-dot "><small><small class="timey">21st June<br></small></small>
                    <div class="dotty">1</div></div>
                </a></div>
    </section>
  </div>




    </div>
            


          </div>
        </div>
        <!-- page content -->

        <!-- footer content -->
                <footer>
          <div class="pull-right">
            2021 Copyright: OpenLinks
          </div>
          <div class="clearfix"></div>
        </footer>        <!-- footer content -->
      </div>
    </div>

  <script src="../Javascript/Gentellela/jquery.js"></script> 
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/nprogress.js"></script>
  <script src="../Javascript/Vendor/Chart.js/dist/Chart.min.js"></script>
  <script src="../Javascript/custom.js"></script>
	
  

</body></html>