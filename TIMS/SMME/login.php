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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- NProgress -->
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../CSS/Vendor/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../CSS/custom.css" rel="stylesheet">
        <link rel="icon" href="../Images/favicon.ico">
  
  </head>

  <body class="login">
  
 <!-- Modal content-->
<div class="modal fade" id="myModal" role="dialog">
    
    <div class="modal-dialog">
      
          <div class="modal-content">
        <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body">
                <p id="textmodal" style="text-align:center; font-weight:bold;"></p>
             </div>
            <div class="modal-footer" style="margin:auto">
            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 15px;">Close</button>
             </div>
          </div>

         </div>
      </div>
    <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="text-align:center; display:none" ></button>
<!-----end of content---->
  
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>


      <div class="login_wrapper">
        <div class="animate form login_form">
        <div class="image"></div>
        <br>
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
             <h1 style="font-family:segoe ui">Login</h1>
             <input type="text" name="tk" value="
            <?php
            $filepath = realpath(dirname(__FILE__));
            include_once($filepath.'/../helpers/token.php');
            echo token::get_unauth("SMMELOGINYASC");
            ?>" required="" disabled hidden>
              <div>
                <input style="font-family:segoe ui; font-size:15px;" type="text" class="form-control inputs" name="Username" placeholder="Username" required="" />
              </div>
              <div>
              <i class="fa fa-eye gone" id="togglePassword"></i>
              <input style="font-family:segoe ui;font-size:15px;" type="password" class="form-control inputs" name="pwd" placeholder="Password" required="" id="id_password" />
              </div>
              <div>
              <div style="margin-left:138px !important" style="font-family:segoe ui">
                <input type="submit" name="SMMELOGIN"  value="login" class="btn btn-round btn-default login" style="width:90px !important; font-family:segoe ui">
                
              </div>
              <br>
              
              <div style="margin-right:140px !important" class="pok">
              <a class="reset_pass" href="forgot_password.php">Lost your password?</a>
              </div>

              </div>

              

              <div class="clearfix"></div>

              <div class="separator">
               
               
                  <!--//<p style="font-family:segoe ui; font-size:13px">Dont have an account ? <a href="#signup" class="to_register" style="font-family:segoe ui; font-size:13px">Create Account </a> </p>-->
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
            <a href="../../index.php"><img src="../Images/OpenLinks Main Logo 2400x1800.jpg" style="height:140px;width:200px;" /></a>
               

                  <p style="font-family:segoe ui">©2021 All Rights Reserved OPENLINKS.<a href="#privacy" style="font-family:segoe ui"> Privacy and Terms</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form" style="background-color:white; border-radius: 15px;">
        <div class="image"></div>
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
              <h1 style="font-family:segoe ui;">Create Account</h1>
              <input type="text" name="tk" value="
            <?php
            echo token::get_unauth("SMMESIGNUPYASC");
            ?>" required="" hidden>
              <div>
                <input type="text" class="form-control inputs" name="Name" placeholder="Name..." required="">
              </div>
              <div>
                <input type="text" class="form-control inputs" name="Surname" placeholder="Surname..." required="" />
              </div>
              <div>
                <input type="email" class="form-control inputs" name="email" placeholder="Email..." required="" />
              </div>
              <div>
                <input type="password" name="pwd" class="form-control inputs" placeholder="Password" required="" id="id_password" />
              </div>
              <div>
                <input type="password" name="pwd-repeat" class="form-control inputs" placeholder="Password-Repeat..." required="" />
              </div>
              <div>
                <input type="text" class="form-control inputs" name="Username" placeholder="Create a username..." required="" />
              </div>
              <div>
                <input style="margin-left:170px; width:100px" value="Signup" type="submit" name="SMMESIGNUP" class="btn btn-round btn-default submit signup">
              </div>

  

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link" style="font-family:segoe ui;">Already a member ?
                  <a href="#signin" class="to_register" style="font-family:segoe ui; font-size:13px"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <a href="../../index.php"><img src="../Images/OpenLinks Main Logo 2400x1800.jpg" style="height:140px;width:200px;" /></a>
                  <p style="font-family:segoe ui;">©2021 All Rights Reserved OPENLINKS.<a href="#privacy" style="font-family:segoe ui;"> Privacy and Terms</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    
    <script src="../Javascript/loginmodal.js"></script>
    <script src="../Javascript/login.js"></script>
    
  </body>
</html>
