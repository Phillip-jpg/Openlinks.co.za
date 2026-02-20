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

    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="../CSS/Vendor/pnotify.css" rel="stylesheet"> -->
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- <link href="../CSS/styleBBBEE.css" rel="stylesheet"> -->
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/spin.css" rel="stylesheet">
    <link href="../CSS/table.css" rel="stylesheet">
 

<!-- 
    <style>
      form .simple_searchTerm {
        width: 70%;
        padding:  10px 20px 20px 20px;
        display: block;
        border-radius: 4px;
        padding: 10px;
        border: 2px solid #070944;
      }
      .search-form-area #search {
    width: 550px;
    height: 50px;
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.7);
    padding: 0 30px;
    color: #fff !important;
    font-size: 14px;
    background-color: transparent;
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
}
    </style> -->
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
                <h3>Search</h3>
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
              <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <!-- <h2>Search <small>Find what you are looking for easily</small></h2> -->
                     <ul class='nav nav-pills row'>
        <li class='active col-sm-3 col-md-3 col-lg-3'><a data-toggle='pill' href='#search_menu1'>Search</a></li>
        <li class='col-sm-3 col-md-3 col-lg-3'><a data-toggle='pill' id="advanced_search_toggle" href='#search_menu2'>Advanced search</a></li>
        
    </ul>
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
                  <div class="x_content" >
                    <br />
                    <div class="container align-self-center">
   
    
    <div class='tab-content'>
    <div id='search_menu1' class='tab-pane fade in active'>
    <br>
    <!-- <h3>Search</h3> -->
        <div class="d-flex justify-content-center">
            <form method="POST" action="" name="search_form" class="search_area1" style="margin:auto">
                <div id="simple_search_area"style="margin:auto">
<!-- 
                <input type="search" name="unauthSearchTerm" id="unauthSearchTerm" placeholder="Type here..." style="border: solid 1px !important; outline: none;" value="<?php if(isset($_GET['s'])) echo $_GET['s'];?>">
                            <button type="submit" id="search_btn_2" style="outline: none;">Search</button> -->

                    <input type="search" style="border: solid 2px rgb(191, 191, 209); border-radius:30px; font-size:20px; width:50vw;height:6vh" id="simple_searchTerm" class="simple_searchTerm form-control col-lg-9 col-md-9 col-xs-12" value="<?php if(isset($_GET['s'])) echo $_GET['s'];?>" name="simple_searchTerm" placeholder="DevInit..." maxlength="100" required>
                    <button class="btn" type="submit" id="search_btn" style="outline: none;">
                        <img src="https://s2.svgbox.net/octicons.svg?ic=search-bold&color=000" width="20" height="20">
                    </button><br>
                </div>
            </form>
        </div>
    </div>
    <br>
        <!-- <button type="button" class="btn btn-primary" id="show_adv" >Advanced Search</button>
        <br> -->
    <div id='search_menu2' class='tab-pane fade' style="width:100%">

        <h3 style="text-align:center">Advanced Search</h3>
        <br>


        <form method="POST" action="" name="search_form2" class="search_area2" style="margin:auto" style="width:100%">
        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3" for="legalname_search">Legal Name</label>
          <input type="text" name="legalname_search" class="legaly form-control col-md-6 col-xs-6 col-lg-6 space" style="border: solid 2px rgb(191, 191, 209); border-radius:30px; " placeholder="DevInit..." maxlength="100">
        </div><br>
        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3" for="Organisational_search">Form of Ownership</label>
          <input type="text" name="Organisational_search" class="fooy form-control col-md-6 col-xs-12 col-lg-6" style="border: solid 2px rgb(191, 191, 209); border-radius:30px; " placeholder="Private..." maxlength="100">
        </div><br>

        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3" for="offices">Industry Category</label>
          <select name="offices" class="officey form-control col-md-6 col-xs-12 col-lg-6" style="border: solid 2px rgb(191, 191, 209); border-radius:30px; " id="offices">
                        <option value="" selected> --blank-- </option>
                        <option value="Office of Life Sciences">Office of Life Sciences</option>
                        <option value="Office of Energy & Transportation">Office of Energy and Transportation</option>
                        <option value="Office of Real Estate & Construction">Office of Real Estate and Construction</option>
                        <option value="Office of Manufacturing">Office of Manufacturing</option>
                        <option value="Office of Technology">Office of Technology</option>
                        <option value="Office of Trade & Services">Office of Trade and Services</option>
                        <option value="Office of Finance">Office of Finance</option>
                        <option value="Office of Structured Fincance">Office of Structured Fincance</option>
                        <option value="Office of International Corporate Finance">Office of International Corporate Finance</option>
                    </select>
        </div><br>

        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3"  for="industry_search">Industry</label>
          <select name="industry_search" id="industries" class="industryy form-control col-md-6 col-xs-12 col-lg-6"style="border: solid 2px rgb(191, 191, 209); border-radius:30px; " disabled>
            <option value="" selected> --blank-- </option>
          </select>
        </div><br>

        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3" for="product_search">Products & Services</label>
          <input type="text" name="product_search" class="producty form-control col-md-6 col-xs-6 col-lg-6" style="border: solid 2px rgb(191, 191, 209); border-radius:30px; " placeholder="Software Engineering..." maxlength="100">
        </div><br>

        <div class="row">
          <h3 class="col-md-12 col-xs-12 col-lg-12">Type Of Entities</h3> 
        </div><br>

        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3">SMME</label> <input  type="checkbox" id="entity1" name="entity1" value="SMME" checked="checked">
        </div>

        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3" >NPO</label> <input type="checkbox" id="entity2" name="entity2" value="NPO" >
        </div>

        <div class="row">
          <label class="col-md-3 col-xs-3 col-lg-3">Large Corporation</label><input  id="entity3" name="entity3" type="checkbox" value="COMPANY" >
        </div>
       
        <div class="row" style="margin:auto"> 
            <div id="btn_div2" class="col-sl-4" data-toggle="tooltip" data-placement="bottom" title="Advanced Search">
                <button class="btn" type="submit" style="border-radius:30px;" id="search_btn2" >
                    <img src="https://s2.svgbox.net/octicons.svg?ic=search-bold&color=000" width="20" height="20">
                </button>
            </div>
        </div>
</form>
        </div>
</div>
    <div id="display_results">

    </div>


                  </div>
                </div>
              </div>
             </div>
          </div>
        </div>
</div>

</div>
    </div>
    <div>
<?php 
        require 'inc/footer.php';
      ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <!-- <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script> -->
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
    <script src="../Javascript/offices.ajax.js"></script>
    <script src="../JavaScript/search.js"></script>
    
    </body>
</html>