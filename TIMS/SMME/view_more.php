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

    <title> View More </title>



    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/products_view.css">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css"> 
       
    <link rel="stylesheet" href="../CSS/spin.css">

    </head>
  <body class="nav-md">

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
                <h3> View More</h3>
              </div>
              
              <?php require 'inc/search.php';?>
            </div>

            <div class="clearfix"></div>
            <div class="leach">

            <div class="wrapper2">
    <section class="chat-area">
    <?php $filepath = realpath(dirname(__FILE__));
    include_once($filepath.'/../helpers/token.php');?>
    <input type="text" name="tk" id="tk" value="<?php echo token::get("VIEW_MORE_CHARTS_YASC");?>" required="" hidden>
    <input type="text" name="tk2" id="tk2" value="<?php echo token::get("SMME_LINK_VISITS_YASC");?>" required="" hidden>
        <?php 
        
            include_once($filepath.'/../helpers/token.php');
                        // $entity = base64_decode($_GET['t']);
                        $id = token::decode($_GET['id']);
                        if(isset($_GET['t'])){
                          $t = token::decode($_GET['t']);
                        }
                        
                        ?><input type="text" name="entity" id="entity" value="<?php echo $id; ?>" required="" hidden><?php
                        $tk = $_POST['tk'];
                       if(($_SESSION['WHO']=="SMME") && (!isset($_GET['t'])) && (isset($_POST['VIEW_MORE'])&&isset($_POST['tk']) && token::val($_POST['tk'], 'VIEW_MORE_YASC'))){
                          include_once "../classes/SMME.class.php";
                            $temp = new SMME();
                            $temp->view_moreInfo($id);
                        }else if((($_SESSION['WHO']=="SMME"))&& ($t == "SMME" || $t == "NPO") && (isset($_POST['VIEW_MORE'])&&isset($_POST['tk']) && token::val($_POST['tk'], 'VIEW_MORE_YASC'))){
                          include_once "../classes/SMME.class.php";
                            $temp = new SMME();
                            $temp->SMME_TO_SMME_view_moreInfo($id);
                        }else if((($_SESSION['WHO']=="SMME"))&& ($t == "COMPANY" ) && (isset($_POST['VIEW_MORE'])&&isset($_POST['tk']) && token::val($_POST['tk'], 'VIEW_MORE_YASC'))){
                          include_once "../classes/SMME.class.php";
                            $temp = new SMME();
                            
                            $temp->view_moreInfo($id);
                        }
                        else{//t is type of entity and 80 meaning npo
                          echo "Something went wrong, no user found.".$tk."<br><br>";
                        }
        ?>
      <div class="chart-container" style="position: relative; height:60vh; width:inheret; margin-top: 10px; padding:10px;">
        <canvas class="d-flex justify-content-center align-items-center" id="shareholder_chart" style="position: relative; height:inheret; width:inheret"></canvas>
      </div>
    </section>
  </div>




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
  <script src="../Javascript/Ajax_header.js"></script>
  <script src="../Javascript/view_more.js" defer></script>
  <script src="../Javascript/LinkVisits.js" defer></script>
  <script src="../Javascript/custom.js"></script>
	
  </body>
</html>

