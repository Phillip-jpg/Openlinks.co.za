<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['contract_id'], $_POST['name_of_contract'], $_POST['date_created'], $_POST['work_type'])) {
        $team_ids = $_POST['new_team_id']; // should be an array
        $contract_id = intval($_POST['contract_id']);
        $name_of_contract = $_POST['name_of_contract'];
        $date_created = $_POST['date_created'];
        $work_type = $_POST['work_type'];
        
        
        $new_team_ids= $_POST['new_team_id'];
        
        $new_contract_name = $_POST['new_contract_name'];
        
        $delete_team  = $_POST['delete_team'];
  

       if (!empty($team_ids)) {
                    $insertQuery = $conn->prepare("
                        INSERT INTO contracts (contract_id, name_of_contract, date_created, work_type_billing, team_id) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                
                    if ($insertQuery) {
                        foreach ($new_team_ids as $team_id) {
                            $team_id = intval($team_id);
                            $insertQuery->bind_param('isssi', $contract_id, $name_of_contract, $date_created, $work_type, $team_id);
                
                            if (!$insertQuery->execute()) {
                                echo "<p style='color:red;'>Insert Error (Team ID $team_id): {$insertQuery->error}</p>";
                            }
                        }
                    } else {
                        echo "<p style='color:red;'>Insert query preparation failed.</p>";
                    }
                }
                
                // Update contract name
                if (!empty($new_contract_name)) {
                    $updateQuery = $conn->prepare("
                        UPDATE contracts SET name_of_contract = ? WHERE contract_id = ?
                    ");
                
                    if ($updateQuery) {
                        $updateQuery->bind_param('si', $new_contract_name, $contract_id);
                
                        if (!$updateQuery->execute()) {
                            echo "<p style='color:red;'>Update Error (Contract $new_contract_name): {$updateQuery->error}</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Update query preparation failed.</p>";
                    }
                }
                
                // Delete team from contract
                if (!empty($delete_team)) {
                    $deleteQuery = $conn->prepare("DELETE FROM contracts WHERE contract_id = ? AND team_id = ?");
                
                    if ($deleteQuery) {
                        $deleteQuery->bind_param('ii', $contract_id, $delete_team);
                
                        if (!$deleteQuery->execute()) {
                            echo "<p style='color:red;'>DELETE Error (Team ID $delete_team): {$deleteQuery->error}</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Delete query preparation failed.</p>";
                    }
                }
                
                // Redirect after all operations
                header("Location: index.php?page=configure_contract&contract_id=$contract_id");
                exit;
        
    } else {
        echo "<p style='color:red;'>Missing required fields.</p>";
    }
} else {
    echo "<p style='color:red;'>Invalid request method.</p>";
}
?>
