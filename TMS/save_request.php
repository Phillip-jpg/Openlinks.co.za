<?php
// Include the database connection file
include 'db_connect.php';

function finishAsyncResponse(string $body): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    ignore_user_abort(true);
    @set_time_limit(0);

    if (function_exists('fastcgi_finish_request')) {
        echo $body;
        @fastcgi_finish_request();
        return;
    }

    @apache_setenv('no-gzip', '1');
    @ini_set('zlib.output_compression', '0');
    @ini_set('implicit_flush', '1');

    header('Connection: close');
    header('Content-Length: ' . strlen($body));

    echo $body;

    while (ob_get_level() > 0) {
        @ob_end_flush();
    }

    @flush();
}

$isAjaxRequest = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
$emailJobs = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if the necessary POST parameters are set
    if (isset($_POST['login_id'], $_POST['activity_id'], $_POST['project_id'])) {
        
        $projectId = (int)$_POST['project_id'];
        $loginId = (int)$_POST['login_id']; // Assuming 'login_id' is passed as 'id'
        $activityId = (int)$_POST['activity_id'];
        $pm_id = isset($_POST['pm_id']) ? (int)$_POST['pm_id'] : 0;
        $done = $_POST['done'] ?? '';
        $my_quantity = $_POST['my_quantity'] ?? '';
        $my_comment = $_POST['my_comment'] ?? '';
        
      
               
            $period = $_POST['period'] ?? '';
            $where = $_POST['where'] ?? '';
            $door = $_POST['door'] ?? '';
            $priority = $_POST['priority'] ?? '';
          
            
            if ($pm_id <= 0) {
                $pmLookup = $conn->query("
                    SELECT manager_id
                    FROM assigned_duties
                    WHERE user_id = $loginId
                      AND project_id = $projectId
                      AND activity_id = $activityId
                    LIMIT 1
                ");

                if ($pmLookup && $pmLookup->num_rows > 0) {
                    $pmRow = $pmLookup->fetch_assoc();
                    $pm_id = (int)($pmRow['manager_id'] ?? 0);
                }
            }

            if($done==000){
                
                $updateQuery = "UPDATE assigned_duties
                                    SET request_days = 1
                                    WHERE user_id = $loginId
                                    AND project_id = $projectId
                                    AND activity_id = $activityId";
            }elseif($done==111){
                
                      $updateQuery = "UPDATE assigned_duties
                                        SET 
                                            request_done = 1,
                                            my_comment = '$my_comment',
                                            my_quantities = '$my_quantity',
                                            my_closing_date = NOW()
                                        WHERE 
                                            user_id = $loginId AND
                                            project_id = $projectId AND
                                            activity_id = $activityId;";
                                            
                if ($pm_id > 0) {
                    $insert_notifications = "INSERT INTO pm_notifications (Member_ID, PM_ID, Job_ID, Activity_ID, Notification_Type) VALUES ($loginId, $pm_id,$projectId ,$activityId, 1)";
                    $conn->query($insert_notifications);
                    
                    $insert_notifications = "INSERT INTO member_notifications (Member_ID, PM_ID, Job_ID, Activity_ID, Notification_Type) VALUES ($loginId, $pm_id,$projectId ,$activityId, 1)";
                    $conn->query($insert_notifications);
                }
                
                include 'send_email.php';
                
                // Get PM + Member email + job info
                $managerFilterSql = $pm_id > 0 ? "AND ad.manager_id = $pm_id" : "";
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
                    WHERE ad.user_id = $loginId
                      $managerFilterSql
                      AND pl.id = $projectId
                      AND ad.activity_id = $activityId
                ");
                
      
                    $fileQuery = $conn->query("
                        SELECT url, created
                        FROM yasccoza_openlink_market.rfp
                        WHERE USER_ID = $loginId
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
                    $data = ($Query && $Query->num_rows > 0) ? $Query->fetch_assoc() : [];

                    // EXTRACT FIELDS
                    // ---------------------------
                    $pm_email       = (string)($data['manager_email'] ?? '');
                    $member_email   = (string)($data['member_email'] ?? '');
                    $manager_name   = (string)($data['manager'] ?? '');
                    $member_name    = (string)($data['member'] ?? '');
                    $activity_name  = (string)($data['activity_name'] ?? '');
                    $team_name      = (string)($data['team_name'] ?? '');
                    $company_name   = (string)($data['company_name'] ?? '');
                    $job_name       = (string)($data['jobname'] ?? '');
                    $date_submitted = (string)($data['submitted_date'] ?? date('Y-m-d H:i:s'));
                    
                    // ---------------------------
                    // PM EMAIL
                    // ---------------------------
                    $subject = "DONE Request for Job ID $projectId";
                    
                    $pmMessage = "
                        <!DOCTYPE html>
                            <html>
                            <head>
                            <meta charset='UTF-8'>
                            <title>DONE Request</title>
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
                            <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200' alt='OpenLinks'>
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
                            <b>$member_name</b> has requested sign-off for the following job:
                            </p>
                            
                            <table width='100%' cellpadding='8' cellspacing='0'
                            style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                            
                            <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$projectId</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$job_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Account Serviced</b></td><td>$company_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Team in Service</b></td><td>$team_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Activity</b></td><td>$activity_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Quantity Closed</b></td><td>$my_quantity</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Comment</b></td><td>$my_comment</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Date Submitted</b></td><td>$date_submitted</td></tr>
                            </table>
                            
                            <!-- FILE SECTION -->
                            <div style='margin-top:25px;padding:15px;background:#f9fafc;border:1px solid #e1e5eb;border-radius:6px;'>
                            <b>Attached Document:</b><br><br>
                                $fileSection
                            </div>
                            
                            <!-- BUTTON -->
                            <div style='text-align:center;margin:35px 0;'>
                            <a href='https://openlinks.co.za/index.php?page=priority_requests'
                            style='background:#0f1f3d;color:#ffffff;padding:14px 30px;
                            text-decoration:none;border-radius:5px;font-size:15px;font-weight:bold;'>
                            Review Request
                            </a>
                            </div>
                            
                            <p>
                            Thank you for your attention and timely action.
                            </p>
                            
                            <p>
                            Kind regards,<br>
                            <b>OpenLinks Operations System</b>
                            </p>
                            
                            </td>
                            </tr>
                            
                            <!-- FOOTER -->
                            <tr>
                            <td style='background:#f0f3f7;padding:20px;font-size:12px;color:#555;text-align:center;'>
                            Telephone: 041 004 0454 &nbsp;|&nbsp;
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
                    if (!empty($pm_email)) {
                        $emailJobs[] = [
                            'to' => (string)$pm_email,
                            'subject' => (string)$subject,
                            'message' => (string)$pmMessage
                        ];
                    }
                    
                    // ---------------------------
                    // MEMBER EMAIL
                    // ---------------------------
                    $memberSubject = "DONE Request Submitted for Job ID $projectId";
                    
                    $memberMessage = "
                   <html>
                            <head>
                            <meta charset='UTF-8'>
                            <title>DONE Request</title>
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
                            <img src='https://openlinks.co.za/TMS/Image_Redone.png' height='80' width='200' alt='OpenLinks'>
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
                            
                        <tr>
                            <!-- BODY -->
                        <td style='padding:30px;font-size:15px;color:#333;'>
                        
                        <h3 style='color:#28a745;'>DONE Request Submitted Successfully</h3>
                        
                        <p>Dear <b>$member_name</b>,</p>
                        
                        <p>Your DONE request has been sent to your Project Manager
                        (<b>$manager_name</b>).</p>
                        
                        <table width='100%' cellpadding='8' cellspacing='0'
                        style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                        
                            <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$projectId</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$job_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Account Serviced</b></td><td>$company_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Team in Service</b></td><td>$team_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Activity</b></td><td>$activity_name</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Quantity Closed</b></td><td>$my_quantity</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Comment</b></td><td>$my_comment</td></tr>
                            <tr><td style='background:#f0f3f7;'><b>Date Submitted</b></td><td>$date_submitted</td></tr>
                        
                        </table>
                        
                            <!-- FILE SECTION -->
                            <div style='margin-top:25px;padding:15px;background:#f9fafc;border:1px solid #e1e5eb;border-radius:6px;'>
                            <b>Attached Document:</b><br><br>
                                $fileSection
                            </div>
                        
                        
                        <p style='margin-top:20px;'>
                        You will be notified once the request has been reviewed.
                        </p>
                        
                        <p>
                        Kind regards,<br>
                        <b>OpenLinks Operations System</b>
                        </p>
                        
                        </td>
                        </tr>
                        
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
                    
                    if (!empty($member_email)) {
                        $emailJobs[] = [
                            'to' => (string)$member_email,
                            'subject' => (string)$memberSubject,
                            'message' => (string)$memberMessage
                        ];
                    }
                }
        

        if ($conn->query($updateQuery) === TRUE) {
            if (!empty($emailJobs)) {
                register_shutdown_function(static function () use ($emailJobs): void {
                    include_once 'send_email.php';
                    if (!function_exists('sendEmailNotification')) {
                        return;
                    }

                    foreach ($emailJobs as $job) {
                        $to = trim((string)($job['to'] ?? ''));
                        $subject = (string)($job['subject'] ?? '');
                        $message = (string)($job['message'] ?? '');

                        if ($to === '' || $subject === '' || $message === '') {
                            continue;
                        }

                        sendEmailNotification($to, $subject, $message);
                    }
                });
            }

            if ($isAjaxRequest) {
                finishAsyncResponse("OK");
                exit;
            }
            
            echo "<p style='color:red; font-size:20px; font-weight:bold'>Data saved successfully ! </p>";
            
          
            
            if(!empty($period)){
                
                 echo "<a  href='index.php?page=my_progress_period&p=$period&w=$where'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
            }elseif($priority){
                
                echo "<a  href='index.php?page=my_priority_jobs_due'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
            }else{
                
                echo "<a  href='index.php?page=my_progress'><span class='badge badge-info' style='font-size:18px; font-family: segoe ui'> Back</span></a>";
            }

        } else {
            // Handle database update error
            if ($isAjaxRequest) {
                http_response_code(500);
                echo "Error updating data: " . $conn->error;
                exit;
            }
            echo "Error updating data: " . $conn->error . "<br>";
        }
    } else {
        // Handle missing or incomplete POST parameters
        if ($isAjaxRequest) {
            http_response_code(400);
            echo "Error: Missing or incomplete parameters.";
            exit;
        }
        echo "Error: Missing or incomplete parameters.<br>";
    }
}
