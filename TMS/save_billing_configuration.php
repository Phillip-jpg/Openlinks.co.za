<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['contract_id']) &&
        isset($_POST['applicable_type']) &&
        isset($_POST['billing_type']) &&
        isset($_POST['description']) &&
        isset($_POST['cost']) &&
        isset($_POST['target']) &&
        // isset($_POST['rate']) &&
        isset($_POST['condition'])
    ) {
        $contract_id      = $_POST['contract_id'];
        $applicable_type  = $_POST['applicable_type'];
        $billing_type     = $_POST['billing_type'];
        $description      = $_POST['description'];
        $cost             = $_POST['cost'];
        $target           = $_POST['target'];
        // $rate             = $_POST['rate'];
        $condition        = $_POST['condition'];
        
        
       if ($target == 1 || $target == 0) {
                $rate = $cost;
            }else{
                
                $rate = $cost/$target;
            }

        
     

        // Prepare the insert statement
        $insertQuery = $conn->prepare("
            INSERT INTO billing_configuration 
            (contract_id, application, Billing_Type, Description, Cost, Target, Rate, conditions) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($insertQuery) {
            $insertQuery->bind_param(
                'iissddds',  // Correct types
                $contract_id,
                $applicable_type,
                $billing_type,
                $description,
                $cost,
                $target,
                $rate,
                $condition
            );

            if ($insertQuery->execute()) {
                header("Location: index.php?page=billing_configuration&contract_id=$contract_id");
                exit();
            } else {
                echo "<p style='color:red; font-size:20px; font-weight:bold'>Error executing query: " . $insertQuery->error . "</p>";
            }
        } else {
            echo "<p style='color:red; font-size:20px; font-weight:bold'>Error: Unable to prepare the insert query!</p>";
        }
    } else {
        echo "<p style='color:red; font-size:20px; font-weight:bold'>Error: Missing required fields!</p>";
    }
}
?>
