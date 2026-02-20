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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="../style.css"> -->
    <link rel="stylesheet" href="../CSS/styleBBBEE.css">
    <link href="../CSS/Vendor/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/Vendor/nprogress.css" rel="stylesheet">
    <link href="../CSS/custom.css" rel="stylesheet">
  </head>


  <body class="nav-md">
  <?php session_start();?>
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
                                    <div class="x_content">
                                        <br />
                                        <div>
                                            <ul class='nav nav-pills align-self-center'>
                                                <li class='active'><a data-toggle='pill' href='#Direct_expense_table'>Direct Expenses</a></li>
                                                <li><a data-toggle='pill' id="advanced_search_toggle" href='#nondirect_expense_table'>Non-Direct Expenses</a></li>
                                            </ul>
                                        </div>
    <div class='align-self-center tab-content' style='display: flex; margin:auto'>
        <div id="Direct_expense_table" class='tab-pane fade in active' >
            <form id="behave" action="../Main/Main.php" method="POST">
                <table>
                    <tr class="headings">
                        <th class="expenses">Service provider procured</th>
                        <th class="expenses">Product name</th>
                        <th class="expenses">Product specification</th>
                        <th class="expenses">Rand Value</th>
                        <th class="expenses"> Frequency of Expense</th>
                    </tr>
                    <tbody id="tbody">
                    <tr>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productname[]' placeholder="Enter product name">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productspecification[]' placeholder="Enter product specification">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='randvalue[]' placeholder="Rand Value">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='frequency[]' placeholder="Frequency of expense">
                        </td>
                    </tr>
                    <tr>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productname[]' placeholder="Enter product name">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productspecification[]' placeholder="Enter product specification">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='randvalue[]' placeholder="Rand Value">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='frequency[]' placeholder="Frequency of expense">
                        </td>
                    </tr>
                    <tr>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productname[]' placeholder="Enter product name">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productspecification[]' placeholder="Enter product specification">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='randvalue[]' placeholder="Rand Value">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='frequency[]' placeholder="Frequency of expense">
                        </td>    
                    </tr>
                    </tbody>
                </table>
                <button class="btn" type="button" id="addItem">Add Item</button>
                <input class="btn btn-success" type="submit" name="DE" value="Submit Direct Expenses">
            </form>
        </div>
        <div id="nondirect_expense_table"  class="tab-pane fade"> 
            <form action="../Main/Main.php" method="POST">
                <table>
                    <tr  >
                        <th class="expenses">Service provider procured</th>
                        <th class="expenses">Product name</th>
                        <th class="expenses">Product specification</th>
                        <th class="expenses">Rand Value</th>
                        <th class="expenses"> Frequency of Expense</th>
                    </tr>
                    <tbody id="tbody1">
                    <tr>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productname[]' placeholder="Enter product name">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productspecification[]' placeholder="Enter product specification">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='randvalue[]' placeholder="Rand Value">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='frequency[]' placeholder="Frequency of expense">
                        </td>
                    </tr>
                    <tr>
                    <td class="added_expense">
                            <input class="table_input" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productname[]' placeholder="Enter product name">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productspecification[]' placeholder="Enter product specification">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='randvalue[]' placeholder="Rand Value">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='frequency[]' placeholder="Frequency of expense">
                        </td>
                    
                    </tr>
                    <tr>
                    <td class="added_expense">
                            <input class="table_input" type="text" name='serviceprovider[]' placeholder="Service provider..." >
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productname[]' placeholder="Enter product name">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='productspecification[]' placeholder="Enter product specification">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='randvalue[]' placeholder="Rand Value">
                        </td>
                        <td class="added_expense">
                            <input class="table_input" type="text" name='frequency[]' placeholder="Frequency of expense">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button class="btn" type="button" id="addItem1">Add Item</button>
                <input class="btn btn-success" type="submit" name="NDE" value="Submit Non-Direct Expenses">
            </form>
        </div>
    </div>
    <?php 
        $filepath = realpath(dirname(__FILE__, 2));
   
        include_once($filepath.'\classes\SMME.class.php');
        include_once($filepath.'\Session.php');
        $temp = new SMME();
        // $id = session::get(id);
        // if($who_this == "SMME"){
        //     $id = session::get("SMME_ID");
        // }else{
        //     //kick out
        // }
        $temp->display_expense(session::get("SMME_ID"));
    ;?>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="../JavaScript/expense_summary.js"></script>
<script src="../JavaScript/Vendor/jquery-3.5.1.js"></script>
  <script src="../JavaScript/Vendor/bootstrap.min.js"></script>
  <script src="../JavaScript/Vendor/pnotify.js"></script>
  <script src="../JavaScript/Vendor/pnotify.buttons.js"></script>
  <script src="../JavaScript/Vendor/jquery.dataTables.js"></script>
  <script src="../JavaScript/Vendor/datatables.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../JavaScript/custom.js"></script>
  
    </body>
</html>
