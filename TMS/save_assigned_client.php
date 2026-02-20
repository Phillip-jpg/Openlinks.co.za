<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are present in the form submission
    if (isset($_POST['manager_id']) && isset($_POST['client_ids']) && is_array($_POST['client_ids'])) {
        // Get the manager ID and client IDs from the form
        $manager_id = $_POST['manager_id'];
        $client_ids = $_POST['client_ids'];

        // Prepare the SQL statement
        $insertQuery = $conn->prepare("INSERT INTO accountng_officers (Accounting_Officer_ID, CLIENT_ID) VALUES (?, ?)");

        // Begin a transaction
        $conn->begin_transaction();
        try {
            // Loop through the client IDs and insert each one with the manager ID
            foreach ($client_ids as $client_id) {
                // Bind the parameters (manager_id and client_id) and execute the statement
                $insertQuery->bind_param('ii', $manager_id, $client_id);
                $insertQuery->execute();
            }
            
            // Commit the transaction if everything is successful
            $conn->commit();
             echo "
                    <p style='color:green; font-size:20px; font-weight:bold'>
                        Member duplicated and assigned to PM successfully!
                    </p>
                    <a href='index.php?page=save_orbit'>
                        <button style='
                            padding:10px 18px;
                            font-size:16px;
                            font-weight:600;
                            background:#17a2b8;
                            color:#fff;
                            border:none;
                            border-radius:5px;
                            cursor:pointer;
                        '>
                            ⬅ Back
                        </button>
                    </a>
                ";

        
        
        } catch (Exception $e) {
            // Roll back the transaction if an error occurs
            $conn->rollback();
            echo "Error inserting data: " . $e->getMessage();
        }
    } else {
        // Handle the case when required fields are missing in the form submission
        echo "Error: Missing required fields!";
    }
}
?>