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

    <link rel="icon" href="../Images/fav.ico">

    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    </head>
  <body class="nav-md"  onload="loadusers()">

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

              <?php 
              include_once "../Main/Main_ChatUser.php";
              ?>
                <div class="wrapper">
                  <section class="users">
                    <header>
                      <div class="content">
                        <?php 
                      if(isset($_SESSION['WHO'])){

                        if($_SESSION['WHO'] == "SMME"){

                            $id=$_SESSION['SMME_ID'];

                        }elseif($_SESSION['WHO'] == "NPO"){

                            $id=$_SESSION['NPO_ID'];

                        }elseif($_SESSION['WHO'] == "COMPANY"){

                            $id=$_SESSION['COMPANY_ID'];

                        }elseif($_SESSION['WHO'] == "CONSULTANT"){

                            $id=$_SESSION['CONSULTANT_ID'];

                        }elseif($_SESSION['WHO'] == "P_COMPANY"){

                            $id=$_SESSION['P_COMPANY_ID'];

                        }elseif($_SESSION['WHO'] == "M_ADMIN"){

                            $id=$_SESSION['ADMIN_ID'];

                        }elseif($_SESSION['WHO'] == "G_ADMIN"){

                            $id=$_SESSION['ADMIN_ID'];

                        }else{

                            echo "technical error";
                            exit();

                        }

                    }else{
                        echo "technical error";
                        exit();
                    }
                            $row = getUser($id);
                        ?>
              <img src="<?php echo $row['ext']; ?>" alt="">
              <div class="details">
                <span><?php echo $row['Legal_name']?></span>
              </div>
            </div>
          </header>
      <div class="search">
        <span class="text">Select an user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fa fa-search"></i></button>
      </div>
      <div class="users-list">
  
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="../Javascript/Gentellela/jquery.js"></script> 
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/nprogress.js"></script>
  <script src="../Javascript/Vendor/Chart.js/dist/Chart.min.js"></script>
  <script src="../Javascript/Ajax_header.js"></script>
  <script src="../Javascript/custom.js"></script>
  <script src="../Javascript/chat/users.js"></script>
	
  </body>
</html>
