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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="../CSS/Vendor/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/chat.css">
    <link href="../CSS/forexpense.css" rel="stylesheet">
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
                            <h3>Expense Summary</h3>
                        </div>

                        <?php require 'inc/search.php';?>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            
                                    <div class="x_title">
                                        <h2>Track all your expenses <small></small></h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="#">Settings 1</a>
                                            </li>
                                            <li>
                                                <a href="#">Settings 2</a>
                                            </li>
                                            </ul>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                        </ul> 
                                        <div class="clearfix"></div>
                                    </div>
                
                                        <br />
                                        <div>
                                            <ul class='nav nav-pills align-self-center'>
                                                <li class='active'><a data-toggle='pill' href='#Direct_expense_table'>Direct Expenses</a></li>
                                                <li><a data-toggle='pill' id="advanced_search_toggle" href='#nondirect_expense_table'>Non-Direct Expenses</a></li>
                                            </ul>
                                        </div>
                                        <br>
                                        <br>
                                            <div class='align-self-center tab-content' style='display: flex; margin:auto;'>
                                                <div id="Direct_expense_table" class='tab-pane fade in active' >
                                                    <form id="behave" action="../Main/Main.php" method="POST">
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
                                            <button type="button" id="clicked" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" style="display:none"></button>
                                        <!-----end of content---->
                                                <table class='table-responsive table table-striped'>
                                                            <thead>
                                                                <th class="expenses">Service provider procured</th>
                                                                <th class="expenses">Product name</th>
                                                                <th class="expenses">Product specification</th>
                                                                <th class="expenses">Rand Value</th>
                                                                <th class="expenses"> Frequency of Expense</th>
                                                            </thead>
                                                            <tbody id="tbody">
                                                            <tr>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productname[]' placeholder=" product name..">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productspecification[]' placeholder=" Product specification...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='randvalue[]' placeholder="Rand Value...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='frequency[]' placeholder="Frequency of expense...">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="added_expense">
                                                                    <input id="move"  required="required" class="form-control col-md-7 col-xs-12" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='productname[]' placeholder="Product name...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productspecification[]' placeholder=" Product specification...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input  id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='randvalue[]' placeholder="Rand Value...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='frequency[]' placeholder="Frequency of expense...">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"   class="form-control col-md-7 col-xs-12" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"   class="form-control col-md-7 col-xs-12" type="text" name='productname[]' placeholder=" Product name...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productspecification[]' placeholder=" Product specification...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move"  required="required" class="form-control col-md-7 col-xs-12" type="text" name='randvalue[]' placeholder="Rand Value...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='frequency[]' placeholder="Frequency of expense...">
                                                                </td>    
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                        <?php $filepath = realpath(dirname(__FILE__));
                                                         
                                                            include_once($filepath.'/../helpers/token.php');
                                                        ?>
                                                            <input style="display:none" type="text" name="tk" value="<?php echo token::get("DE");?>" required="" hidden>
                                                        <button class="btn" type="button" id="addItem">Add Item</button>
                                                        <input class="btn btn-success" type="submit" name="DE" value="Submit Direct Expenses">
                                                    </form>
                                                </div>
                                                <div id="nondirect_expense_table"  class="tab-pane fade"> 
                                                    <form action="../Main/Main.php" method="POST">
                                                        <table class='table-responsive table table-striped'>
                                                            <thead>
                                                                <th class="expenses">Service provider procured</th>
                                                                <th class="expenses">Product name</th>
                                                                <th class="expenses">Product specification</th>
                                                                <th class="expenses">Rand Value</th>
                                                                <th class="expenses"> Frequency of Expense</th>
                                                            </thead>
                                                            <tbody id="tbody1">
                                                            <tr>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='productname[]' placeholder=" Product name...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='productspecification[]' placeholder="Product specification...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='randvalue[]' placeholder="Rand Value...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='frequency[]' placeholder="Frequency of expense...">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                            <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productname[]' placeholder=" Product name...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required"  class="form-control col-md-7 col-xs-12" type="text" name='productspecification[]' placeholder=" Product specification...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='randvalue[]' placeholder="Rand Value...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='frequency[]' placeholder="Frequency of expense...">
                                                                </td>
                                                            
                                                            </tr>
                                                            <tr>
                                                            <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productname[]' placeholder="Product name...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='productspecification[]' placeholder="Product specification...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='randvalue[]' placeholder="Rand Value...">
                                                                </td>
                                                                <td class="added_expense">
                                                                    <input id="move" required="required" class="form-control col-md-7 col-xs-12" type="text" name='frequency[]' placeholder="Frequency of expense...">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                        <?php $filepath = realpath(dirname(__FILE__));
                                                       
                                                            include_once($filepath.'/../helpers/token.php');?>
                                                            <input style="display:none" type="text" name="tk" value="<?php echo token::get("NDE");?>" required="" hidden>
                                                        <button class="btn" type="button" id="addItem1">Add Item</button>
                                                        <input class="btn btn-success" type="submit" name="NDE" value="Submit Non-Direct Expenses">
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                            <?php 
                                                $filepath = realpath(dirname(__FILE__));
                                        
                                                include_once($filepath.'/../classes/SMME.class.php');
                                                $filepath = realpath(dirname(__FILE__));
                                                include_once($filepath.'/../lib/Session.php');
                                                $temp = new SMME();
                                            ?>
                                            <div>
                                            <?php
                                                $temp->display_expense(session::get("SMME_ID"), 1);
                                            ?>
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
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js   "></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="../Javascript/expense_summary.js"></script>
<script src="../Javascript/Vendor/jquery-3.5.1.js"></script>
  <script src="../Javascript/Vendor/bootstrap.min.js"></script>
  <script src="../Javascript/Vendor/pnotify.js"></script>
  <script src="../Javascript/Vendor/pnotify.buttons.js"></script>
  <script src="../Javascript/Vendor/jquery.dataTables.js"></script>
  <script src="../Javascript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../Javascript/modal.js"></script>
    <script src="../Javascript/Ajax_header.js"></script>
    <script src="../Javascript/custom.js"></script>
  
    </body>
</html>
