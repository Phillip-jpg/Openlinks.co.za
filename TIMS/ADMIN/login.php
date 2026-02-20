<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Openlinks Login</title>

    <!-- Bootstrap -->
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="../Images/favicon.ico">

    <style>
      :root {
        --primary: #00b4d8;
        --secondary: #0077b6;
        --accent: #48cae4;
        --light-bg: #ffffff;
        --dark-bg: #032033;
        --border-radius: 14px;
        --transition: all 0.3s ease;
        --shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
      }

      * { box-sizing: border-box; }

      body.login {
        font-family: "Segoe UI", sans-serif;
        background-color: var(--dark-bg);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
      }

      .login_wrapper {
        width: 100%;
        max-width: 420px;
      }

      .login_form, .registration_form {
        background: var(--light-bg);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: var(--transition);
      }

      .login_form:hover, .registration_form:hover {
        transform: translateY(-4px);
      }

      .login_header {
        background: var(--primary);
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .login_content {
        padding: 30px 25px;
      }

      .login_content h1 {
        color: #032033;
        font-weight: 600;
        text-align: center;
        margin-bottom: 25px;
      }

      .form-control {
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 12px 15px;
        font-size: 15px;
        transition: var(--transition);
      }

      .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 180, 216, 0.15);
      }

      .password-container {
        position: relative;
      }

      .toggle-password {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.3s ease;
      }

      .toggle-password:hover {
        color: var(--primary);
      }

      .btn.submit {
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        width: 100%;
        padding: 12px;
        font-weight: 500;
        transition: var(--transition);
        margin-top: 10px;
      }

      .btn.submit:hover {
        background: var(--secondary);
        transform: translateY(-2px);
      }

      .separator {
        text-align: center;
        margin-top: 25px;
        position: relative;
      }

      .separator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e1e5eb;
      }

      .separator p {
        background: var(--light-bg);
        display: inline-block;
        padding: 0 15px;
        position: relative;
        z-index: 2;
        color: #6c757d;
      }

      .change_link {
        text-align: center;
        margin-top: 20px;
      }

      .change_link a {
        color: var(--primary);
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
      }

      .change_link a:hover {
        color: var(--secondary);
        text-decoration: underline;
      }

      .logo-container {
        text-align: center;
        margin-top: 20px;
      }

      .logo-container img {
        max-width: 160px;
      }

      .footer-text {
        text-align: center;
        color: #6c757d;
        font-size: 13px;
        margin-top: 20px;
      }

      .footer-text a {
        color: var(--primary);
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .animate form {
        animation: fadeIn 0.5s ease;
      }

      @media (max-width: 576px) {
        .login_content { padding: 25px 20px; }
      }
    </style>
  </head>

  <body class="login">
    <div>
        <div class="login_header" style="background-color:#032033">
              <h1>OPENLINKS OPS</h1>
                </div>
      <div class="login_wrapper">
        <div class="animate form login_form">
            <br>
          
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
              
              <input type="text" name="tk" value="<?php $filepath = realpath(dirname(__FILE__));include_once($filepath.'/../helpers/token.php');echo token::get_unauth("ADMINLOGINYASC");?>" hidden>
              <div>
                <input type="text" class="form-control" name="Username" placeholder="email@email.com" required />
              </div>
              <br>
              <div class="password-container">
                <input type="password" class="form-control" name="pwd" id="loginPassword" placeholder="Password" required />
                <button type="button" class="toggle-password" id="toggleLoginPassword"><i class="fa fa-eye"></i></button>
              </div>
              <div>
                <input type="submit" name="ADMINLOGIN" class="btn submit" value="Login">
              </div>
              
              <div class="logo-container" style="background-color:background-color: transparent;">
                  <a href="../../index.php"><img style="border-radius:50px" src="../Images/OpenLinks Main Logo 2400x1800.jpg" /></a>
                </div>

              <div class="separator">
                <!--<p class="change_link">New to Openlinks?-->
                <!--  <a href="#signup" class="to_register"> Create Account </a>-->
                <!--</p>-->

                
                <div class="footer-text">
                  <p>©2025 All Rights Reserved OPENLINKS. <a href="#privacy">Privacy and Terms</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const toggle = (btnId, inputId) => {
          const btn = document.getElementById(btnId);
          const input = document.getElementById(inputId);
          if (btn && input) {
            btn.addEventListener('click', () => {
              const type = input.type === 'password' ? 'text' : 'password';
              input.type = type;
              btn.innerHTML = type === 'password'
                ? '<i class="fa fa-eye"></i>'
                : '<i class="fa fa-eye-slash"></i>';
            });
          }
        };
        toggle('toggleLoginPassword', 'loginPassword');
        toggle('toggleRegisterPassword', 'registerPassword');
        toggle('toggleRegisterPasswordRepeat', 'registerPasswordRepeat');
      });
    </script>
  </body>
</html>
