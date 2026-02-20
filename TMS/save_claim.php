<?php
// Display all PHP errors (for development purposes)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'db_connect.php'; // Ensure this file properly initializes $conn

// Error handling function
function handle_error($error_message) {
    echo "Error: " . $error_message;
}

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if required POST variables are set
        if (isset($_POST['job_id'], $_POST['activity_id'], $_POST['worktype_id'], $_POST['login_id'], $_POST['user_id'])) {
            
            $start = $_POST['start'] ?? '';
            $end = $_POST['end'] ?? '';
            // Collect the form data
            $job_id = $_POST['job_id'] ?? '';
            $pm_id = $_POST['pm_id'] ?? '';
            $period = $_POST['period'] ?? '';
            $login_id = $_POST['login_id'] ?? '';
            $activity_id = $_POST['activity_id'] ?? '';
            $worktype_id = $_POST['worktype_id'] ?? '';
            $user_id = $_POST['user_id'] ?? '';
            $member = $_POST['member'] ?? '';
            $month = $_POST['month'] ?? '';
            $jobname = $_POST['jobname'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $manager = $_POST['manager'] ?? '';
            $client = $_POST['client'] ?? '';
            $worktype = $_POST['worktype'] ?? '';
            $activity = $_POST['Activity'] ?? '';
            $actual_done = $_POST['actual_done'] ?? '';
            $done = $_POST['done'] ?? '';
            $days_exceeded = $_POST['days_exceeded'] ?? '';
            
            $discount_name = $_POST['discount_name'] ?? '';
            $discount = $_POST['discount'] ?? '';
            
            $claim_status = $_POST['claim_status'] ?? '';
            
           function clean_decimal($value) {
    return is_numeric(str_replace(',', '', $value)) 
        ? (float) str_replace(',', '', $value) 
        : 0.00;
        }
        
        $rate = clean_decimal($_POST['rate'] ?? '');
        $discounted_rate = clean_decimal($_POST['discounted_rate'] ?? '');
        $member_discounted_rate = clean_decimal($_POST['member_discounted_rate'] ?? '');
        $team_discounted_rate = clean_decimal($_POST['team_discounted_rate'] ?? '');
        $totalopenlinks_serivce = clean_decimal($_POST['totalopenlinks_serivce'] ?? '');
        $total_production_team = clean_decimal($_POST['total_production_team'] ?? '');
        $Billiable_Deductable = clean_decimal($_POST['Billiable_Deductable'] ?? '');
          
    

                // Prepare the second statement for `assigned_duties`
                $stmt2 = $conn->prepare("
                        UPDATE assigned_duties 
                        SET 
                            claim_status = ?, 
                            approved_by = ?, 
                            Discount = ?, 
                            Discount_Applied = ?,
                            Pay_Out = ?, 
                            my_team_discount_rate=?,
                            Openlinks_Serivce=?,
                            Production_Team=?,
                            my_discounted_rate=?,
                            adj_claimable=?,
                            Date_Processed = NOW()
                        WHERE 
                            project_id = ? AND 
                            user_id = ? AND 
                            task_id = ? AND 
                            activity_id = ?
                    ");
                    
                        $stmt2->bind_param(
                            "iiisddddddiiii", 
                            $claim_status,               // i
                            $login_id,                   // i
                            $discount,                   // i
                            $discount_name,              // s
                            $discounted_rate, 
                            $team_discounted_rate,
                            $totalopenlinks_serivce,     // d
                            $total_production_team,      // d
                            $member_discounted_rate,     // d
                            $Billiable_Deductable,       // d
                            $job_id,                     // i
                            $user_id,                    // i
                            $worktype_id,                // i
                            $activity_id                 // i
                        );
                                            
                        if ($stmt2->execute()) {
                            echo "Record successfully saved";
                        } else {
                            echo "Error executing statement in `assigned_duties`: " . $stmt2->error;
                        }
                    
                // Provide a link back
                echo "<br><br><a href='index.php?page=period_claims&id=$job_id&start=$start&end=$end'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
        

            // Close the first statement
            $stmt2->close();
        } else {
            echo "Required fields are missing.";
        }
    }
} catch (Exception $e) {
    handle_error($e->getMessage());
}
?>
