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
    <link rel="stylesheet" href="../CSS/products_view.css">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link rel="stylesheet" href="../CSS/spin.css">
    <link rel="icon" href="../Images/fav.ico">
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
    <input type="text" name="tk" id="tk" value="<?php echo token::get("ADMIN_VIEW_MORE_CHARTS_YASC");?>" required="" hidden>
    <input type="text" name="tk2" id="tk2" value="<?php echo token::get("LINK_VISITS_YASC");?>" required="" hidden>
        <?php 
            include_once($filepath.'/../helpers/token.php');
                        // $entity = base64_decode($_GET['t']);
                        $id = token::decode($_GET['id']);
                        if(isset($_GET['t'])){
                          $t = token::decode($_GET['t']);
                          
                        }
                        ?><input type="text" name="entity" id="entity" value="<?php echo $id; ?>" required="" hidden><?php
                        $tk = $_POST['tk'];
                        if(($_SESSION['WHO']=="M_ADMIN") && (isset($_POST['VIEW_MORE'])&&isset($_POST['tk']) && token::val($_POST['tk'], 'VIEW_MORE_YASC'))){//t is type of entity and 7 meaning smme
                          include_once('../classes/M_ADMIN.class.php');
                          
                            $temp = new MAdmin();                            
                            $temp->view_moreInfo($id);
                            
                        }
                        
        ?>
      <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="">
                    <div class="x_title">
                      <h2><small>Ownership by Race</small></h2>
                      
                      <div class="clearfix"></div></br>
                      <ul style='list-style-type: none;' >
                        <li style='float:left; margin: 5px' ><div  style='background-color:#0dc0ff ; border:1px solid black; width:15px;height:10px'></div> White Ownership</li>
                        <li style='float:left;margin: 5px'><div  style='background-color:#032033 ; border:1px solid black; width:15px;height:10px'></div>Black Ownership</li>
                      </ul>
                    </div>
                    <div class="x_content">
                    <canvas class="d-flex justify-content-center align-items-center" id="shareholder_chart_1" style="position: relative; height:5vh !important; width:inheret"></canvas>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <div class="">
                    <div class="x_title">
                      <h2> <small>Black Ownership by Gender</small></h2>
                      
                      <div class="clearfix"></div></br>
                       <ul style='list-style-type: none;' >
                        <li style='float:left; margin: 5px' ><div  style='background-color:#032033; border:1px solid black; width:15px;height:10px'></div> Black Male</li>
                        <li style='float:left;margin: 5px'><div  style='background-color:#0dc0ff ; border:1px solid black; width:15px;height:10px'></div>Black Female</li>
                      </ul>
                    </div>
                    <div class="x_content ">
                    
                      <canvas class="d-flex justify-content-center align-items-center" id="shareholder_chart_2" style="position: relative; height:10vh !important; width:inheret"></canvas>
                      
                    </div>
                  </div>
                </div>
            


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

