<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Expenses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleBBBEE.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body>
<?php session_start();?>
    <div id="expense_header">
        <p id="expense_title">business expenses</p>
    </div>  
    <div>
        <ul style="display: inline-block;" class="nav nav-pills">
            <li id="direct_expensesLabel" onclick="displayDirectExpense()" style="display: inline-block;">
                <a data-toggle='pill' href="#Direct_expense_table"><h2>Direct Expenses</h2></a>
            </li>
            <li id="direct_expensesLabe2" onclick="displayNonDirect()" style="display: inline-block;" >
                <a data-toggle='pill' href="#nondirect_expense_table"><h2>Non direct Expenses</h2></a>
            </li>
        </ul>
    </div>
    <div class='align-self-center tab-content' style='display: flex; margin:auto'>";
        <div id="Direct_expense_table" class='tab-pane fade in active' >
            <form id="behave" action="Main/Main.php" method="POST">
                <table>
                    <tr id="expense_shell" >
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
                <button type="button" id="addItem">Add Item</button>
                <input type="submit" name="DE" value="Submit Direct Expenses">
            </form>
        </div>
        <div id="nondirect_expense_table"  class="tab-pane fade"> 
            <form action="Main/Main.php" method="POST">
                <table>
                    <tr id="expense_shell" >
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
                <button type="button" id="addItem1">Add Item</button>
                <input type="submit" name="NDE" value="Submit Non-Direct Expenses">
            </form>
        </div>
    </div>
    <?php 
        $filepath = realpath(dirname(__FILE__));
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
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script src="Javascript/expense_summary.js"></script>
    <script src="jquery-3.5.1.js"></script>
    <script src="scriptBBBEE.js"></script>
</body>