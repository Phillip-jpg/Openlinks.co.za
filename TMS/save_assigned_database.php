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

function escapeEmailValue($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function buildProductionPhaseEmail(array $mailData): string {
    $clientAccountName = trim((string)($mailData['client_account_name'] ?? 'Client Account'));
    $repName = trim((string)($mailData['rep_name'] ?? ''));
    $repEmail = trim((string)($mailData['rep_email'] ?? ''));
    $jobId = escapeEmailValue($mailData['project_id'] ?? '');
    $jobName = escapeEmailValue(trim((string)($mailData['job_name'] ?? 'N/A')));
    $teamName = trim((string)($mailData['team_name'] ?? 'Production'));
    $clientEmail = escapeEmailValue(trim((string)($mailData['company_email'] ?? 'N/A')));
    $scorecardTitle = escapeEmailValue(trim((string)($mailData['scorecard_title'] ?? 'N/A')));
    $workTypes = escapeEmailValue(trim((string)($mailData['work_types'] ?? 'N/A')));
    $startDate = escapeEmailValue(trim((string)($mailData['start_date'] ?? 'N/A')));
    $endDate = escapeEmailValue(trim((string)($mailData['end_date'] ?? 'N/A')));
    $createdDate = escapeEmailValue(trim((string)($mailData['created_date'] ?? 'N/A')));
    $entityName = trim((string)($mailData['entity_name'] ?? 'OpenLinks'));
    $managerName = trim((string)($mailData['manager_name'] ?? 'Project Manager'));
    $managerEmail = trim((string)($mailData['manager_email'] ?? ''));
    $managerNumber = trim((string)($mailData['manager_number'] ?? ''));

    $greeting = escapeEmailValue($clientAccountName !== '' ? $clientAccountName : 'Client Account');
    if ($repName !== '') {
        $greeting .= ' and ' . escapeEmailValue($repName);
    }

    $teamNameSafe = escapeEmailValue($teamName !== '' ? $teamName : 'Production');
    $repDisplaySafe = $repName !== ''
        ? escapeEmailValue($repName . ($repEmail !== '' ? ' (' . $repEmail . ')' : ''))
        : escapeEmailValue($repEmail !== '' ? $repEmail : 'N/A');
    $entityNameSafe = escapeEmailValue($entityName !== '' ? $entityName : 'OpenLinks');
    $managerNameSafe = escapeEmailValue($managerName !== '' ? $managerName : 'Project Manager');
    $managerEmailSafe = escapeEmailValue($managerEmail !== '' ? $managerEmail : 'N/A');
    $managerNumberSafe = escapeEmailValue($managerNumber !== '' ? $managerNumber : 'N/A');

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Job Has Entered Production</title>
    </head>
    <body style='margin:0;padding:0;background-color:#f4f6f8;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
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
                        <tr>
                            <td style='padding:30px;color:#333;font-size:15px;'>
                                <p>Dear <b>$greeting</b>,</p>
                                <p>
                                    We confirm that resourcing has now been fully completed for Job ID - <b>$jobId</b>, and the work order has officially entered the production phase.
                                </p>
                                <p>
                                    Execution is currently underway by our dedicated <b>$teamNameSafe Production Team</b>, who are assigned to support your account. The work is being carried out under the supervision of our appointed supervisors, with ongoing quality oversight conducted by our Operations Controller to ensure compliance with all required standards and specifications.
                                </p>
                                <p>
                                    Upon completion, the scope will undergo final review and formal sign-off by our Operations Managers.
                                </p>
                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                    <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$jobId</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$jobName</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Client Account</b></td><td>" . escapeEmailValue($clientAccountName !== '' ? $clientAccountName : 'N/A') . "</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Client Email</b></td><td>$clientEmail</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Representative</b></td><td>$repDisplaySafe</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Production Team</b></td><td>$teamNameSafe</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Scorecard</b></td><td>$scorecardTitle</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Work Types</b></td><td>$workTypes</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Start Date</b></td><td>$startDate</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>End Date</b></td><td>$endDate</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Date Created</b></td><td>$createdDate</td></tr>
                                </table>
                                <p>
                                    For any correspondence or additional information regarding this work order, please contact:
                                </p>
                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                    <tr><td style='background:#f0f3f7;width:35%;'><b>Entity</b></td><td>$entityNameSafe</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Project Manager</b></td><td>$managerNameSafe</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Email</b></td><td>$managerEmailSafe</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Contact Number</b></td><td>$managerNumberSafe</td></tr>
                                </table>
                                <p>
                                    Kind regards,<br>
                                    <b>OpenLinks Operations System</b>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                                <small>Automated Notification - Do not reply</small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
}

function buildEntityTeamAssignedEmail(array $mailData): string {
    $entityName = trim((string)($mailData['entity_name'] ?? 'Entity'));
    $jobId = escapeEmailValue($mailData['project_id'] ?? '');
    $jobName = escapeEmailValue(trim((string)($mailData['job_name'] ?? 'N/A')));
    $clientAccountName = escapeEmailValue(trim((string)($mailData['client_account_name'] ?? 'N/A')));
    $clientEmail = escapeEmailValue(trim((string)($mailData['company_email'] ?? 'N/A')));
    $repName = trim((string)($mailData['rep_name'] ?? ''));
    $repEmail = trim((string)($mailData['rep_email'] ?? ''));
    $teamName = escapeEmailValue(trim((string)($mailData['team_name'] ?? 'Production')));
    $scorecardTitle = escapeEmailValue(trim((string)($mailData['scorecard_title'] ?? 'N/A')));
    $workTypes = escapeEmailValue(trim((string)($mailData['work_types'] ?? 'N/A')));
    $startDate = escapeEmailValue(trim((string)($mailData['start_date'] ?? 'N/A')));
    $endDate = escapeEmailValue(trim((string)($mailData['end_date'] ?? 'N/A')));
    $createdDate = escapeEmailValue(trim((string)($mailData['created_date'] ?? 'N/A')));
    $managerName = escapeEmailValue(trim((string)($mailData['manager_name'] ?? 'Project Manager')));
    $managerEmail = escapeEmailValue(trim((string)($mailData['manager_email'] ?? 'N/A')));
    $managerNumber = escapeEmailValue(trim((string)($mailData['manager_number'] ?? 'N/A')));
    $repDisplaySafe = $repName !== ''
        ? escapeEmailValue($repName . ($repEmail !== '' ? ' (' . $repEmail . ')' : ''))
        : escapeEmailValue($repEmail !== '' ? $repEmail : 'N/A');
    $entityNameSafe = escapeEmailValue($entityName !== '' ? $entityName : 'Entity');

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Team Fully Assigned</title>
    </head>
    <body style='margin:0;padding:0;background-color:#f4f6f8;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f4f6f8;padding:30px 0;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,sans-serif;'>
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
                        <tr>
                            <td style='padding:30px;color:#333;font-size:15px;'>
                                <p>Dear <b>$entityNameSafe</b>,</p>
                                <p>
                                    Your team has now been fully assigned for Job ID - <b>$jobId</b>. Resourcing is complete and the job is ready to proceed under the allocated production team.
                                </p>
                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                    <tr><td style='background:#f0f3f7;width:35%;'><b>Job ID</b></td><td>$jobId</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Job Name</b></td><td>$jobName</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Client Account</b></td><td>$clientAccountName</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Client Email</b></td><td>$clientEmail</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Representative</b></td><td>$repDisplaySafe</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Production Team</b></td><td>$teamName</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Scorecard</b></td><td>$scorecardTitle</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Work Types</b></td><td>$workTypes</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Start Date</b></td><td>$startDate</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>End Date</b></td><td>$endDate</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Date Created</b></td><td>$createdDate</td></tr>
                                </table>
                                <p style='margin-top:18px;'>
                                    Project contact details:
                                </p>
                                <table width='100%' cellpadding='8' cellspacing='0' style='border-collapse:collapse;font-size:14px;margin-top:15px;'>
                                    <tr><td style='background:#f0f3f7;width:35%;'><b>Project Manager</b></td><td>$managerName</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Email</b></td><td>$managerEmail</td></tr>
                                    <tr><td style='background:#f0f3f7;'><b>Contact Number</b></td><td>$managerNumber</td></tr>
                                </table>
                                <p>
                                    Kind regards,<br>
                                    <b>OpenLinks Operations System</b>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style='background:#f0f3f7;padding:15px;text-align:center;font-size:12px;'>
                                <small>Automated Notification - Do not reply</small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
}

function sendProductionPhaseNotifications(mysqli $conn, int $projectId): void {
    if (!function_exists('sendEmailNotification')) {
        include_once 'send_email.php';
    }

    $projectId = (int)$projectId;
    $query = $conn->query("
        SELECT
            pl.id AS project_id,
            pl.name AS job_name,
            DATE_FORMAT(pl.start_date, '%d-%m-%Y') AS start_date,
            DATE_FORMAT(pl.end_date, '%d-%m-%Y') AS end_date,
            DATE_FORMAT(pl.date_created, '%d-%m-%Y %H:%i') AS created_date,
            c.company_name AS client_account_name,
            c.Email AS company_email,
            (
                SELECT cr1.REP_NAME
                FROM client_rep cr1
                WHERE cr1.CLIENT_ID = pl.CLIENT_ID
                  AND cr1.REP_NAME IS NOT NULL
                  AND cr1.REP_NAME <> ''
                LIMIT 1
            ) AS rep_name,
            (
                SELECT cr2.REP_EMAIL
                FROM client_rep cr2
                WHERE cr2.CLIENT_ID = pl.CLIENT_ID
                  AND cr2.REP_EMAIL IS NOT NULL
                  AND cr2.REP_EMAIL <> ''
                LIMIT 1
            ) AS rep_email,
            COALESCE(NULLIF(ts.team_name, ''), 'Production') AS team_name,
            COALESCE(NULLIF(s.Title, ''), 'N/A') AS scorecard_title,
            COALESCE((
                SELECT GROUP_CONCAT(DISTINCT tl.task_name ORDER BY tl.task_name SEPARATOR ', ')
                FROM task_list tl
                WHERE FIND_IN_SET(tl.id, pl.task_ids)
            ), 'N/A') AS work_types,
            CONCAT(pm.firstname, ' ', pm.lastname) AS manager_name,
            pm.email AS manager_email,
            pm.number AS manager_number,
            CASE
                WHEN pm.type = 2 THEN CONCAT(pm.firstname, ' ', pm.lastname)
                WHEN entity_user.id IS NOT NULL THEN CONCAT(entity_user.firstname, ' ', entity_user.lastname)
                ELSE CONCAT(pm.firstname, ' ', pm.lastname)
            END AS entity_name,
            CASE
                WHEN pm.type = 2 THEN pm.email
                WHEN entity_user.email IS NOT NULL AND entity_user.email <> '' THEN entity_user.email
                ELSE pm.email
            END AS entity_email
        FROM project_list pl
        LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
        LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
        LEFT JOIN users pm ON pm.id = pl.manager_id
        LEFT JOIN users entity_user ON entity_user.id = pm.creator_id
        LEFT JOIN yasccoza_openlink_market.scorecard s ON s.SCORECARD_ID = pl.scorecard
        WHERE pl.id = $projectId
        LIMIT 1
    ");

    if (!$query || $query->num_rows === 0) {
        return;
    }

    $mailData = $query->fetch_assoc();
    $subject = "Resourcing Completed for Job ID $projectId";
    $message = buildProductionPhaseEmail($mailData);

    $sent = array();
    $recipientMap = array(
        strtolower(trim((string)($mailData['company_email'] ?? ''))),
        strtolower(trim((string)($mailData['rep_email'] ?? '')))
    );

    foreach ($recipientMap as $recipientEmail) {
        if ($recipientEmail === '' || isset($sent[$recipientEmail])) {
            continue;
        }
        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            continue;
        }

        sendEmailNotification($recipientEmail, $subject, $message);
        $sent[$recipientEmail] = true;
    }

    $entityEmail = strtolower(trim((string)($mailData['entity_email'] ?? '')));
    if ($entityEmail === '') {
        $entityEmail = strtolower(trim((string)($mailData['manager_email'] ?? '')));
    }

    if ($entityEmail !== '' && filter_var($entityEmail, FILTER_VALIDATE_EMAIL)) {
        $entitySubject = "Your Team Has Been Fully Assigned for Job ID $projectId";
        $entityMessage = buildEntityTeamAssignedEmail($mailData);
        sendEmailNotification($entityEmail, $entitySubject, $entityMessage);
    }
}

function insertFullyAssignedPmNotification(mysqli $conn, int $projectId, int $managerId, int $teamId): void {
    $projectId = (int)$projectId;
    $managerId = (int)$managerId;
    $teamId = (int)$teamId;

    if ($projectId <= 0 || $managerId <= 0) {
        return;
    }

    $checkQuery = "
        SELECT 1
        FROM pm_notifications
        WHERE PM_ID = $managerId
          AND Job_ID = $projectId
          AND Notification_Type = 466
        LIMIT 1
    ";
    $checkResult = $conn->query($checkQuery);
    if ($checkResult && $checkResult->num_rows > 0) {
        return;
    }

    $insertQuery = "
        INSERT INTO pm_notifications (PM_ID, Job_ID, team_id, Notification_Type)
        VALUES ($managerId, $projectId, $teamId, 466)
    ";
    if (!$conn->query($insertQuery)) {
        error_log("Failed to insert fully assigned PM notification: " . $conn->error);
    }
}

function refreshProjectAssignedState(mysqli $conn, int $projectId, bool &$projectJustAssigned = false): bool {
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
        $assignedCheck = $conn->query("SELECT Assigned FROM project_list WHERE id = $projectId");
        $assignedRow = $assignedCheck ? $assignedCheck->fetch_assoc() : null;
        $wasAssigned = $assignedRow ? ((int)$assignedRow['Assigned'] === 1) : false;

        $updateQuery = "UPDATE project_list SET Assigned=1 WHERE id=$projectId";
        if (!$conn->query($updateQuery)) {

        
            handle_error("Error updating project status: " . $conn->error);
            return false;
        }

        $projectJustAssigned = !$wasAssigned;
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

                $projectJustAssigned = false;
                if (!refreshProjectAssignedState($conn, (int)$projectId, $projectJustAssigned)) {
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

                if ($projectJustAssigned) {
                    insertFullyAssignedPmNotification($conn, (int)$projectId, (int)$managerId, (int)$team_id);
                    sendProductionPhaseNotifications($conn, (int)$projectId);
                }

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
