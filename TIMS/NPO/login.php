<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Openlinks Login</title>

    <!-- Bootstrap -->
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- NProgress -->
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../CSS/Vendor/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../CSS/custom.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>


      <div class="login_wrapper">
        <div class="animate form login_form">
        <div style="background-image: url(../Images/bg-01.jpg); background-size:cover; width: 550px; height: 130px; border-top-left-radius:5px; border-top-right-radius: 5px; margin-left:-50px !important; margin-top:0px;"></div>
          <section class="login_content">
            <form 
            <?php
            if(isset($_GET["r"])){
              if(is_numeric($_GET["r"])){
                $temp = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                echo 'action="../Main/Main.php?'.parse_url($temp, PHP_URL_QUERY).'"';
              }else{
                'action="../Main/Main.php?r="';
              }
            }else{
              echo 'action="../Main/Main.php?r="';
            }
            ?>
             method="POST">
              <h1>Login</h1>
              <input type="text" name="tk" value="
            <?php
            $filepath = realpath(dirname(__FILE__));
            include_once($filepath.'/../helpers/token.php');
            echo token::get_unauth("NPOLOGINYASC");
            ?>" required="" hidden>
              <div>
                <input type="text" class="form-control" name="Username" placeholder="Username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" name="pwd" placeholder="Password" required="" />
              </div>
              <div>
              <div>
                <input type="submit" name="NPOLOGIN" class="btn btn-default submit">
              </div>
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <a href="#signup" class="to_register"> Create Account </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                <h1><i class="fa fa-link"></i> OPENLINKS</h1>
                  <p>©2021 All Rights Reserved OPENLINKS.<a href="#privacy"> Privacy and Terms</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form" style="background-color:white; border-radius: 15px;">
        <div style="background-image: url(../Images/bg-01.jpg); background-size:cover; width: 550px; height: 130px; border-top-left-radius:5px; border-top-right-radius: 5px; margin-left:-50px !important; margin-top:0px;"></div>
          <section class="login_content">
            <form 
            <?php
            if(isset($_GET["r"])){
              if(is_numeric($_GET["r"])){
                $temp = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                echo 'action="../Main/Main.php?'.parse_url($temp, PHP_URL_QUERY).'"';
              }else{
                'action="../Main/Main.php?r="';
              }
            }else{
              echo 'action="../Main/Main.php?r="';
            }
            ?> method="POST">
              <h1>Create Account</h1>
              <input type="text" name="tk" value="
            <?php
            echo token::get_unauth("NPOSIGNUPYASC");
            ?>" required="" hidden>
              <div>
                <input type="text" class="form-control" name="Name" placeholder="Name..." required="">
              </div>
              <div>
                <input type="text" class="form-control" name="Surname" placeholder="Surname..." required="" />
              </div>
              <div>
                <input type="email" class="form-control" name="email" placeholder="Email..." required="" />
              </div>
              <div>
                <input type="password" name="pwd" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <input type="password" name="pwd-repeat" class="form-control" placeholder="Password-Repeat..." required="" />
              </div>
              <div>
                <input type="text" class="form-control" name="Username" placeholder="Creat a username..." required="" />
              </div>
              <div>
                <input type="submit" name="NPOSIGNUP" class="btn btn-default submit">
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                <h1><i class="fa fa-link"></i> OPENLINKS</h1>
                  <p>©2021 All Rights Reserved OPENLINKS.<a href="#privacy"> Privacy and Terms</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
