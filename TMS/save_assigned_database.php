<?php
// Include the database connection file
include 'db_connect.php';

function isAjaxRequest(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function flushClientResponse(string $body): void {
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Connection: close');
        header('Content-Length: ' . strlen($body));
    }

    echo $body;

    while (ob_get_level() > 0) {
        @ob_end_flush();
    }
    @flush();

    if (function_exists('fastcgi_finish_request')) {
        @fastcgi_finish_request();
    }
}

// Error handling function
function handle_error($error_message) {
    if (isAjaxRequest()) {
        if (!headers_sent()) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo "ERROR: " . $error_message;
    } else {
        echo "Error: " . $error_message;
    }
    // Redirect to an error page or display a user-friendly error message
}

function refreshProjectAssignedState(mysqli $conn, int $projectId): bool {
    $check1 = $conn->query("SELECT task_ids FROM project_list WHERE id = $projectId")->fetch_array();
    if (!$check1) {
        handle_error("Error fetching task_ids: " . $conn->error);
        return false;
    }
    $taskIdsString = $check1['task_ids'];
    $taskIdsArray = array_map('intval', explode(',', $taskIdsString));
    $taskIdsString = implode(',', $taskIdsArray);

    $check2 = $conn->query("SELECT id FROM user_productivity WHERE task_id IN ($taskIdsString)");
    if (!$check2) {
        handle_error("Error fetching activity IDs: " . $conn->error);
        return false;
    }
    $activityIds = $check2->fetch_all(MYSQLI_ASSOC);
    if (empty($activityIds)) {
        return true;
    }

    $activityIdsString = implode(',', array_column($activityIds, 'id'));

    $check3 = $conn->query("SELECT COUNT(*) as count FROM assigned_duties 
                            WHERE activity_id IN ($activityIdsString) AND project_id = $projectId");
    if (!$check3) {
        handle_error("Error fetching count of assigned duties: " . $conn->error);
        return false;
    }
    $result1 = $check3->fetch_assoc();
    $count1 = (int)$result1['count'];

    if (count($activityIds) === $count1) {
        $updateQuery = "UPDATE project_list SET Assigned=1 WHERE id=$projectId";
        if (!$conn->query($updateQuery)) {
            handle_error("Error updating project status: " . $conn->error);
            return false;
        }
    }

    return true;
}

$isAjax = isAjaxRequest();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['project_id'], $_POST['task_id'], $_POST['activity_id'], $_POST['manager_id'], $_POST['client_id'], $_POST['user_id'])) {
        // Retrieve POST data
        $projectId = $_POST['project_id'];
        $taskId = $_POST['task_id'];
        $activityId = $_POST['activity_id'];
        $managerId = $_POST['manager_id'];
        $clientId = $_POST['client_id'];
        $team_id=$_POST['team_id'];
        
        // echo $_POST['user_id'];
   
        
        // Check if user_id is set
        if (($_POST['user_id'])>0) {
            $userId = $_POST['user_id'];

            // Fetch duration from the user_productivity table
            $qry3 = $conn->query("SELECT duration FROM user_productivity WHERE id = $activityId");
            if (!$qry3) {
                handle_error($conn->error);
                exit; // Stop script execution
            }
            $row = $qry3->fetch_object();
            if (!$row) {
                handle_error("No data found in user_productivity table for activity ID: $activityId");
                exit; // Stop script execution
            }
            $duration = $row->duration;

            // Calculate end date based on duration

            // Proceed with inserting assigned duties for each user ID
            
            date_default_timezone_set('Africa/Johannesburg');
            $currentDateTime = date('Y-m-d');
           
                // Calculate end date based on duration and current date
                $endDate = calculateWorkingEndDate($currentDateTime, $duration);
                
                
                               // INSERT NOTIFICATION
                // ----------------------------------------------------
                $insert_notifications = "
                    INSERT INTO member_notifications 
                    (PM_ID, Member_ID, Job_ID, Activity_ID, Notification_Type)
                    VALUES ($managerId, $userId, $projectId, $activityId, 3)
                ";
                $conn->query($insert_notifications);
                
                // ----------------------------------------------------
                // INSERT ASSIGNED DUTY
                // ----------------------------------------------------
                $insertQuery = "
                    INSERT INTO assigned_duties
                    (project_id, task_id, activity_id, user_id, team_id, manager_id, CLIENT_ID,
                     duration, start_date, end_date, days_left, status)
                    VALUES
                    ('$projectId', '$taskId', '$activityId', '$userId', '$team_id', '$managerId',
                     '$clientId', '$duration', '$currentDateTime', '$endDate', '$duration', 'In-progress')
                ";
                
                if (!$conn->query($insertQuery)) {
                    handle_error("Error inserting data: " . $conn->error);
                    exit;
                }

// ----------------------------------------------------
                // EMAIL SETUP
                // ----------------------------------------------------
                include 'send_email.php';
                
                // ----------------------------------------------------
                // FETCH JOB + USER DATA
                // ----------------------------------------------------
                $Query = $conn->query("
                    SELECT DISTINCT
                        pl.name AS jobname,
                        u.email AS manager_email,
                        u1.email AS member_email,
                        up.name AS activity_name,
                        ts.team_name,
                        c.company_name,
                        CONCAT(u.firstname, ' ', u.lastname) AS manager,
                        CONCAT(u1.firstname, ' ', u1.lastname) AS member,
                        NOW() AS submitted_date
                    FROM assigned_duties ad
                    LEFT JOIN user_productivity up ON ad.activity_id = up.id
                    LEFT JOIN project_list pl ON ad.project_id = pl.id
                    LEFT JOIN users u ON ad.manager_id = u.id
                    LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
                    LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
                    LEFT JOIN users u1 ON ad.user_id = u1.id
                    WHERE ad.user_id = $userId
                      AND ad.manager_id = $managerId
                      AND pl.id = $projectId
                      AND ad.activity_id = $activityId
                ");
                
                $data = $Query->fetch_assoc();
                
                // ----------------------------------------------------
                // EXTRACT DATA
                // ----------------------------------------------------
                $member_email  = $data['member_email'];
                $member_name   = $data['member'];
                $activity_name = $data['activity_name'];
                $team_name     = $data['team_name'];
                $company_name  = $data['company_name'];
                $job_name      = $data['jobname'];
                $date_submitted = $data['submitted_date'];
                
                // ----------------------------------------------------
                // FILE ATTACHMENTS
                // ----------------------------------------------------
                $fileQuery = $conn->query("
                    SELECT url, created
                    FROM yasccoza_openlink_market.rfp
                      WHERE POST_ID = $projectId
                ");
                
                $fileSection = "";
                
                if ($fileQuery && $fileQuery->num_rows > 0) {
                
                    while ($file = $fileQuery->fetch_assoc()) {
                
                        $file_url  = "https://openlinks.co.za/TIMS/STORAGE/FILES/" . $file['url'];
                        $file_date = date("d M Y", strtotime($file['created']));
                
                        $fileSection .= "
                        <div style='margin-top:10px;'>
                            <a href='$file_url' target='_blank'
                               style='text-decoration:none;color:#0f1f3d;display:inline-block;'>
                                <img src='https://openlinks.co.za/TIMS/Images/PDF_file_icon.png'
                                     style='vertical-align:middle;height:40px;width:40px;margin-right:10px;'>
                                <span style='font-size:14px;'>View Uploaded Document</span><br>
                                <small style='color:#666;'>Uploaded: $file_date</small>
                            </a>
                        </div>
                        ";
                    }
                
                } else {
                
                    $fileSection = "
                    <div style='margin-top:10px;color:#999;font-style:italic;'>
                        No documents attached
                    </div>
                    ";
                }
                
                // ----------------------------------------------------
                // MEMBER EMAIL (ASSIGNMENT)
                // ----------------------------------------------------
                $subject = "You Have Been Assigned an Activity for Job ID $projectId";
                
                $memberMessage = "
                <!DOCTYPE html>
                <html>
                <head>
                <meta charset='UTF-8'>
                <title>New Activity Assigned</title>
                </head>
                
                <body style='margin:0;padding:0;background-color:#f4f6f8;'>
                
                <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
                <tr>
                <td align='center'>
                
                <table width='600' cellpadding='0' cellspacing='0'
                style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
                
                <!-- HEADER -->
                <tr>
                <td style='padding:20px;background:#0f1f3d;color:white;'>
                <table width='100%'>
                <tr>
                <td align='left'>
                <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200'>
                </td>
                <td align='right' style='font-size:13px;line-height:18px;'>
                <b>OpenLinks Corporations (Pty) Ltd</b><br>
                314 Cape Road, Newton Park<br>
                Port Elizabeth, Eastern Cape 6070
                </td>
                </tr>
                </table>
                </td>
                </tr>
                
                <!-- BODY -->
                <tr>
                <td style='padding:30px;color:#333;font-size:15px;'>
                
                <p>Dear <b>$member_name</b>,</p>
                
                <p>
                You have been assigned the following activity <b> $activity_name </b> for the following Job:
                </p>
                
                <table width='100%' cellpadding='8' cellspacing='0'
                style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                
                <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$projectId</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$job_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Client</b></td><td>$company_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Team</b></td><td>$team_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Activity</b></td><td>$activity_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Date Assigned</b></td><td>$date_submitted</td></tr>
                
                </table>
                
                <!-- FILE SECTION -->
                <div style='margin-top:25px;padding:15px;background:#f9fafc;border:1px solid #e1e5eb;border-radius:6px;'>
                <b>Attached Document:</b><br><br>
                $fileSection
                </div>
                
                <p style='margin-top:20px;'>
                Please log in to the system to begin working on this Activity.
                </p>
                
                 <!-- BUTTON -->
                            <div style='text-align:center;margin:35px 0;'>
                            <a href='https://openlinks.co.za/index.php?page=my_progress'
                            style='background:#0f1f3d;color:#ffffff;padding:14px 30px;
                            text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                            Review Assigned
                            </a>
                            </div>
    
                <p>
                Kind regards,<br>
                <b>OpenLinks Operations System</b>
                </p>
                
                </td>
                </tr>
                
                <!-- FOOTER -->
                <tr>
                <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                <small>Automated Notification – Do not reply</small>
                </td>
                </tr>
                
                </table>
                
                </td>
                </tr>
                </table>
                
                </body>
                </html>
                ";
                
                $redirectUrl = "index.php?page=assign_duties&id=$projectId";

                if (!refreshProjectAssignedState($conn, (int)$projectId)) {
                    exit;
                }

                if ($isAjax) {
                    ignore_user_abort(true);
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        @session_write_close();
                    }
                    flushClientResponse("OK|$redirectUrl");
                }

                // SEND EMAIL
                sendEmailNotification("$member_email", $subject, $memberMessage);

                if (!$isAjax) {
                    header("Location: $redirectUrl");
                    exit; // Stop script execution
                }

                exit;
        } else {
                if ($isAjax) {
                    if (!headers_sent()) {
                        http_response_code(400);
                    }
                    echo "ERROR: Please insert member!";
                    exit;
                }

                echo "<p style='color:red; font-size:20px; font-weight:bold'>Please insert member!</p>";
                echo "<a href='index.php?page=save_assign&project_id=$projectId&activity_id=$activityId&task_id=$taskId&team_id=$team_id'>
                        <span class='badge badge-info' style='font-size:18px; font-family:segoe ui'>Back</span>
                      </a>";
        }
    } else {
        // Handle missing required fields
        if ($isAjax) {
            if (!headers_sent()) {
                http_response_code(400);
            }
            echo "ERROR: Missing data!";
            exit;
        }

       echo "<p style='color:red; font-size:20px; font-weight:bold'>Missing data!</p>";
echo "<a href='index.php?page=assign_duties&id=$projectId'>
        <span class='badge badge-info' style='font-size:18px; font-family:segoe ui'>Back</span>
      </a>";

     
    }
} else {
    // Handle invalid request method
    if ($isAjax) {
        if (!headers_sent()) {
            http_response_code(405);
        }
        echo "ERROR: Invalid request method!";
    } else {
        handle_error("Invalid request method!");
    }
    exit; // Stop script execution
}


function calculateWorkingEndDate($startDate, $duration) {
    $endDate = new DateTime($startDate);
    
    // Add the specified duration in working days
    for ($day = 1; $day <= $duration; $day++) {
        // Add one day at a time while skipping weekends (Saturday and Sunday)
        do {
            $endDate->modify("+1 day");
        } while ($endDate->format('N') >= 6 && $endDate->format('N') <= 7); // Skip Saturday (6) and Sunday (7)
    }
    
    return $endDate->format('Y-m-d'); // Return the calculated end date
}
?>
