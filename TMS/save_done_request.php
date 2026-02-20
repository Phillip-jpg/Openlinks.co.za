<?php
include 'db_connect.php';
include 'send_email.php';

// 1. Get Parameters safely
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$team_id    = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;
$manager_id = isset($_GET['manager_id']) ? intval($_GET['manager_id']) : 0;

if ($project_id === 0) {
    die("Invalid Project ID");
}

// 2. Insert Notification & Update Status
$insert_notifications = "INSERT INTO pm_notifications (PM_ID, Job_ID, team_id, Notification_Type) VALUES ($manager_id, $project_id, $team_id, 4)";
$conn->query($insert_notifications);

$updateQuery = $conn->prepare("UPDATE project_list SET status = 'Done', Job_Done = NOW() WHERE id = ?");
$updateQuery->bind_param('i', $project_id);
$updateQuery->execute();

// 3. Fetch Data (Fetching all rows in case there are multiple activities/members)
$queryStr = "SELECT DISTINCT
                pl.name AS jobname,
                pl.id AS Job_ID,
                u.email AS manager_email,
                tl.task_name,
                up.name AS activity_name,
                ts.team_name,
                cr.REP_NAME, cr.REP_EMAIL, cr.REP_CONTACT,
                c.company_name, c.Email AS Company_Email,
                ad.my_quantities, ad.pm_quantities, ad.my_closing_date, ad.Final_Date, 
                ad.my_comment,
                ad.pm_comment,
                CONCAT(u.firstname, ' ', u.lastname) AS manager_name,
                CONCAT(u1.firstname, ' ', u1.lastname) AS member_name,
                NOW() AS submitted_date
            FROM assigned_duties ad
            LEFT JOIN user_productivity up ON ad.activity_id = up.id
            LEFT JOIN project_list pl ON ad.project_id = pl.id
            LEFT JOIN users u ON ad.manager_id = u.id
            LEFT JOIN team_schedule ts ON ts.team_id = pl.team_ids
            LEFT JOIN task_list tl ON tl.id = ad.task_id
            LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
            LEFT JOIN users u1 ON ad.user_id = u1.id
            LEFT JOIN client_rep cr ON cr.CLIENT_ID = c.CLIENT_ID
            WHERE pl.id = $project_id";

$result = $conn->query($queryStr);
$rows = [];
while($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

if (count($rows) > 0) {
    $data = $rows[0]; // Header data from first row
    
    // Build Activity Tables Dynamically
    $memberActivityHtml = "";
    $managerActivityHtml = "";

    foreach ($rows as $r) {
        $memberActivityHtml .= "
        <table width='100%' cellpadding='8' cellspacing='0' style='border:1px solid #eee; font-size:14px; margin-bottom:10px;'>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Member</b></td><td>{$r['member_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Worktype</b></td><td>{$r['task_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Activity</b></td><td>{$r['activity_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Quantities</b></td><td>{$r['my_quantities']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Comment</b></td><td>{$r['my_comment']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Closed Date</b></td><td>{$r['my_closing_date']}</td></tr>
        </table>";

        $managerActivityHtml .= "
        <table width='100%' cellpadding='8' cellspacing='0' style='border:1px solid #eee; font-size:14px; margin-bottom:10px;'>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Worktype</b></td><td>{$r['task_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Activity</b></td><td>{$r['activity_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>PM Quantities</b></td><td>{$r['pm_quantities']}</td></tr>
             <tr><td style='background:#f0f3f7;width:35%;'><b>PM Comment</b></td><td>{$r['pm_comment']}</td></tr>
              <tr><td style='background:#f0f3f7;width:35%;'><b>Member</b></td><td>{$r['member_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Manager</b></td><td>{$r['manager_name']}</td></tr>
            <tr><td style='background:#f0f3f7;width:35%;'><b>Signoff Date</b></td><td>{$r['Final_Date']}</td></tr>
        </table>";
    }

    // 4. Construct the Email
    $subject = "Job Completion Summary: " . $data['jobname'];
    $pmMessage = "
    <html>
    <body style='margin:0;padding:0;background-color:#f4f6f8; font-family:Arial,sans-serif;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='padding:30px 0;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='padding:20px;background:#0f1f3d;color:white;'>
                                <table width='100%'>
                                    <tr>
                                        <td><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='60' alt='OpenLinks'></td>
                                        <td align='right' style='font-size:12px;'><b>OpenLinks Corporations</b><br>Port Elizabeth, 6070</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:30px;color:#333;'>
                                <p>Dear <b>{$data['company_name']}</b>,</p>
                                <p>Please find the completion summary for the job related to your account.</p>
                                
                                <h3 style='color:#0f1f3d;'>Job Details</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
                                    <tr><td style='background:#f0f3f7;'><b>Account Management Serviced</b></td><td>{$data['company_name']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Rep</b></td><td>{$data['REP_NAME']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Rep Email</b></td><td>{$data['REP_EMAIL']}</td></tr>
                                     <tr><td style='background:#f0f3f7;'><b>Rep Contact</b></td><td>{$data['REP_CONTACT']}</td></tr>
                                     <tr><td style='background:#f0f3f7;'><b>Team Serving</b></td><td>{$data['team_name']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>{$data['jobname']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Job ID</b></td><td>{$data['Job_ID']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Date Finished</b></td><td>{$data['submitted_date']}</td></tr>
                                </table>

                                <h3 style='color:#0f1f3d; margin-top:20px;'>Activities Completed (Requesting Member)</h3>
                                $memberActivityHtml

                                <h3 style='color:#0f1f3d; margin-top:20px;'>Activities Completed (Signoff Manager)</h3>
                                $managerActivityHtml

                                <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
     $pmMessage1 = "
    <html>
    <body style='margin:0;padding:0;background-color:#f4f6f8; font-family:Arial,sans-serif;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='padding:30px 0;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.1);'>
                        <tr>
                            <td style='padding:20px;background:#0f1f3d;color:white;'>
                                <table width='100%'>
                                    <tr>
                                        <td><img src='https://openlinks.co.za/TMS/Image_Redone.png' height='60' alt='OpenLinks'></td>
                                        <td align='right' style='font-size:12px;'><b>OpenLinks Corporations</b><br>Port Elizabeth, 6070</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:30px;color:#333;'>
                                <p>Dear <b>{$data['manager_name']}</b>,</p>
                                <p>Please find the completion summary for the job related to your Team : <b>{$data['team_name']}</b>.</p>
                                
                                <h3 style='color:#0f1f3d;'>Job Details</h3>
                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;'>
                                    <tr><td style='background:#f0f3f7;'><b>Account Management Serviced</b></td><td>{$data['company_name']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Rep</b></td><td>{$data['REP_NAME']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Rep Email</b></td><td>{$data['REP_EMAIL']}</td></tr>
                                     <tr><td style='background:#f0f3f7;'><b>Rep Contact</b></td><td>{$data['REP_CONTACT']}</td></tr>
                                     <tr><td style='background:#f0f3f7;'><b>Team Serving</b></td><td>{$data['team_name']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>{$data['jobname']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Job ID</b></td><td>{$data['Job_ID']}</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Date Finished</b></td><td>{$data['submitted_date']}</td></tr>
                                </table>

                                <h3 style='color:#0f1f3d; margin-top:20px;'>Activities Completed (Requesting Member)</h3>
                                $memberActivityHtml

                                <h3 style='color:#0f1f3d; margin-top:20px;'>Activities Completed (Signoff Manager)</h3>
                                $managerActivityHtml

                                <p>Kind regards,<br><b>OpenLinks Operations System</b></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    
    
    $company_email=$data['Company_Email'];
    $rep_email=$data['REP_EMAIL'];
    $manager_email=$data['manager_email'];
    
    
    // 5. Send Email to variables, NOT strings
    if(!empty($data['Company_Email'])) sendEmailNotification("$company_email", $subject, $pmMessage);
    if(!empty($data['REP_EMAIL']))     sendEmailNotification("$rep_email", $subject, $pmMessage);
    if(!empty($data['manager_email']))     sendEmailNotification("$manager_email", $subject, $pmMessage1);
}

// 6. Output Result
if ($updateQuery) {
    echo "<div style='text-align:center; margin-top:50px;'>
            <p style='color:green; font-size:24px; font-weight:bold'>Job marked as 'Done' and notifications sent!</p>
            <a href='index.php?page=priority_jobs_done' style='text-decoration:none; background:#17a2b8; color:white; padding:10px 20px; border-radius:5px;'>Back to List</a>
          </div>";
} else {
    echo "<p style='color:red;'>Error updating database.</p>";
}
?>