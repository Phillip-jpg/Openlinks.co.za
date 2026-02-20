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
    <link href="../CSS/spin.css" rel="stylesheet">
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
                <h3> Messages </h3>
              </div>

              <?php require 'inc/search.php';?>
            </div>

            <div class="clearfix"></div>
            <div class="leach">

            <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
        include_once "../Main/Main_ChatUser.php";
        include_once "../helpers/token.php";
        if(isset($_GET['url'])||isset($_GET['id'])){
          if(isset($_GET['url'])){
          
            $id=$_GET['url'];
            $id = token::decode($id);
            
            $row = getUser($id, TRUE);
            seen($id);
          }else if(isset($_GET['id'])){
          
            $id=$_GET['id'];
            $id = token::decode($id);
            $row = getUser($id, TRUE);
            seen($id);
          }
        }
        ?>
        <a href="messages.php" class="back-icon"><i class="fa fa-arrow-left"></i></a>
        <img src="<?php echo $row['ext']; ?>" alt="">
        <div class="details">
          <span><?php if (isset($row['Legal_name'])){
            echo $row['Legal_name'];
          }else{
            echo "<b>Error </b>";
          }
           ; ?></span>
        </div>
      </header>
      <div class="successy">
      </div>
      <div class="chat-box">
      </div>
      <form action="#" class="typing-area">
        <input type="text" class="To_" name="To_" value="<?php echo $id; ?>" hidden>
        <input type="text" name="message" id="testy" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
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
  <script src="../Javascript/custom.js"></script>
  <script src="../Javascript/chat/chat.js" async></script>
	
  </body>
</html>
