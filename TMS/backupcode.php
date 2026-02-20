<?php 
function printReportData() {
    include('db_connect.php');

    // Excel file name for download
    $fileName = "Jobs_Period_report_" . date('Y-m-d') . ".xls";

    // Column names
    $fields = array(
        'period',
        'start_week',
        'end_week',
        'Job_Status_Completion',
        'Due_This_Period',
        'Completed_This_Period',
        'Created_This_Period',
        'date_created',
        'start_date',
        'end_date',
        'Date_Job_Finished',
        'Job_ID',
        'Job_Name',
        'Manager_Name',
        'CLIENT',
        'scorecard',
        'status',
        'Date_Post_Created',
        'Date_Post_verified',
        'time_taken',
    );

    // SQL query to fetch data from the database
    $sql = "SELECT
        wwp.period,
        wwp.start_week,
        wwp.end_week,
        pl.name AS Job_Name,
        pl.id AS Job_ID,
        CONCAT(u.firstname, ' ', u.lastname) AS Manager_Name,
        c.company_name AS CLIENT,
        pl.scorecard,
        pl.status,
        pl.Job_Done AS Date_Job_Finished,
        MIN(pl.start_date) AS start_date,
        MAX(pl.end_date) AS end_date,
        pl.date_created,
        CASE
            WHEN pl.end_date >= wwp.start_week AND pl.end_date <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Due_This_Period,
        CASE
            WHEN pl.Job_Done >= wwp.start_week AND pl.Job_Done <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Completed_This_Period,
        CASE
            WHEN pl.date_created >= wwp.start_week AND pl.date_created <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Created_This_Period,
        CASE
            WHEN pl.Date_Post_Created >= wwp.start_week AND pl.Date_Post_Created <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Date_Post_Created,
        CASE
            WHEN pl.Date_Post_Verified >= wwp.start_week AND pl.Date_Post_Verified <= wwp.end_week THEN 'yes'
            ELSE 'no'
        END AS Date_Post_Verified,
        CASE
            WHEN pl.Date_Post_Created IS NOT NULL AND pl.Date_Post_Verified IS NOT NULL THEN
                FLOOR((UNIX_TIMESTAMP(pl.Date_Post_Verified) - UNIX_TIMESTAMP(pl.Date_Post_Created)) / (60 * 60 * 24))
            ELSE
                NULL
        END AS time_taken
    FROM
        project_list pl
    JOIN
        working_week_periods wwp
    ON
        (pl.date_created >= wwp.start_week AND pl.date_created <= wwp.end_week)
        OR
        (pl.Date_Post_Created >= wwp.start_week AND pl.Date_Post_Created <= wwp.end_week)
        OR
        (pl.Date_Post_Verified >= wwp.start_week AND pl.Date_Post_Verified <= wwp.end_week)
        OR
        (pl.end_date >= wwp.start_week AND pl.end_date <= wwp.end_week)
        OR
        (pl.Job_Done >= wwp.start_week AND pl.Job_Done <= wwp.end_week)
    LEFT JOIN 
        users u ON pl.manager_id = u.id
    LEFT JOIN 
        client c ON pl.CLIENT_ID = c.CLIENT_ID
    GROUP BY
        wwp.start_week, wwp.end_week, wwp.period, pl.name, pl.scorecard, pl.status, pl.manager_id, pl.id
    ORDER BY
        wwp.start_week, wwp.end_week, wwp.period";

    // Prepare the data as tab-separated values
    $excelData = implode("\t", array_values($fields)) . "\n";
    $result = $conn->query($sql);

    // Check if there are any projects found
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine the job status based on conditions
            $jobStatus = '';
            if ($row['Date_Job_Finished'] == null) {
                $jobStatus = 'Still in Progress';
            } elseif ($row['Date_Job_Finished'] > $row['end_date']) {
                $jobStatus = 'Finished_Over_Time';
            } elseif ($row['Date_Job_Finished'] < $row['end_date']) {
                $jobStatus = 'Finished_On_Time';
            }

            if (empty($row['Date_Post_Created'])) {
                $created = "created on TMS";
            } else {
                $created = $row['Date_Post_Created'];
            }

            if (empty($row['Date_Post_Verified'])) {
                $verified = "not yet verified";
            } else {
                $verified = $row['Date_Post_Verified'];
            }

            $time_taken = "Not applicable";
            if (!empty($row['Date_Post_Created']) && !empty($row['Date_Post_Verified'])) {
                $time_taken = $row['time_taken'];
            }

            // Append job status to the line data
            $lineData = array(
                $row['period'],
                $row['start_week'],
                $row['end_week'],
                $jobStatus,
                $row['Due_This_Period'],
                $row['Completed_This_Period'],
                $row['Created_This_Period'],
                $row['date_created'], 
                $row['start_date'],
                $row['end_date'],
                $row['Date_Job_Finished'],
                $row['Job_ID'],
                $row['Job_Name'],
                $row['Manager_Name'],
                $row['CLIENT'],
                $row['scorecard'],
                $row['status'],
                $created,
                $verified,
                $time_taken,
            );

            $excelData .= implode("\t", array_values($lineData)) . "\n";
        }
    }

    // Set headers for Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    // Output the Excel data
    echo $excelData;

    // Terminate script execution
    exit();
}
