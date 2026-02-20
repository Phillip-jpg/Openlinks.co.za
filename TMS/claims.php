<?php

session_start();
$login_id = $_SESSION['login_id'];

if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
} else {
    echo "No dates provided.";
    exit;
}

?>

<?php
// First query to collect project IDs and total rates
$total_rates = 0;
$total_jobs = 0;
$total_activity=0;
$total_processed_rate = 0;
$unprocessed=0;
$plids = [];

$see = $conn->query("SELECT 
                            wwp.period,
                            wwp.month,
                            pl.id,
                            COUNT(DISTINCT pl.id) as unique_id_count,
                            pl.start_date, 
                            pl.end_date, 
                            pl.name, 
                            pl.JOB_TYPE, 
                            pl.status,
                            SUM(up.rate) AS total_rate,
                            COUNT(ad.activity_id) AS activity_count, 
                            CONCAT(u.firstname, ' ', u.lastname) AS manager_name,
                            (
                                SELECT COUNT(ad_all.activity_id)
                                FROM assigned_duties ad_all
                                WHERE ad_all.project_id = pl.id
                            ) AS total_project_activity_count,
                            (
                                SELECT SUM(up_all.rate*ad_all.pm_quantities)
                                FROM assigned_duties ad_all
                                JOIN user_productivity up_all ON up_all.id = ad_all.activity_id
                                WHERE ad_all.project_id = pl.id
                            ) AS total_project_rate_sum
                        FROM 
                            assigned_duties ad
                        JOIN 
                            project_list pl ON pl.id = ad.project_id
                        JOIN 
                            users u ON u.id = ad.manager_id
                        JOIN 
                            team_schedule ts ON ts.team_id = pl.team_ids
                        JOIN 
                            user_productivity up ON up.id = ad.activity_id
                                 LEFT JOIN working_week_periods wwp 
                      ON (DATE(ad.Done_Date) BETWEEN wwp.start_week AND wwp.end_week)
                        WHERE 
                            DATE(ad.Done_Date) >= '$start' AND DATE(ad.Done_Date) <= '$end'
                        GROUP BY 
                            wwp.period,
                            wwp.month,
                            pl.id, 
                            pl.start_date, 
                            pl.end_date, 
                            pl.name, 
                            pl.JOB_TYPE, 
                            pl.status,
                            u.firstname, 
                            u.lastname;");

// Collect project IDs and total rates
while ($status_row = $see->fetch_assoc()) {
    $total_jobs+=$status_row['unique_id_count'];
    $total_activity+=$status_row['total_project_activity_count'];
    $total_rates += $status_row['total_project_rate_sum'];
    $plids[] = $status_row['id']; // Store project IDs
}



// Convert $plids to a comma-separated string for the next query
$plids_str = implode(",", $plids);

// Second query using the collected project IDs
$see2 = $conn->query("SELECT DISTINCT
    wwp.period,
    wwp.month,
    pl.id,
    pl.name AS Job_Name,
    ad.user_id,
    ad.manager_id,
    ad.activity_id,
    ad.task_id,
    COALESCE(c.company_name, smme.Legal_name) AS Client,
    ad.start_date, 
    tl.task_name AS Work_Type,
    ad.Done_Date, 
    CONCAT(u.firstname, ' ', u.lastname) AS manager_name, 
    CONCAT(u1.firstname, ' ', u1.lastname) AS member,
    up.name,
    ad.claim_status,
    up.rate,
    ad.pm_quantities,
    pl.JOB_TYPE, 
    ad.activity_id,
    up.duration AS Activity_Duration,
    CASE
        WHEN ad.Done_Date >= wwp.start_week AND ad.Done_Date <= wwp.end_week THEN 'yes'
        ELSE 'no'
    END AS Done,
    CASE
        WHEN ad.Done_Date <= ad.end_date THEN 'yes'
        ELSE 'no'
    END AS Done_On_TIME,
    CASE
        WHEN (WEEKDAY(ad.start_date) + up.duration) < 5 THEN DATE_ADD(ad.start_date, INTERVAL up.duration DAY)
        WHEN (WEEKDAY(ad.start_date) + up.duration) >= 5 THEN 
            DATE_ADD(ad.start_date, INTERVAL up.duration + (2 * ((WEEKDAY(ad.start_date) + up.duration) DIV 5)) DAY)
    END AS Estimated_Completion_Date
FROM 
    assigned_duties ad
JOIN 
    project_list pl ON pl.id = ad.project_id
JOIN 
    users u ON u.id = ad.manager_id
JOIN 
    user_productivity up ON up.id = ad.activity_id
JOIN 
    team_schedule ts ON ts.team_id = pl.team_ids
LEFT JOIN 
    yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID
LEFT JOIN
    yasccoza_openlink_market.client c ON ad.CLIENT_ID = c.CLIENT_ID
LEFT JOIN
    task_list tl ON ad.task_id = tl.id
LEFT JOIN working_week_periods wwp 
  ON (DATE(ad.Done_Date) BETWEEN wwp.start_week AND wwp.end_week)
JOIN 
    users u1 ON u1.id = ad.user_id
WHERE 
    ad.project_id IN ($plids_str)
   AND (ad.claim_status = 1 OR ad.claim_status = 2)
");
    
    
    while ($row = $see2->fetch_assoc()) {
    $total_processed_rate  += ($row['rate']*$row['pm_quantities']);
    
    }
    
    $see3 = $conn->query("SELECT DISTINCT
    wwp.period,
    wwp.month,
    pl.id,
    pl.name AS Job_Name,
    ad.user_id,
    ad.manager_id,
    ad.activity_id,
    ad.task_id,
    ad.pm_quantities,
    COALESCE(c.company_name, smme.Legal_name) AS Client,
    ad.start_date, 
    tl.task_name AS Work_Type,
    ad.Done_Date, 
    CONCAT(u.firstname, ' ', u.lastname) AS manager_name, 
    CONCAT(u1.firstname, ' ', u1.lastname) AS member,
    up.name,
    ad.claim_status,
    up.rate,
    pl.JOB_TYPE, 
    ad.activity_id,
    up.duration AS Activity_Duration,
    CASE
        WHEN ad.Done_Date >= wwp.start_week AND ad.Done_Date <= wwp.end_week THEN 'yes'
        ELSE 'no'
    END AS Done,
    CASE
        WHEN ad.Done_Date <= ad.end_date THEN 'yes'
        ELSE 'no'
    END AS Done_On_TIME,
    CASE
        WHEN (WEEKDAY(ad.start_date) + up.duration) < 5 THEN DATE_ADD(ad.start_date, INTERVAL up.duration DAY)
        WHEN (WEEKDAY(ad.start_date) + up.duration) >= 5 THEN 
            DATE_ADD(ad.start_date, INTERVAL up.duration + (2 * ((WEEKDAY(ad.start_date) + up.duration) DIV 5)) DAY)
    END AS Estimated_Completion_Date
FROM 
    assigned_duties ad
JOIN 
    project_list pl ON pl.id = ad.project_id
JOIN 
    users u ON u.id = ad.manager_id
JOIN 
    user_productivity up ON up.id = ad.activity_id
JOIN 
    team_schedule ts ON ts.team_id = pl.team_ids
LEFT JOIN 
    yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID
LEFT JOIN
    yasccoza_openlink_market.client c ON ad.CLIENT_ID = c.CLIENT_ID
LEFT JOIN
    task_list tl ON ad.task_id = tl.id
LEFT JOIN working_week_periods wwp 
  ON (DATE(ad.Done_Date) BETWEEN wwp.start_week AND wwp.end_week)
JOIN 
    users u1 ON u1.id = ad.user_id
WHERE 
    ad.project_id IN ($plids_str)
    AND ad.claim_status=0");

// Fetch results and process further if necessary
while ($row1 = $see3->fetch_assoc()) {
    $unprocessed  += ($row1['rate']*$row1['pm_quantities']);
    
    
}

// Calculate the percentage of processed rates
if ($total_rates > 0) {
    $processed_percentage = ($total_processed_rate / $total_rates) * 100;
} else {
    $processed_percentage = 0; // Avoid division by zero
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Rates Summary</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        table p {
            margin: unset !important;
        }
        table td, table th {
            vertical-align: middle !important;
        }
        table thead th {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2>Project Rates Summary</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total Jobs: </strong><?php echo number_format($total_jobs); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Processed: </strong>R <?php echo number_format($total_processed_rate, 2); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Activities: </strong> <?php echo number_format($total_activity); ?></p>
                </div>
                 <div class="col-md-6">
                    <p><strong>Total Unprocessed: </strong>R <?php echo number_format($unprocessed, 2); ?></p>
                </div>
                 
                <div class="col-md-6">
                    <p><strong>Total Rates: </strong>R <?php echo number_format($total_rates, 2); ?></p>
                </div>
               
                
                
                <div class="col-md-6">
                    <p><strong>Percentage Processed: </strong><span class="text-success"><?php echo number_format($processed_percentage, 2); ?>%</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Table -->
<div class="container mt-4">
    <div class="card card-outline card-success">
        <div class="card-header bg-primary text-white">
            <strong>Filter Results</strong>
        </div>
        <div class="card-body">
            
                          <div class="form-row mb-3">
                <!-- Office Filter -->
                <div class="col-md-3">
                    <label for="status-filter">Filter by Status:</label>
                    <select id="status-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $status_qry = $conn->query("SELECT DISTINCT status FROM assigned_duties");
                        while($status_row = $status_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $status_row['status']; ?>"><?php echo $status_row['status']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="month-filter">Filter by Month:</label>
                    <select id="month-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $month_qry = $conn->query("SELECT DISTINCT month FROM working_week_periods");
                        while($month_row = $month_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                 <div class="col-md-3">
                    <label for="manager-filter">Filter by Manager:</label>
                    <select id="manager-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $manager_qry = $conn->query("
                            SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) AS manager_name 
                            FROM users u 
                            INNER JOIN assigned_duties ad ON u.id = ad.manager_id
                        ");
                        while($manager_row = $manager_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $manager_row['manager_name']; ?>"><?php echo $manager_row['manager_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <br>
                <br>
<div class="table-responsive">
    <br>
                <!-- Project Data Table -->
                <table class="table table-hover table-condensed" id="list">
                    <colgroup>
                        <col width="10%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>Job ID</th>
                            <th>Activity Count</th>
                            <th>PM Manager</th>
                            <th>Team Name</th>
                            <th>Job Start Date</th>
                            <th>Job End Date</th>
                            <th>Job Type</th>
                            <th>Status</th>
                            <th>Total Rate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qry = $conn->query("SELECT 
    wwp.period,
    wwp.month,
    pl.id, 
    pl.start_date, 
    pl.end_date,
    ts.team_name,
    pl.name, 
    pl.JOB_TYPE, 
    pl.status,
    SUM(up.rate) AS total_rate,
    COUNT(ad.activity_id) AS activity_count, 
    CONCAT(u.firstname, ' ', u.lastname) AS manager_name,
    (
        SELECT COUNT(ad_all.activity_id)
        FROM assigned_duties ad_all
        WHERE ad_all.project_id = pl.id
    ) AS total_project_activity_count,
    (
        SELECT SUM(up_all.rate*ad_all.pm_quantities)
        FROM assigned_duties ad_all
        JOIN user_productivity up_all ON up_all.id = ad_all.activity_id
        WHERE ad_all.project_id = pl.id
    ) AS total_project_rate_sum
FROM 
    assigned_duties ad
LEFT JOIN 
    project_list pl ON pl.id = ad.project_id
LEFT JOIN 
    users u ON u.id = ad.manager_id
LEFT JOIN team_schedule ts ON
    ts.team_id = pl.team_ids
LEFT JOIN 
    user_productivity up ON up.id = ad.activity_id
LEFT JOIN working_week_periods wwp 
  ON (DATE(ad.Done_Date) BETWEEN wwp.start_week AND wwp.end_week)
WHERE 
    DATE(ad.Done_Date) >= '$start' AND DATE(ad.Done_Date) <= '$end'
GROUP BY 
    wwp.period,
    wwp.month,
    pl.id, 
    pl.start_date, 
    pl.end_date, 
    pl.name, 
    pl.JOB_TYPE, 
    pl.status,
    u.firstname, 
    u.lastname;");
                        
                        while($row = $qry->fetch_assoc()):
                            
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $row['period']; ?></td>
                                <td><?php echo $row['month']; ?></td>
                                <td class="text-center"><?php echo $row['id']; ?></td>
                                <td><?php echo $row['total_project_activity_count']; ?></td>
                                <td><?php echo $row['manager_name']; ?></td>
                                 <td><?php echo $row['team_name']; ?></td>
                                <td><?php echo $row['start_date']; ?></td>
                                <td><?php echo $row['end_date']; ?></td>
                                <td><?php echo $row['JOB_TYPE']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td>R <?php echo number_format($row['total_project_rate_sum'], 2); ?></td>
                                <td>
                                    <a href="./index.php?page=period_claims&id=<?php echo $row['id']; ?>&start=<?php echo $start; ?>&end=<?php echo $end; ?>" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
 <button class="btn btn-flat bg-gradient-secondary mx-2" type="button"
  onclick="location.href='index.php?page=filter_claims'">
  Back
</button>
</div>




<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function(){
        var dataTable = $('#list').DataTable();

        $('#status-filter, #manager-filter, #month-filter').on('change', function () {
            filterTable();
        });

        function filterTable() {
            var selectedstatus = $('#status-filter').val();
            var selectedproject_manager = $('#manager-filter').val();
            var selectedmonth = $('#month-filter').val();

            dataTable.columns(8).search(selectedstatus).columns(4).search(selectedproject_manager).columns(1).search(selectedmonth).draw();
        }
    });
</script>

</body>
</html>
