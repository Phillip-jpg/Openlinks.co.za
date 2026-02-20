<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['contract_name']) &&
        isset($_POST['team_id']) &&
        isset($_POST['worktype'])
    ) {
        $contract_name = $_POST['contract_name'];
        $team_ids = $_POST['team_id']; // array
        $work_type = $_POST['worktype'];

        if (!empty($team_ids)) {
            // Fetch existing contract_ids to avoid duplicates
            $checkQuery = $conn->prepare("SELECT contract_id FROM contracts");
            $checkQuery->execute();
            $result = $checkQuery->get_result();

            $existingContractIds = [];
            while ($row = $result->fetch_assoc()) {
                $existingContractIds[] = $row['contract_id'];
            }
            $checkQuery->close();

            // Generate unique 5-digit contract ID
            do {
                $contract_id = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            } while (in_array($contract_id, $existingContractIds));

            // Prepare insert statement
            $insertQuery = $conn->prepare("INSERT INTO contracts (contract_id, name_of_contract, work_type_billing, team_id) VALUES (?, ?, ?, ?)");

            if ($insertQuery) {
                foreach ($team_ids as $team_id) {
                    $insertQuery->bind_param('isii', $contract_id, $contract_name, $work_type, $team_id);

                    if (!$insertQuery->execute()) {
                        echo "<p style='color:red;'>Error inserting data for team ID $team_id: " . $insertQuery->error . "</p>";
                    }
                }

                header("Location: index.php?page=contracts");
                exit();
            } else {
                echo "<p style='color:red; font-size:20px; font-weight:bold'>Error: Unable to prepare the insert query!</p>";
            }
        } else {
            echo "<p style='color:red; font-size:20px; font-weight:bold'>Error: No team IDs provided!</p>";
        }
    } else {
        echo "<p style='color:red; font-size:20px; font-weight:bold'>Error: Missing required fields!</p>";
    }
}
?>
