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
    
    </head>
  <body class="nav-md">

  <div class="container body">
      <div class="main_container">
      <?php $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/../helpers/token.php');?>
    <input type="text" name="tk" id="tk" value="<?php echo token::get("CHAMS_NOTIFICATIONS_ALL_YASC");?>" required="" hidden>

      
      <?php 
        require 'inc/sidebar.php';
        require 'inc/header.php';
      ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3> Notifications </h3>
              </div>

              <?php require 'inc/search.php';?>
            </div>

            <div class="clearfix"></div>


            <div id="notifications">
            </div>




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
  <script src="../Javascript/custom.js"></script>
  <script src="../Javascript/Ajax_header.js"></script>
  <script src="../JavaScript/chamsAJAX.js" async></script>
  <script>
  $('#myDatepicker').datetimepicker();
  </script>
	
  </body>
</html>
