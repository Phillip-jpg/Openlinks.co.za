<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('db_connect.php');

if (!isset($_SESSION['login_id']) || !is_numeric($_SESSION['login_id'])) {
    exit;
}

$login_id = (int)$_SESSION['login_id'];
$period   = isset($_GET['p']) ? (int)$_GET['p'] : 0;   // e.g. 202635
$where    = isset($_GET['w']) ? (int)$_GET['w'] : 1;   // 1=Started,2=Due,3=Done
$door     = isset($_GET['d']) ? (int)$_GET['d'] : 0;   // kept because you had it

if ($period <= 0) {
    exit("Invalid period.");
}

/* ------------------------------------------------
   Week range (Mon–Fri) from period (ISO year+week)
------------------------------------------------ */
$pStr = (string)$period;                 // 202635
$isoYear = (int)substr($pStr, 0, 4);     // 2026
$isoWeek = (int)substr($pStr, 4, 2);     // 35

$weekStart = new DateTime();
$weekStart->setISODate($isoYear, $isoWeek); // Monday
$weekEnd = clone $weekStart;
$weekEnd->modify('+4 days');                // Friday

$startLabel = $weekStart->format('d M Y');
$endLabel   = $weekEnd->format('d M Y');

/* ------------------------------------------------
   Main query (NO working_week_periods)
------------------------------------------------ */
if ($where === 1) {
    // Started in this ISO week
    $qry = $conn->query("
        SELECT DISTINCT
            pl.name AS jobname,
            ad.request_days,
            ad.request_done,
            ad.project_id,
            tl.task_name,
            pl.id,
            ad.my_quantities,
            ad.my_comment,
            ad.pm_quantities,
            ad.pm_comment,
            ad.activity_id,
            pl.start_date AS job_start_date,
            pl.end_date AS job_end_date,
            up.name,
            CONCAT(u.firstname, ' ', u.lastname) AS manager,
            ad.duration,
            ad.start_date,
            MONTHNAME(ad.start_date) AS MONTH,
            ad.end_date,
            c.company_name,
            ad.days_left,
            ad.status,
            ts.team_name,
            CONCAT(u1.firstname,' ',u1.lastname) AS ops_manager
        FROM assigned_duties ad
        LEFT JOIN user_productivity up ON ad.activity_id = up.id
        LEFT JOIN task_list tl ON tl.id = up.task_id
        LEFT JOIN project_list pl ON pl.id = ad.project_id
        LEFT JOIN users u ON ad.manager_id = u.id
        LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
        LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
        LEFT JOIN users u1 ON ts.op_ids = u1.id
        WHERE ad.user_id = $login_id
          AND YEARWEEK(ad.start_date, 1) = $period
        ORDER BY pl.id
    ");
} elseif ($where === 2) {
    // Due in this ISO week (project end_date)
    $qry = $conn->query("
        SELECT DISTINCT
            pl.name AS jobname,
            ad.request_days,
            ad.request_done,
            ad.project_id,
            tl.task_name,
            pl.start_date AS job_start_date,
            pl.end_date AS job_end_date,
            pl.id,
            ad.activity_id,
            ad.my_quantities,
            ad.my_comment,
            ad.pm_quantities,
            ad.pm_comment,
            up.name,
            CONCAT(u.firstname, ' ', u.lastname) AS manager,
            ad.duration,
            ad.start_date,
            ad.end_date,
            MONTHNAME(pl.end_date) AS MONTH,
            c.company_name,
            ad.days_left,
            ad.status,
            ts.team_name,
            CONCAT(u1.firstname,' ',u1.lastname) AS ops_manager
        FROM assigned_duties ad
        JOIN user_productivity up ON ad.activity_id = up.id
        JOIN task_list tl ON tl.id = up.task_id
        JOIN project_list pl ON pl.id = ad.project_id
        JOIN users u ON ad.manager_id = u.id
        JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
        LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
        LEFT JOIN users u1 ON ts.op_ids = u1.id
        WHERE ad.user_id = $login_id
          AND YEARWEEK(pl.end_date, 1) = $period
        ORDER BY pl.id
    ");
} else {
    // Done in this ISO week
    $qry = $conn->query("
        SELECT DISTINCT
            pl.name AS jobname,
            ad.request_days,
            ad.request_done,
            ad.project_id,
            pl.start_date AS job_start_date,
            pl.end_date AS job_end_date,
            tl.task_name,
            pl.id,
            ad.activity_id,
            up.name,
            CONCAT(u.firstname, ' ', u.lastname) AS manager,
            ad.duration,
            ad.start_date,
            ad.end_date,
            ad.my_quantities,
            ad.my_comment,
            ad.pm_quantities,
            ad.pm_comment,
            ad.Done_Date,
            MONTHNAME(ad.Done_Date) AS MONTH,
            c.company_name,
            ad.days_left,
            ad.status,
            ts.team_name,
            CONCAT(u1.firstname,' ',u1.lastname) AS ops_manager
        FROM assigned_duties ad
        JOIN user_productivity up ON ad.activity_id = up.id
        JOIN task_list tl ON tl.id = up.task_id
        JOIN project_list pl ON pl.id = ad.project_id
        JOIN users u ON ad.manager_id = u.id
        JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
        LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
        LEFT JOIN users u1 ON ts.op_ids = u1.id
        WHERE ad.user_id = $login_id
          AND YEARWEEK(ad.Done_Date, 1) = $period
        ORDER BY pl.id
    ");
    $where = 3;
}

/* ------------------------------------------------
   Header text based on $where
------------------------------------------------ */
$heading = "My Progress";
$sub = "";

if ($where == 1) {
    $sub = "Jobs Assigned this week<br><span style='font-weight:normal'>{$startLabel} – {$endLabel}</span>";
} elseif ($where == 2) {
    $sub = "Jobs Due this week<br><span style='font-weight:normal'>{$startLabel} – {$endLabel}</span>";
} else {
    $sub = "Jobs Done this week<br><span style='font-weight:normal'>{$startLabel} – {$endLabel}</span>";
}

/* ------------------------------------------------
   Month dropdown options (dynamic, NO wwp)
------------------------------------------------ */
if ($where == 1) {
    $month_qry = $conn->query("
        SELECT DISTINCT MONTHNAME(ad.start_date) AS month
        FROM assigned_duties ad
        WHERE ad.user_id = $login_id
          AND YEARWEEK(ad.start_date, 1) = $period
        ORDER BY MONTH(ad.start_date)
    ");
} elseif ($where == 2) {
    $month_qry = $conn->query("
        SELECT DISTINCT MONTHNAME(pl.end_date) AS month
        FROM assigned_duties ad
        JOIN project_list pl ON pl.id = ad.project_id
        WHERE ad.user_id = $login_id
          AND YEARWEEK(pl.end_date, 1) = $period
        ORDER BY MONTH(pl.end_date)
    ");
} else {
    $month_qry = $conn->query("
        SELECT DISTINCT MONTHNAME(ad.Done_Date) AS month
        FROM assigned_duties ad
        WHERE ad.user_id = $login_id
          AND YEARWEEK(ad.Done_Date, 1) = $period
        ORDER BY MONTH(ad.Done_Date)
    ");
}

/* Client dropdown options */
$company_qry = $conn->query("
    SELECT DISTINCT c.company_name
    FROM yasccoza_openlink_market.client c
    JOIN project_list pl ON c.CLIENT_ID = pl.CLIENT_ID
    ORDER BY c.company_name
");

/* Status dropdown options */
$status_qry = $conn->query("SELECT DISTINCT status FROM assigned_duties ORDER BY status");

/* Days requested dropdown */
$days_qry = $conn->query("
    SELECT DISTINCT 
        CASE
            WHEN request_days = 1 THEN 'Requested'
            WHEN request_days = 2 THEN 'Job Complete'
            WHEN request_days = 3 THEN 'Denied'
            WHEN request_days = 5 THEN 'Granted'
        END AS status_label
    FROM assigned_duties
");

/* Done requested dropdown */
$done_qry = $conn->query("
    SELECT DISTINCT 
        CASE 
            WHEN request_done = 1 THEN 'Requested'
            WHEN request_done = 2 THEN 'Granted'
            WHEN request_done = 3 THEN 'Denied'
        END AS done_label
    FROM assigned_duties
");
?>

<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <b><?php echo $heading; ?></b>
            <a href="./index.php?page=my_progress_calendar" class="btn btn-light btn-sm" style="background-color: #032033;">
                <i class="fas fa-arrow-left mr-1"></i> Back to My Progress Calendar
            </a>
        </div>

        <div class="card-body">

            <p style="font-weight:bold"><?php echo $sub; ?></p>

            <div class="form-row mb-3">
                <div class="col-md-3">
                    <label for="month-filter">Filter by Month:</label>
                    <select id="month-filter" class="form-control">
                        <option value="">All</option>
                        <?php while($month_row = $month_qry->fetch_assoc()): ?>
                            <?php if (!empty($month_row['month'])): ?>
                                <option value="<?php echo htmlspecialchars($month_row['month']); ?>">
                                    <?php echo htmlspecialchars($month_row['month']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="created-filter">Filter by Client:</label>
                    <select id="created-filter" class="form-control">
                        <option value="">All</option>
                        <?php while($company_row = $company_qry->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($company_row['company_name']); ?>">
                                <?php echo htmlspecialchars($company_row['company_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status-filter">Filter by Status:</label>
                    <select id="status-filter" class="form-control">
                        <option value="">All</option>
                        <?php while($status_row = $status_qry->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($status_row['status']); ?>">
                                <?php echo htmlspecialchars($status_row['status']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="Days-filter">Filter by Days Requested:</label>
                    <select id="Days-filter" class="form-control">
                        <option value="">All</option>
                        <?php while($days_row = $days_qry->fetch_assoc()): ?>
                            <?php if (!empty($days_row['status_label'])): ?>
                                <option value="<?php echo htmlspecialchars($days_row['status_label']); ?>">
                                    <?php echo htmlspecialchars($days_row['status_label']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3 mt-3">
                    <label for="request_done">Filter by Done Requested:</label>
                    <select id="request_done" class="form-control">
                        <option value="">All</option>
                        <?php while($done_row = $done_qry->fetch_assoc()): ?>
                            <?php if (!empty($done_row['done_label'])): ?>
                                <option value="<?php echo htmlspecialchars($done_row['done_label']); ?>">
                                    <?php echo htmlspecialchars($done_row['done_label']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th style="width:130px">Job_ID</th>
                            <th>Month</th>
                            <th>Team Name</th>
                            <th>Production Manager</th>
                            <th>Team Leader</th>
                            <th>Client</th>
                            <th>Activity</th>
                            <th>Job</th>
                            <th>Job Start Date</th>
                            <th>Job End Date</th>
                            <th>My Closed Quantity</th>
                            <th>PM Closed Quantity</th>
                            <!-- <th>(working) Days left</th> -->
                            <th>Status</th>
                            <th>Request Done</th>
                            <!-- <th>Request More Days</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $qry->fetch_assoc()) {

                        $start_date = $row['start_date'] ?? null;
                        $days_left  = (int)($row['days_left'] ?? 0);

                        // Business days since start_date (exclude weekends)
                        $current_time = new DateTime();
                        $start_date_obj = $start_date ? new DateTime($start_date) : null;

                        $business_days_difference = 0;
                        if ($start_date_obj) {
                            $interval = $start_date_obj->diff($current_time);
                            $total_days_difference = $interval->days;

                            $weekend_days = 0;
                            for ($day = 0; $day <= $total_days_difference; $day++) {
                                $current_day = clone $start_date_obj;
                                $current_day->modify("+$day day");
                                $day_of_week = (int)$current_day->format('N'); // 1..7
                                if ($day_of_week >= 6) $weekend_days++;
                            }
                            $business_days_difference = $total_days_difference - $weekend_days;
                            $days_left -= $business_days_difference;
                        }

                        // shorten job name
                        $words = explode(' ', (string)$row['jobname']);
                        $shortenedJobName = (count($words) >= 3)
                            ? implode(' ', array_slice($words, 0, 3)) . '...'
                            : (string)$row['jobname'];

                        echo "<tr>";

                        echo "<td style='color:#428bca;font-weight:bold'>" . htmlspecialchars((string)$row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars((string)$row['MONTH']) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['team_name'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['manager'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['ops_manager'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['company_name'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['name'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars($shortenedJobName) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['job_start_date'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['job_end_date'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['my_quantities'] ?? '')) . "</td>";
                        echo "<td>" . htmlspecialchars((string)($row['pm_quantities'] ?? '')) . "</td>";

                        // if (($row['status'] ?? '') === 'Done' || ($row['status'] ?? '') === 'Dropped') {
                        //     echo "<td style='font-weight:bold; text-align:center; color:blue'>Non</td>";
                        // } else {
                        //     echo "<td style='font-weight:bold; text-align:center;'>" . htmlspecialchars((string)$days_left) . "</td>";
                        // }

                        // Status badge
                        echo "<td>";
                        $status = $row['status'] ?? '';
                        if ($status === 'Done') {
                            echo '<a href="index.php?page=job_finalised&activity_id=' . (int)$row['activity_id'] . '&project_id=' . (int)$row['project_id'] . '&period=' . $period . '&where=' . $where . '" style="color:white">
                                    <span class="badge badge-success">Done: View</span>
                                  </a>';
                        } elseif ($status === 'On-Hold') {
                            echo "<span class='badge badge-warning'>On-Hold</span>";
                        } elseif ($days_left < 0) {
                            echo "<span class='badge badge-danger'>Over Due</span>";
                        } elseif ($days_left == 0) {
                            echo "<span class='badge badge-warning'>Due Today</span>";
                        } else {
                            echo "<span class='badge badge-info'>In-progress</span>";
                        }
                        echo "</td>";

                        // Request Done
                        $request_done = (int)($row['request_done'] ?? 0);
                        if ($request_done === 0) {
                            echo '<td><button class="badge badge-info" style="border-radius:5px;">
                                    <a href="./index.php?page=job_finalisation&activity_id=' . (int)$row['activity_id'] . '&project_id=' . (int)$row['project_id'] . '&period=' . $period . '&where=' . $where . '" style="color:white">Request</a>
                                  </button></td>';
                        } elseif ($request_done === 1) {
                            echo "<td><span class='badge badge-warning'>Done Requested</span></td>";
                        } elseif ($request_done === 2) {
                            echo "<td><span class='badge badge-success'>Granted</span></td>";
                        } else {
                            echo "<td><span class='badge badge-danger'>Denied</span></td>";
                        }

                        // // Request More Days
                        // $request_days = (int)($row['request_days'] ?? 0);
                        // if ($request_days === 0) {
                        //     echo '<td>
                        //         <form method="post" action="./index.php?page=save_request">
                        //             <input type="hidden" name="done" value="000">
                        //             <input type="hidden" name="login_id" value="' . $login_id . '">
                        //             <input type="hidden" name="activity_id" value="' . (int)$row['activity_id'] . '">
                        //             <input type="hidden" name="project_id" value="' . (int)$row['project_id'] . '">
                        //             <input type="hidden" name="period" value="' . $period . '">
                        //             <input type="hidden" name="where" value="' . $where . '">
                        //             <button class="badge badge-info" style="border-radius:5px;" type="submit">Request</button>
                        //         </form>
                        //       </td>';
                        // } elseif ($request_days === 1) {
                        //     echo "<td><span class='badge badge-warning'>Requested</span></td>";
                        // } elseif ($request_days === 2) {
                        //     echo "<td><span class='badge badge-success'>Job Complete</span></td>";
                        // } elseif ($request_days === 3) {
                        //     echo "<td><span class='badge badge-danger'>Denied</span></td>";
                        // } else {
                        //     echo "<td><span class='badge badge-success'>Granted</span></td>";
                        // }

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
table p { margin: unset !important; }
table td { vertical-align: middle !important; }
</style>

<script>
$(document).ready(function() {
    var dataTable = $('#list').DataTable();

    $('#Days-filter, #month-filter, #created-filter, #status-filter, #request_done').change(function() {
        filterTable();
    });

    function filterTable() {
        var selectedMonth = $('#month-filter').val();
        var selectedClient = $('#created-filter').val();
        var selectedStatus = $('#status-filter').val();
        var selectedDays = $('#Days-filter').val();
        var selectedRequestDone = $('#request_done').val();

        // Column indexes based on YOUR table header:
        // 0 Job_ID
        // 1 Month
        // 2 Team Name
        // 3 Production Manager
        // 4 Team Leader
        // 5 Client
        // 6 Activity
        // 7 Job
        // 8 Job Start Date
        // 9 Job End Date
        // 10 My Closed Quantity
        // 11 PM Closed Quantity
        // 12 Days left
        // 13 Status
        // 14 Request Done
        // 15 Request More Days
        dataTable
            .column(1).search(selectedMonth)
            .column(5).search(selectedClient)
            .column(13).search(selectedStatus)
            .column(15).search(selectedDays)
            .column(14).search(selectedRequestDone)
            .draw();
    }
});
</script>
