<!--<div class="d-flex gap-3 flex-wrap">-->
<!--  <a href="index.php?page=all_my_teams_progress" style="margin-left:20px" class="btn btn-primary">View all team progress</a>-->
<!--<a href="index.php?page=priority_requests" style="margin-left:20px" class="btn btn-primary">Priority Requests</a>-->
<!--</div>-->
<!--<br>-->
<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('db_connect.php');

if (isset($_SESSION['login_id']) && is_numeric($_SESSION['login_id'])) {
    $login_id = (int) $_SESSION['login_id']; // Cast to integer for safety

    $qry2 = $conn->query("
        SELECT
    wwp.period,
    wwp.start_week,
    wwp.end_week,
    MONTH(wwp.start_week) AS Month_Created,
    (
    SELECT
        COUNT(ad.start_date)
    FROM
        yasccoza_tms_db.assigned_duties ad
    WHERE
        ad.start_date BETWEEN wwp.start_week AND wwp.end_week
) AS Assigned,
(
    SELECT
        COUNT(ad1.end_date)
    FROM
        yasccoza_tms_db.assigned_duties ad1
    WHERE
        ad1.end_date BETWEEN wwp.start_week AND wwp.end_week
) AS Due,
(
    SELECT
        COUNT(ad2.Done_Date)
    FROM
        yasccoza_tms_db.assigned_duties ad2
    WHERE
        ad2.Done_Date BETWEEN wwp.start_week AND wwp.end_week
) AS Done
FROM
    yasccoza_tms_db.working_week_periods wwp
WHERE
    YEAR(wwp.start_week) = YEAR(CURRENT_DATE)
ORDER BY
    wwp.start_week,
    wwp.end_week,
    wwp.period;
    ");

    $jobPeriods = [];
    while ($row = $qry2->fetch_assoc()) {
        $jobPeriods[] = $row;
    }

    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $display = '<div style="background-color:white !important">';

    $weeks_in_months = array_column($jobPeriods, 'Month_Created');
    $actual_counts = array_count_values($weeks_in_months);

    $start = $done = $due = $looper = 0;

    for ($i = 0; $i < 12; $i++) {
        $month_count = isset($actual_counts[$i + 1]) ? $actual_counts[$i + 1] : 0;
        $total = $totaldone = $totaldue = 0;

        for ($j = 0; $j < $month_count; $j++) {
            $total += $jobPeriods[$start++]['Assigned'];
            $totaldone += $jobPeriods[$done++]['Done'];
            $totaldue += $jobPeriods[$due++]['Due'];
        }

        if ($i % 4 == 0) {
            $display .= '<div class="row">';
        }

        $display .= '<div class="col-xl-4 col-lg-4 col-md-3 col-sm-3 col-12">
                        <div class="pricing" style="background-color:#67b7d1 !important">
                            <div class="title" style="background-color: #172D44;">
                                <span style="color:#0dc0ff; font-size:21px;">' . $months[$i] . '</span><br><br>
                                <h2 style="color:white; font-size:16px"> Total Assigned: ' . $total . '</h2>
                                <h2 style="color:white; font-size:16px"> Total Due: ' . $totaldue . '</h2>
                                <h2 style="color:white; font-size:16px"> Total Done: ' . $totaldone . '</h2>
                            </div>
                            <div class="x_content">
                                <div class="pricing_features" style="background-color: #EDEDED !important; border: 3px solid lightgrey;">
                                    <br> 
                                    <p style="background-color:#172D44; color:white; text-align:center">Periods</p>
                                    <br>
                                    <div class="row">
                                        <ul class="list-unstyled" style="font-size: 15px !important; width: 600px; padding-left: 7px; display: flex; flex-wrap: wrap;">';

        for ($x = 0; $x < $month_count; $x++) {
            $display .= '<li style="display: inline-block; margin: 5px 10px;"> 
                            <button style="background-color:#172D44 !important;" type="button" class="btn btn-primary btn-">
                                <a href="index.php?page=all_teams_progress_period&p=' . $jobPeriods[$looper]['period'] . '&w=1">
                                    <i class="fa fa-calendar text-success">Started</i><br><br>
                                    <span class="text-success" style="font-weight:bold">' . $jobPeriods[$looper]['Assigned'] . '</span>
                                </a>
                                <br><hr style="border: 1px solid white">
                                <a href="index.php?page=all_teams_progress_period&p=' . $jobPeriods[$looper]['period'] . '&w=2">
                                    <i class="fa fa-calendar text-danger">Due</i><br><br>
                                    <span class="text-danger" style="font-weight:bold">' . $jobPeriods[$looper]['Due'] . '</span>
                                </a>
                                <br><hr style="border: 1px solid white">
                                <a href="index.php?page=all_teams_progress_period&p=' . $jobPeriods[$looper]['period'] . '&w=3">
                                    <i class="fa fa-calendar" style="color:#0dc0ff">Done</i><br><br>
                                    <span style="color:#0dc0ff; font-weight:bold">' . $jobPeriods[$looper]['Done'] . '</span>
                                </a>
                            </button>
                        </li>';
            $looper++;
        }

        $display .= '</ul></div></div></div></div></div>';

        if (($i + 1) % 4 == 0 || $i == 11) {
            $display .= '</div>';
        }
    }

    $display .= '</div>'; // Closing main div
    echo $display;
}

?>

