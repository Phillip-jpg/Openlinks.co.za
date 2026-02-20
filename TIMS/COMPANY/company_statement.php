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
  

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/font-awesome.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/pnotify.css" rel="stylesheet">
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/style.css" rel="stylesheet">
    <!-- <link href="../CSS/styleBBBEE.css" rel="stylesheet"> -->
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link rel="stylesheet" href="../CSS/spin.css">

    

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
                <h3>Registration</h3>
              </div>
              <?php require 'inc/search.php';?>
              
              <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Company Statements<small>fill in information about your company statments...</small></h2>
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
                    <form action="../Main/Main.php" method="POST" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                    <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="textarea">Introdution...<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea style="width:35vw"  id="textarea" required="required" name="Introduction" class="form-control col-md-7 col-xs-12 formz"></textarea>
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 formz" for="textarea">Vision...<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea style="width:35vw" id="textarea" required="required" name="Vision" class="form-control col-md-7 col-xs-12 formz"></textarea>
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="textarea">Mission...<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea style="width:35vw" id="textarea" required="required" name="Mission" class="form-control col-md-7 col-xs-12 formz"></textarea>
                        </div>
                      </div>
                      <!-- Modal content-->
                      <div class="modal fade" id="myModal" role="dialog">
    
                      <div class="modal-dialog">
                        
                            <div class="modal-content">
                          <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                               </div>
                               <div class="modal-body">
                                  <p id="textmodal" style="text-align:center"></p>
                               </div>
                              <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                               </div>
                            </div>
      
                           </div>
                        </div>
                      <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">Open Modal</button>
                  <!-----end of content---->
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="textarea">Values...<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea style="width:35vw" id="textarea" required="required" name="Values" class="form-control col-md-7 col-xs-12 formz"></textarea>
                        </div>
                      </div>
                      <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="textarea">Goals & Objectives...<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea style="width:35vw"  id="textarea" required="required" name="Goals_Objectives" class="form-control col-md-7 col-xs-12 formz"></textarea>
                        </div>
                      </div>
                      
                      <div class="ln_solid"></div>
                      <?php $filepath = realpath(dirname(__FILE__));
                      include_once($filepath.'/../helpers/token.php');?>
                      <input style="display:none" type="text" name="tk" value="<?php echo token::get("COMPANYSTATEMENTSYASC");?>" required="" hidden>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                         
						              <button class="btn btn-primary" type="reset">Cancel</button>
                          <button type="submit" name="COMPANYSTATEMENTS" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                   
                    </form>
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
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="../Javascript/expense_summary.js"></script>
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>

  <script src="../Javascript/Vendor/pnotify.buttons.js"></script>
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/modal.js"></script>
    <script src="../Javascript/custom.js"></script>
  
    </body>
</html>