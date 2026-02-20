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
                    <h2>Directors <small>fill in directors about your company ...</small></h2>
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
                    <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action="../Main/Main.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Name:<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input style="width:35vw" type="text" id="first-name" name="Name[]" required="required" class="form-control col-md-7 col-xs-12 formz">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Surname:<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input style="width:35vw" type="text" id="first-name" name="Surname[]" required="required" class="form-control col-md-7 col-xs-12 formz">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Identification Type</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select style="width:35vw" class="form-control col-md-7 col-xs-12 formz" name="IDType[]">
                            <option value="SA_ID">South Africa ID</option>
                            <option value="Passport">Passport</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">ID Number/Passport</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input style="width:35vw" id="middle-name" name="IDNumber[]" class="form-control col-md-7 col-xs-12 formz" type="text" >
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name"  class="control-label col-md-3 col-sm-3 col-xs-12">Ethinic Group</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                        <input style="width:35vw" id="middle-name" class="form-control col-md-7 col-xs-12 formz" name="Race[]" list="Ethnicc1">
            <datalist style="width:35vw" id="Ethnicc1">
            <datalist id="Ethnicc1">
             <option value="Black">Black</option>
             <option value="White">White</option>
             <option value="Coloured">Coloured</option>
             <option value="Indian">Indian</option>
            </datalist><br/>
                        </div>
                      </div>
                      <div class="form-group" >
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Gender</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                <select name="Gender[]"  size="1" style="width:35vw" class="form-control col-md-7 col-xs-12 formz"  >
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <!--Other not relavent-->
                </select><br/>
                        </div>
                      </div>
                    
                 <div class="item form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" id="inputGroupFileAddon01" for="number">Upload ID/Passport<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12 custom-file">
                          <input style="border-style: none;" type="file" style="width:35vw" name="IDcopy" class="form-control col-md-7 col-xs-12 custom-file-input formz" id="inputGroupFile01" >
                        </div>
                      </div>
            
                      <div class="ln_solid"></div>
                      <?php $filepath = realpath(dirname(__FILE__));
                      include_once($filepath.'/../helpers/token.php');?>
                      <input type="text" name="tk" value="<?php echo token::get("Company_directors_YASC");?>" required="" hidden>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
						  <button class="btn btn-primary" type="reset">Cancel</button>
                          <button name="Company_directors_YASC" type="submit" class="btn btn-success">Submit</button>
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
             <?php require 'inc/footer.php'; ?>
             </div>
             
            


             <script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <!-- <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script> -->
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
    <script src="../Javascript/modal.js"></script>
    <script src="../Javascript/offices.ajax.js"></script>
    </body>
</html>
