<?php
include('db_connect.php');

if (isset($_SESSION['login_id']) && is_numeric($_SESSION['login_id'])) {
    
    $login_id = $_SESSION['login_id'];
    
    $period = intval($_GET['p']);
    $where = intval($_GET['w']);
    
if($_GET['w'] == 1){
    
 $qry = $conn->query("SELECT DISTINCT
    CONCAT(u.firstname, ' ', u.lastname) AS fullname,
    c.company_name,
    pl.id,
    ts.team_name,
    CONCAT(u1.firstname, ' ', u1.lastname) AS ops_manager,
    CONCAT(u2.firstname, ' ', u2.lastname) AS project_manager,
    ad.user_id,
    ad.activity_id,
    ad.project_id,
    pl.name AS jobname,
    ad.request_days,
    tl.task_name,
    up.name,
    ad.done_days,
    ad.request_done,
    ad.duration,
    ad.start_date,
    MONTHNAME(ad.start_date) AS MONTH,
    ad.end_date,
    ad.days_left,
    ad.status
FROM assigned_duties ad
LEFT JOIN user_productivity up ON ad.activity_id = up.id
LEFT JOIN task_list tl ON tl.id = up.task_id
LEFT JOIN users u ON u.id = ad.user_id
LEFT JOIN project_list pl ON ad.project_id = pl.id
LEFT JOIN users u2 ON u2.id = ad.manager_id
LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
LEFT JOIN users u1 ON ts.op_ids = u1.id
WHERE YEARWEEK(ad.start_date, 1) = $period
AND ad.manager_id = $login_id
ORDER BY pl.id
");
    
    
}



elseif($_GET['w'] == 2){
    
   $qry = $conn->query("SELECT DISTINCT
    CONCAT(u.firstname, ' ', u.lastname) AS fullname,
    c.company_name,
    pl.id,
    ts.team_name,
    CONCAT(u1.firstname, ' ', u1.lastname) AS ops_manager,
    CONCAT(u2.firstname, ' ', u2.lastname) AS project_manager,
    ad.user_id,
    ad.activity_id,
    ad.project_id,
    pl.name AS jobname,
    ad.request_days,
    tl.task_name,
    up.name,
    ad.done_days,
    ad.duration,
    ad.start_date,
    ad.end_date,
    MONTHNAME(pl.end_date) AS MONTH,
    ad.days_left,
    ad.status
FROM assigned_duties ad
JOIN user_productivity up ON ad.activity_id = up.id
JOIN task_list tl ON tl.id = up.task_id
JOIN users u ON u.id = ad.user_id
JOIN project_list pl ON ad.project_id = pl.id
JOIN users u2 ON u2.id = ad.manager_id
JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
LEFT JOIN users u1 ON ts.op_ids = u1.id
WHERE YEARWEEK(ad.end_date, 1) = $period
AND ad.manager_id = $login_id
ORDER BY pl.id
");
    
    
}


elseif($_GET['w'] == 3){
    
  $qry = $conn->query("SELECT DISTINCT
    CONCAT(u.firstname, ' ', u.lastname) AS fullname,
    c.company_name,
    pl.id,
    ts.team_name,
    CONCAT(u1.firstname, ' ', u1.lastname) AS ops_manager,
    CONCAT(u2.firstname, ' ', u2.lastname) AS project_manager,
    ad.user_id,
    ad.activity_id,
    ad.project_id,
    pl.name AS jobname,
    ad.request_days,
    tl.task_name,
    up.name,
    ad.done_days,
    ad.duration,
    MONTHNAME(ad.Done_Date) AS MONTH,
    ad.start_date,
    ad.end_date,
    ad.days_left,
    ad.status
FROM assigned_duties ad
JOIN user_productivity up ON ad.activity_id = up.id
JOIN task_list tl ON tl.id = up.task_id
JOIN users u ON u.id = ad.user_id
JOIN project_list pl ON ad.project_id = pl.id
JOIN users u2 ON u2.id = ad.manager_id
JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
LEFT JOIN users u1 ON ts.op_ids = u1.id
WHERE YEARWEEK(ad.Done_Date, 1) = $period
AND ad.manager_id = $login_id
ORDER BY pl.id
");
    
    
}


$period = (int)$period; // e.g. 202635 (ISO year+week)
$year   = (int)substr($period, 0, 4);
$week   = (int)substr($period, 4, 2);

// Monday of the ISO week
$weekStart = new DateTime();
$weekStart->setISODate($year, $week); // Monday
$weekEnd = clone $weekStart;
$weekEnd->modify('+4 days'); // Friday

$start = $weekStart->format('d M Y');
$end   = $weekEnd->format('d M Y');



if ($where == 1) {

    echo '<p style="font-weight:bold">
        Jobs Assigned this week<br>
        <span style="font-weight:normal">' . $start . ' – ' . $end . '</span>
    </p>';

} elseif ($where == 2) {

    echo '<p style="font-weight:bold">
        Jobs Due this week<br>
        <span style="font-weight:normal">' . $start . ' – ' . $end . '</span>
    </p>';

} elseif ($where == 3) {

    echo '<p style="font-weight:bold">
        Jobs Done this week<br>
        <span style="font-weight:normal">' . $start . ' – ' . $end . '</span>
    </p>';

}


}


$qry3 = $conn->query("SELECT start_week, end_week, period
                     FROM working_week_periods WHERE period = $period");

// Check if the query executed successfully
if ($qry3) {
    // Check if there are rows returned
    if ($qry3->num_rows > 0) {
        // Fetch each row
        while ($row = $qry3->fetch_assoc()) {
            // Display the values of start_week and end_week
            echo 'Week: ' . ucwords($row['period']) . ' <p style="color:red;">' . ucwords($row['start_week']) . ' - ' . ucwords($row['end_week']) . '</p>';



            
            // If you only want to display the first result, you can break the loop here
            // break;
        }
    }
}
?>

<div class="col-lg-12">
    	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
	
            <b>My Team Progress</b>
            <a href="./index.php?page=my_teams_progress_calendar" class="btn btn-light btn-sm" style="background-color: #032033;">
                <i class="fas fa-arrow-left mr-1"></i> Back to My Team Calendar
            </a>
        </div>
        <div class="card-body">
            <div class="form-row mb-3">
		    <div class="col-md-3">
                <label for="month-filter">Filter by Month:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $month_qry = $conn->query("SELECT DISTINCT wwp.month
FROM project_list pl, working_week_periods wwp WHERE wwp.start_week>= pl.date_created AND wwp.end_week>=pl.date_created");
                    while($month_row = $month_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="col-md-3">
                <label for="created-filter">Filter by Client:</label>
                <select id="created-filter" class="form-control">
                    <option value="">All</option> 
                    <?php
                    $company_qry = $conn->query("SELECT DISTINCT c.company_name FROM yasccoza_openlink_market.client c, project_list pl WHERE c.CLIENT_ID = pl.CLIENT_ID");
                    while($company_row = $company_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $company_row['company_name']; ?>"><?php echo $company_row['company_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="member-filter">Filter by Member:</label>
                <select id="member-filter" class="form-control">
                    <option value="">All</option> 
                    <?php
                    // Corrected SQL query to fetch unique members
                    $member_qry = $conn->query("
                        SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) as member
                        FROM `assigned_duties` ad
                        LEFT JOIN `users` u ON ad.user_id = u.id
                    ");
                    // Loop through the results and create options for each member
                    while($member_row = $member_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo htmlspecialchars($member_row['member']); ?>">
                            <?php echo htmlspecialchars($member_row['member']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

             <div class="col-md-3">
                <label for="status-filter">Filter by Status:</label>
                <select id="status-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $status_qry = $conn->query("SELECT DISTINCT status FROM `assigned_duties`");
                    while($status_row = $status_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $status_row['status']; ?>"><?php echo $status_row['status']; ?></option>
                          <option value="">Over Due</option>
                    <?php endwhile; ?>
                 
                </select>
            </div>
              <div class="col-md-3">
                <label for="Days-filter">Filter by Days Requested:</label>
                <select id="Days-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $days_qry = $conn->query("SELECT DISTINCT 
    CASE 
        WHEN request_days = 0 THEN 'Not Yet'
        WHEN request_days = 2 THEN 'Job Complete No Days Required'
        WHEN request_days = 3 THEN 'Denied'
        WHEN request_days = 5 THEN 'Granted'
    END as status_label
FROM `assigned_duties`

");
                    while($days_row =  $days_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $days_row['status_label']; ?>"><?php echo $days_row['status_label']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="request_done">Filter by Done Requested:</label>
                <select id="request_done" class="form-control">
                    <option value="">All</option>
                    <?php
                  $done_qry = $conn->query("
   SELECT DISTINCT 
    CASE 
        WHEN request_done = 0 THEN 'Not Yet'
        WHEN request_done = 1 THEN 'Requested'
        WHEN request_done = 2 THEN 'Granted'
        WHEN request_done = 3 THEN 'Denied'
    END as done_label
FROM `assigned_duties`
");
                    while($done_row = $done_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $done_row['done_label']; ?>"><?php echo $done_row['done_label']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
         
               	</div>
            <div class="table-responsive">
          <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
               	<thead style="background-color:#032033 !important; color:white">
                        <tr>
                          <th style="width:300px ! Important">Job_ID</th>
                                 <th>Month</th>
                                 <th>Team Name</th>
                                  <th>Production Manager</th>
                                    <th>Team leader</th>
                                     <th>Client</th>
                                       <th>Activity</th>
                                        <th>Member</th>
                                        <th>Job</th>
                                        <th>End Date</th>
								<th>(working) Days left</th>
								<th>Status</th>
                                     <th>Done Request</th>
                                      <!-- <th>Days Request</th> -->
                                <th>Grant (DAYS/STATUS)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $qry->fetch_assoc()) {
                        $start_date = $row['start_date'];
                        $days_left = $row['days_left'];

                        $end_date = $row['end_date'];
                        if (is_string($end_date)) {
                            $end_date_formatted = $end_date;
                        } elseif ($end_date instanceof DateTime) {
                            $end_date_formatted = $end_date->format('Y-m-d');
                        }

                        $current_time = new DateTime();
                        $start_date_obj = new DateTime($start_date);
                        $interval = $start_date_obj->diff($current_time);
                        $total_days_difference = $interval->days;

                        $weekend_days = 0;
                        for ($day = 0; $day <= $total_days_difference; $day++) {
                            $current_day = clone $start_date_obj;
                            $current_day->modify("+$day day");
                            $day_of_week = $current_day->format('N');
                            if ($day_of_week >= 6) {
                                $weekend_days++;
                            }
                        }

                        $business_days_difference = $total_days_difference - $weekend_days;
                        $start_day = new DateTime($row['start_date']);
                        $duration = $row['duration'];
                        $end_date = clone $start_day;

                        for ($day = 1; $day <= $duration; $day++) {
                            do {
                                $end_date->modify("+1 day");
                            } while ($end_date->format('N') >= 6 && $end_date->format('N') <= 7);
                        }

                        $end_date->modify("-1 day");
                        $days_left -= $business_days_difference;

                        $words = explode(' ', $row['jobname']);
                        $shortenedJobName = '';
                        if (count($words) >= 3) {
                            $shortenedJobName = implode(' ', array_slice($words, 0, 4)) . '...';
                        } else {
                            $shortenedJobName = $row['jobname'];
                        }

                        echo "<tr>";
                        echo "<td style='color:#428bca;font-weight:bold'>" . $row['id'] . "</td>";
                         echo "<td>" . $row['MONTH'] . "</td>";
                         echo "<td style='width:300px !important'>" . $row['team_name'] . "</td>";
                            echo "<td>" . $row['project_manager'] . "</td>";
                           echo "<td style='width:300px !important'>" . $row['ops_manager'] . "</td>";
                      echo "<td>" . $row['company_name'] . "</td>";
                         echo "<td>" . $row['name'] . "</td>";
                          echo "<td>" . $row['fullname'] . "</td>";
                        echo "<td>" . $shortenedJobName . "</td>";
                        echo "<td>" . $end_date_formatted. "</td>";
                        if ($row['status'] == 'Done' || $row['status'] == 'Dropped') {
                            echo "<td style='font-weight:bold; text-align: center;'>" . $row['done_days'] . "</td>";
                        } else {
                            echo "<td style='font-weight:bold; text-align: center;'>" . $days_left . "</td>";
                        }
                        echo "<td>";
                        if ($row['status'] == 'Done') {
                            echo "<span class='badge badge-success'>{$row['status']}</span>";
                        } elseif ($row['status'] == 'On-Hold') {
                            echo "<span class='badge badge-warning'>{$row['status']}</span>";
                        } elseif ($row['status'] == 'Dropped') {
                            echo "<span class='badge badge-danger'>{$row['status']}</span>";
                        } elseif ($days_left < 0) {
                            echo "<span class='badge badge-danger'>Over Due</span>";
                        } elseif ($days_left == 0) {
                            echo "<span class='badge badge-warning'>Due Today</span>";
                        } elseif ($days_left > 0) {
                            echo "<span class='badge badge-info'>In-progress</span>";
                        }
                        echo "</td>";
                        
                         if ($row['request_done'] == 0) {
                            echo "<td><span class='badge badge-info'>Not Yet</span></td>";
                        } elseif ($row['request_done'] == 1) {
                            echo "<td><span class='badge badge-warning'>Requested</span></td>";
                        } elseif ($row['request_done'] == 2) {
                            echo "<td><span class='badge badge-success'>Granted</span></td>";
                        } elseif ($row['request_done'] == 3) {
                            echo "<td><span class='badge badge-danger'>Denied</span></td>";
                        }
                        // if ($row['request_days'] == 0) {
                        //     echo "<td><span class='badge badge-info'>Not Yet</span></td>";
                        // } elseif ($row['request_days'] == 1) {
                        //     echo "<td><span class='badge badge-warning'>Requested</span></td>";
                        // } elseif ($row['request_days'] == 2) {
                        //     echo "<td><span class='badge badge-success'>Job Complete No Days Required</span></td>";
                        // } elseif ($row['request_days'] == 3) {
                        //     echo "<td><span class='badge badge-danger'>Denied</span></td>";
                        // } elseif ($row['request_days'] == 5) {
                        //     echo "<td><span class='badge badge-info'>Granted</span></td>";
                        // }
                        
                            echo '<td>
                            <form id="assign-form" method="post" action="./index.php?page=grant_days">
                                <input type="hidden" name="user_id" value="' . $row['user_id'] . '">
                                <input type="hidden" name="id" value="' . $login_id . '">
                                <input type="hidden" name="activity_id" value="' . $row['activity_id'] . '">
                                <input type="hidden" name="days_left" value="' . $days_left . '">
                                <input type="hidden" name="project_id" value="' . $row['project_id'] . '">
                                <input type="hidden" name="where" value="' . $where . '">
                                <input type="hidden" name="period" value="' . $period . '">
                                <button class="badge badge-success" style="border-radius: 5px;" type="submit">
                                    GRANT
                                </button>
                            </form>
                        </td>';
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    table p {
        margin: unset !important;
    }
    table td {
        vertical-align: middle !important;
        white-space: normal; 
        word-wrap: break-word;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var dataTable = $('#list').DataTable();

        // Event listener for each filter dropdown
        $('#Days-filter').change(function() {
            filterTable();
        });

        $('#month-filter').change(function() {
            filterTable();
        });
        
        $('#member-filter').change(function() {
            filterTable();
        });
        $('#created-filter').change(function() {
            filterTable();
        });

        $('#status-filter').change(function() {
            filterTable();
        });

        $('#request_done').change(function() {
            filterTable();
        });

        // Function to filter the DataTable
        function filterTable() {
            
             var selectedMember = $('#member-filter').val();
            var selectedMonth = $('#month-filter').val();
            var selectedClient = $('#created-filter').val();
            var selectedStatus = $('#status-filter').val();
            var selectedDays = $('#Days-filter').val();
            var selectedRequestDone = $('#request_done').val();

            // Apply filter for each column:
            dataTable
                .column(1).search(selectedMonth) // Month filter on 2nd column (index 1)
                .column(2).search(selectedMember)
                .column(4).search(selectedClient)
                // Client filter on 5th column (index 4)
                .column(8).search(selectedStatus) // Status filter on 9th column (index 8)
                .column(9).search(selectedDays) // Days Request filter on 10th column (index 9)
                .column(10).search(selectedRequestDone) // Done Request filter on 11th column (index 10)
                .draw(); // Redraw the table with the new filters
        }
    });
</script>

