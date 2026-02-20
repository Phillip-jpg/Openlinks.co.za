<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the necessary POST parameters are set
    if (isset($_POST['manager_id'], $_POST['activity_id'], $_POST['user_id'], $_POST['project_id'])) {
        
        $pm_comment = $_POST['pm_comment'];
        $pm_quantity = $_POST['pm_quantity'];
        
        $userId = $_POST['user_id'];
        $projectId = $_POST['project_id'];
        $managerId = $_POST['manager_id'];
        $activityId = $_POST['activity_id'];
        $days_left = $_POST['days_left'];
        $duration = $_POST['duration'];
        $status = $_POST['status'];
        $done_days = $_POST['days_left'];
        
        $period = $_POST['period'];
        $where = $_POST['where'];
        $priority = $_POST['priority'];
        
        $login_id = $_SESSION['login_id'];

        if ($status === "Done") {
            $updateQuery = "UPDATE assigned_duties
                SET 
                    request_days = 2,
                    request_done = 2,
                    status = '$status',
                    Done_Date = NOW(),
                    Final_Date = NOW(),
                    who_closed = $login_id,
                    done_days = $done_days,
                    pm_quantities = $pm_quantity,
                    pm_comment = '$pm_comment'
                WHERE 
                    user_id = $userId AND
                    project_id = $projectId AND
                    manager_id = $managerId AND
                    activity_id = $activityId
            ";

            $insert_notifications = "INSERT INTO member_notifications (Member_ID, PM_ID, Job_ID, Activity_ID, Notification_Type) VALUES ($userId, $managerId, $projectId, $activityId, 2)";
            $conn->query($insert_notifications);
            
            $insert_notifications = "INSERT INTO pm_notifications (Member_ID, PM_ID, Job_ID, Activity_ID, Notification_Type) VALUES ($userId, $managerId, $projectId, $activityId, 2)";
            $conn->query($insert_notifications);
            
            
               include 'send_email.php';
            
            $Query = $conn->query("
                SELECT DISTINCT
                    pl.name AS jobname,
                    u.email AS manager_email,
                    u1.email AS member_email,
                    up.name AS activity_name,
                    ad.my_quantities,
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

            // EXTRACT FIELDS
            $pm_email       = $data['manager_email'];
            $member_email   = $data['member_email'];
            $manager_name   = $data['manager'];
            $member_name    = $data['member'];
            $my_quantities  = $data['my_quantities'];
            $activity_name  = $data['activity_name'];
            $team_name      = $data['team_name'];
            $company_name   = $data['company_name'];
            $job_name       = $data['jobname'];
            $date_submitted = $data['submitted_date'];
            
            $fileQuery = $conn->query("
                SELECT url, created
                FROM yasccoza_openlink_market.rfp
                WHERE USER_ID = $userId
                  AND POST_ID = $projectId
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
            
            $pmCloseSubject = "Activity SIGNED OFF for Job ID $projectId";

            $pmCloseMessage = "
            <!DOCTYPE html>
            <html>
            <head>
            <meta charset='UTF-8'>
            <title>Activity Closed</title>
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
            
            <p>Dear <b>$manager_name</b>,</p>
            
            <p>
            You have <b style='color:#28a745;'>successfully SIGNED OFF</b> the following actvity done by <b>$member_name</b>.
            </p>
            
            <table width='100%' cellpadding='8' cellspacing='0'
            style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
            
            <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$projectId</td></tr>
            <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$job_name</td></tr>
            <tr><td style='background:#f0f3f7;'><b>Account Serviced</b></td><td>$company_name</td></tr>
            <tr><td style='background:#f0f3f7;'><b>Team</b></td><td>$team_name</td></tr>
            <tr><td style='background:#f0f3f7;'><b>Activity</b></td><td>$activity_name</td></tr>
            <tr><td style='background:#f0f3f7;'><b>Member Quantity Closed</b></td><td>$my_quantities</td></tr>
            <tr><td style='background:#f0f3f7;'><b>PM Quantity Closed</b></td><td>$pm_quantity</td></tr>
            <tr><td style='background:#f0f3f7;'><b>PM Comment</b></td><td>$pm_comment</td></tr>
            <tr><td style='background:#f0f3f7;'><b>Date Closed</b></td><td>$date_submitted</td></tr>
            
            </table>
            
            <!-- FILE SECTION -->
            <div style='margin-top:25px;padding:15px;background:#f9fafc;border:1px solid #e1e5eb;border-radius:6px;'>
            <b>Attached Document:</b><br><br>
            $fileSection
            </div>
            
            <p>
            Kind regards,<br>
            <b>OpenLinks Operations System</b>
            </p>
            
            </td>
            </tr>
            
            <!-- FOOTER -->
            <tr>
            <td style='background:#f0f3f7;padding:20px;font-size:12px;color:#555;text-align:center;'>
            Telephone: 041 004 0454 |
            <a href='https://www.openlinks.co.za' style='color:#0f1f3d;text-decoration:none;'>www.openlinks.co.za</a>
            <br><br>
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
            
            sendEmailNotification("$pm_email", $pmCloseSubject, $pmCloseMessage);
            
            
            $memberCloseSubject = "Your Activity Has Been SIGNED OFF for Job ID $projectId";

                $memberCloseMessage = "
                <!DOCTYPE html>
                <html>
                <head>
                <meta charset='UTF-8'>
                <title>Activity SIGNED OFF</title>
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
                Your Activity has been <b style='color:#28a745;'>successfully SIGNED OFF</b> by your Project Manager:<span style='font-weight:bold'> $manager_name</span>
                and recorded in the system.
                </p>
                
                <table width='100%' cellpadding='8' cellspacing='0'
                style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                
                <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$projectId</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$job_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Account Serviced</b></td><td>$company_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Team</b></td><td>$team_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Activity</b></td><td>$activity_name</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Member Quantity Closed</b></td><td>$my_quantities</td></tr>
                <tr><td style='background:#f0f3f7;'><b>PM Quantity Closed</b></td><td>$pm_quantity</td></tr>
                <tr><td style='background:#f0f3f7;'><b>PM Comment</b></td><td>$pm_comment</td></tr>
                <tr><td style='background:#f0f3f7;'><b>Date Closed</b></td><td>$date_submitted</td></tr>
            
                
                </table>
                
                <!-- FILE SECTION -->
                <div style='margin-top:25px;padding:15px;background:#f9fafc;border:1px solid #e1e5eb;border-radius:6px;'>
                <b>Attached Document:</b><br><br>
                $fileSection
                </div>
                
                <p style='margin-top:20px;'>
                If you have any questions, please contact your Project Manager.
                </p>
                
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
                
                sendEmailNotification($member_email, $memberCloseSubject, $memberCloseMessage);
            
            
            if ($conn->query($updateQuery) === TRUE) {
                if (!empty($period)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=my_team_progress_period&p=$period&w=$where'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } elseif (!empty($priority)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=priority_requests'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } else {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=all_my_teams_progress'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                }
            } else {
                // Handle database update error
                echo "Error updating data: " . $conn->error . "<br>";
            }
            
        } elseif ($status === "Denied") {
            $updateQuery = "UPDATE assigned_duties
                SET request_done=3, status = '$status', Done_Date = CURDATE(), done_days = $done_days
                WHERE user_id = $userId
                AND project_id = $projectId
                AND manager_id = $managerId
                AND activity_id = $activityId
            ";

            if ($conn->query($updateQuery) === TRUE) {
                if (!empty($period)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=my_team_progress_period&p=$period&w=$where'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } elseif (!empty($priority)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=priority_requests'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } else {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=all_my_teams_progress'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                }
            } else {
                // Handle database update error
                echo "Error updating data: " . $conn->error . "<br>";
            }
            
        } elseif ($duration == 11) {
            $updateQuery = "UPDATE assigned_duties
                SET request_days = 3, status = '$status'
                WHERE user_id = $userId
                AND project_id = $projectId
                AND manager_id = $managerId
                AND activity_id = $activityId";
        
            if ($conn->query($updateQuery) === TRUE) {
                if (!empty($period)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=my_team_progress_period&p=$period&w=$where'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } elseif (!empty($priority)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=priority_requests'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } else {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=all_my_teams_progress'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                }
            } else {
                // Handle database update error
                echo "Error updating data: " . $conn->error . "<br>";
            }
            
        } elseif ($duration != 11) {
            $new_days_left = $duration + $days_left;

            $updateQuery = "UPDATE assigned_duties
                SET request_days = 5,
                    days_left = $new_days_left,
                    status = '$status'
                WHERE user_id = $userId
                  AND project_id = $projectId
                  AND manager_id = $managerId
                  AND activity_id = $activityId";

            if ($conn->query($updateQuery) === TRUE) {
                if (!empty($period)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=my_team_progress_period&p=$period&w=$where'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } elseif (!empty($priority)) {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=priority_requests'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                } else {
                    echo "<p style='color:red; font-size:20px; font-weight:bold'>Changes have been successfully made ! </p>";
                    echo "<a  href='index.php?page=all_my_teams_progress'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
                }
            } else {
                // Handle database update error
                echo "Error updating data: " . $conn->error . "<br>";
            }
        }
    }
}
?>