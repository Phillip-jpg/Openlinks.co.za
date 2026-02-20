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
    <link rel="icon" href="../Images/fav.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="../CSS/Vendor/pnotify.css" rel="stylesheet"> -->
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    
  </head>

  <body>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">

  <?php require 'inc/sidebar.php';?>
  <?php require 'inc/header.php';?>

  <div class="right_col" role="main">
          <div class="">
  <div class="page-title">
              <div class="title_left">
                <h3>Criteria</h3>
              </div>

              <?php require 'inc/search.php';?>
              <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><small>Remove question from this criteria</small></h2>
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
                    <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="../MARKET/ROUTE.php?q=<?php echo $_GET['q']?>&w=<?php echo $_GET['w']?>" Method="POST">
                      
                      <div class="ln_solid"></div>
                      <?php $filepath = realpath(dirname(__FILE__));
                      include_once($filepath.'/../helpers/token.php');?>
                      <input type="text" name="tk" value="<?php echo token::get("QUESTION_REMOVE_OPENLINKS");?>" required="" hidden>

                        <!-- Modal content-->
                      <div class="modal fade" id="myModal" role="dialog">
    
                      <div class="modal-dialog">
                        
                            <div class="modal-content">
                          <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                               </div>
                               <div class="modal-body">
                                  <p id="textmodal" style="text-align:center">Are you sure you want to remove this question?</p>
                               </div>
                              <div class="modal-footer">
                                
                          <?php 
                            if(isset($_GET['t'])){
                              switch($_GET['t']){
                                case 1://smme
                                    echo '<button type="submit" class="btn btn-default" name="SMME_QUESTION_REMOVE" >Remove</button>';
                                    break;
                                case 2: //company
                                  echo '<button type="Submit" class="btn btn-success" name="COMPANY_QUESTION_REMOVE">Remove</button>';
                                  break;
                                case 3://adimin
                                  echo '<button type="Submit" class="btn btn-success" name="ADMIN_QUESTION_REMOVE">Remove</button>';
                                  break;
                              }
                            }
                          ?>
                             
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                               </div>
                            </div>
      
                           </div>
                        </div>
                  <!-----end of content modal---->
                  <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">Open Modal</button>
                     
                      
                    </form>

                  </div>
                </div>
              </div>
             </div>
             </div>
          </div>
          </div>
          <?php require 'inc/footer.php';?>
 </div>
</div>
  
  <script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script>
     $('#clicked').trigger('click');
  </script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <!-- <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script> -->
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
  <script src="../Javascript/modal.js"></script>
  <script src="../Javascript/Ajax_header.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/custom.js"></script>
    
    </body>
</html>