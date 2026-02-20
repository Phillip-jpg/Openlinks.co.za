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

    <!-- Bootstrap -->
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/pnotify.css" rel="stylesheet">
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
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
                <h3>Profile</h3>
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
              <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>
                      <?php 
                        echo "Welcome to your profile";//insert name here              
                      ?>                    
                    </h2>
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
                    <br />
                    <img src="../Images/profile_image.png" alt="" height="200" width="200">
                    <form action="../Main/Main.php" method="POST" enctype="multipart/form-data">
                      <input type="file" name="file">
                      <input type="submit" name="UploadProfilePic">
                    </form>

                    <div style="float: right;">
                        <p id="users_name"></p>
                        <p id="users_surname"></p>
                    </div>

                    <div> 
                        Short description of what the company offers(products/services)
                    </div>

                    <div id="flexbox">
                      <div id="flex1" onclick="changePage_registration()">
                          <p style="text-align: center;">Complete Registration</p>
                          <img src="../Images/edit_profile.png" height="150px" width="150" title="Complete Registration">
                      </div>
                      <div id="flex2" onclick="changePage_settings()">
                          <p style="text-align: center;">Settings</p>
                          <img src="../Images/settings.jpg" height="150px" width="150" title="Settings">
                      </div>
                      <div id="flex3" onclick="changePage_Expense()">
                          <p style="text-align: center;">Expense Summary</p>
                          <img src="../Images/expense_summary.jpg" height="150px" width="150" title="Expense Summary">
                      </div>
                      <div id="flex4" onclick="changePage_account()">
                          <p style="text-align: center;">Account</p>
                          <img src="../Images/account.png" height="150px" width="150" title="Account">
                      </div>
                      <div id="flex5" onclick="changePage_SMME()">
                          <p style="text-align: center;">myBBBEE</p>
                          <img src="../Images/mySMME.png" height="150px" width="150" title="myBBBEE">
                      </div>
                      <div>
                        <a id="flex1" href="messages.php">
                          Read Messages
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             </div>
             </div>
             </div>
             </div>
             <?php 
        require 'inc/footer.php';
      ?>
             </div>
             </div>
  <script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script>
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/custom.js"></script>
    </body>
</html>
<!-- 
<form action="Main/Main.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="file">
                <input type="submit" name="UploadProfilePic">
                </form>
            </div>
            <div style="float: right;">
                <p id="users_name"></p>
                <p id="users_surname"></p>
            </div>
        </div>
        <div> 
            Short description of what the company offers(products/services)
        </div>
         <div id="flexbox">
            <div id="flex1" onclick="changePage_registration()">
                <p style="text-align: center;">Complete Registration</p>
                <img src="../Images/edit_profile.png" height="150px" width="150" title="Complete Registration">
            </div>
            <div id="flex2" onclick="changePage_settings()">
                <p style="text-align: center;">Settings</p>
                <img src="../Images/settings.jpg" height="150px" width="150" title="Settings">
            </div>
            <div id="flex3" onclick="changePage_Expense()">
                <p style="text-align: center;">Expense Summary</p>
                <img src="../Images/expense_summary.jpg" height="150px" width="150" title="Expense Summary">
            </div>
            <div id="flex4" onclick="changePage_account()">
                <p style="text-align: center;">Account</p>
                <img src="../Images/account.png" height="150px" width="150" title="Account">
            </div>
            <div id="flex5" onclick="changePage_SMME()">
                <p style="text-align: center;">myBBBEE</p>
                <img src="../Images/mySMME.png" height="150px" width="150" title="myBBBEE">
            </div>
            <div>
            <a id="flex1" href="messages.php">
            Read Messages
            </a>
            </div>
        </div > -->