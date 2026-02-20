<?php

$login_id = $_SESSION['login_id'];

if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
    $id = htmlspecialchars($_GET['id']);
} else {
    echo "No dates provided.";
    exit;
}

?>

<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header bg-primary text-white">
            <h3>Project Details</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
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
                    <thead class="thead-light">
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>Job ID</th>
                            <th>Individual Done</th>
                            <th>Job Start Date</th>
                            <th>Job End Date</th>
                            <th>Actual Date Job Ended</th>
                            <th>PM Manager</th>
                             <th>Team</th>
                            <th>Member</th>
                            <th>Activity</th>
                            <th>Rate</th>
                            <th>Quantity Done</th>
                             <th>Total Claimable</th>
                            <th>Claim Status</th>
                            <th>Claim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_claims = 0;

                        $qry = $conn->query("SELECT DISTINCT
                            wwp.period,
                            wwp.month,
                            pl.id,
                            pl.start_date as job_start_date,
                            pl.end_date as job_end_date,
                            pl.Job_Done as Job_done,
                            pl.name AS Job_Name,
                            ad.user_id,
                            ad.manager_id,
                            ad.pm_quantities,
                            ad.activity_id,
                            ts.team_id,
                            ts.team_name,
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
                            pl.JOB_TYPE, 
                            up.duration AS Activity_Duration,
                            CASE WHEN ad.Done_Date >= wwp.start_week AND ad.Done_Date <= wwp.end_week THEN 'yes' ELSE 'no' END AS Done,
                            CASE WHEN ad.Done_Date <= ad.end_date THEN 'yes' ELSE 'no' END AS Done_On_TIME,
                            CASE 
                                WHEN ad.Done_Date IS NOT NULL THEN 'yes'
                                ELSE 'no'
                            END AS activity_done,
                            CASE
                                WHEN (WEEKDAY(ad.start_date) + up.duration) < 5 THEN DATE_ADD(ad.start_date, INTERVAL up.duration DAY)
                                WHEN (WEEKDAY(ad.start_date) + up.duration) >= 5 THEN DATE_ADD(ad.start_date, INTERVAL up.duration + (2 * ((WEEKDAY(ad.start_date) + up.duration) DIV 5)) DAY)
                            END AS Estimated_Completion_Date
                        FROM 
                            assigned_duties ad
                        JOIN 
                            project_list pl ON pl.id = ad.project_id
                        JOIN 
                            users u ON u.id = ad.manager_id
                        JOIN 
                            user_productivity up ON up.id = ad.activity_id
                        JOIN team_schedule ts ON
                            ts.team_id = pl.team_ids
                        LEFT JOIN 
                            yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID
                        LEFT JOIN
                            yasccoza_openlink_market.client c ON ad.CLIENT_ID = c.CLIENT_ID
                        LEFT JOIN
                            task_list tl ON ad.task_id = tl.id
                        LEFT JOIN
                            working_week_periods wwp ON (ad.Done_Date >= wwp.start_week AND ad.Done_Date <= wwp.end_week)
                        JOIN 
                            users u1 ON u1.id = ad.user_id
                        WHERE 
                            ad.project_id = $id");

                        while ($row = $qry->fetch_assoc()) {
                            $total_claims += $row['rate']*$row['pm_quantities'];
                        ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?php echo empty($row['period']) ? '---' : $row['period']; ?></td>
                                <td class="text-center font-weight-bold"><?php echo empty($row['month']) ? '---' : $row['month']; ?></td>
                                <td class="text-center" style="color:#007bff; font-weight:bold"><?php echo ($row['id']) ?></td>
                                <td><p><b style="color: <?php echo strtolower($row['activity_done']) == 'yes' ? 'green' : 'red'; ?>"><?php echo ucwords($row['activity_done']); ?></b></p></td>
                                <td><p><b><?php echo ucwords($row['job_start_date']) ?></b></p></td>
                                <td class="text-center font-weight-bold"><?php echo ($row['job_end_date']) ?></td>
                                <td><p><b><?php echo empty($row['Job_done']) ? '---' : ucwords($row['Job_done']); ?></b></p></td>
                                <td><p><b><?php echo ucwords($row['manager_name']) ?></b></p></td>
                                 <td><p><b><?php echo ucwords($row['team_name']) ?></b></p></td>
                                <td><p><b><?php echo ucwords($row['member']) ?></b></p></td>
                                <td><p><b><?php echo ucwords($row['name']) ?></b></p></td>
                                <td><p style="color:blue">R <b><?php echo $row['rate']; ?></b></p></td>
                                 <td><p style="color:black"><b><?php echo $row['pm_quantities']; ?></b></p></td>
                                 <td><p style="color:red">R <b><?php echo $row['rate']*$row['pm_quantities']; ?></b></p></td>
                                <td>
                                    <?php if ($row['claim_status'] == 0) { ?>
                                        <span class="badge badge-warning">Unprocessed</span>
                                    <?php } elseif ($row['claim_status'] == 1) { ?>
                                        <span class="badge badge-success">Approved</span>
                                    <?php } elseif ($row['claim_status'] == 2) { ?>
                                        <span class="badge badge-danger">Rejected</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Action</button>
                                    <div class="dropdown-menu">
                                        <form id="assign-form" method="post" action="./index.php?page=process_claim">
                                            <!-- Hidden fields for claim details -->
                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
                                    <input type="hidden" name="job_id" value="<?php echo $row['id'] ?>">
                                    <input type="hidden" name="period" value="<?php echo $row['period'] ?>">
                                    <input type="hidden" name="activity_id" value="<?php echo $row['activity_id'] ?>">
                                    <input type="hidden" name="pm_id" value="<?php echo $row['manager_id'] ?>">
                                    <input type="hidden" name="worktype_id" value="<?php echo $row['task_id'] ?>">
                                    <input type="hidden" name="pm_quantities" value="<?php echo $row['pm_quantities'] ?>">
                                     <input type="hidden" name="team_id" value="<?php echo $row['team_id'] ?>">
                                     <input type="hidden" name="team_name" value="<?php echo $row['team_name'] ?>">
                                    <input type="hidden" name="login_id" value="<?php echo $login_id ?>">
                                    <input type="hidden" name="month" value="<?php echo ucwords($row['month']) ?>">
                                    <input type="hidden" name="job_start_date" value="<?php echo ucwords($row['job_start_date']) ?>">
                                    <input type="hidden" name="job_end_date" value="<?php echo ucwords($row['job_end_date']) ?>">
                                    <input type="hidden" name="actual_job_done" value="<?php echo ucwords($row['Job_done']) ?>">
                                    <input type="hidden" name="client" value="<?php echo ucwords($row['Client']) ?>">
                                    <input type="hidden" name="member" value="<?php echo ucwords($row['member']) ?>">
                                    <input type="hidden" name="manager" value="<?php echo ucwords($row['manager_name']) ?>">
                                    <input type="hidden" name="Activity" value="<?php echo ucwords($row['name']) ?>">
                                    <input type="hidden" name="done" value="<?php echo ucwords($row['Done_On_TIME']) ?>">
                                    <input type="hidden" name="estimated_done" value="<?php echo $row['Estimated_Completion_Date'] ?>">
                                    <input type="hidden" name="actual_done" value="<?php echo $row['Done_Date'] ?>">
                                    <input type="hidden" name="jobname" value="<?php echo ucwords($row['Job_Name']) ?>">
                                    <input type="hidden" name="worktype" value="<?php echo ucwords($row['Work_Type']) ?>">
                                    <input type="hidden" name="rate" value="<?php echo $row['rate'] ?>">
                                           <input type="hidden" name="start" value="<?php echo htmlspecialchars($start); ?>">
<input type="hidden" name="end" value="<?php echo htmlspecialchars($end); ?>">
<input type="hidden" name="claim_status" value="<?php echo htmlspecialchars($row['claim_status']); ?>">
                                            
                                            
                                            <?php if ($row['claim_status'] == 1 || $row['claim_status'] == 2) { ?>
                                                <button class="dropdown-item" type="submit" style="border-radius: 5px;"><span style="color:green">View</span></button>
                                            <?php } elseif ($row['claim_status'] == 0 && $row['activity_done'] == 'yes' && $row['Job_done']!=null) { ?>
                                                <button class="dropdown-item" type="submit" style="border-radius: 5px;"><span style="color:red">Process claim</span></button>
                                                
                                                <?php if($_SESSION['login_type'] ==1): ?>
                                                <a class="dropdown-item" href="./index.php?page=edit_quantities&project_id=<?php echo $row['id'] ?>&user_id=<?php echo $row['user_id'] ?>&activity_id=<?php echo $row['activity_id'] ?>&task_id=<?php echo $row['task_id'] ?>&start=<?php echo $start ?>&end=<?php echo $end ?>">Edit Quantity</a>
                                                <?php endif; ?>
                                            <?php } ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <p style="font-weight:bold; font-size:20px;">Total Claims: <span style="color:blue; font-weight:bold; font-size:20px;">R <?php echo $total_claims; ?></span></p>
            </div>
        </div>
        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button"
  onclick="location.href='index.php?page=claims&start=<?php echo $start ?>&end=<?php echo $end ?>'">
  Back
</button>
    </div>
</div>

<!-- CSS and Scripts -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

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

<script>
    $(document).ready(function(){
        var dataTable = $('#list').DataTable();

        function filterTable() {
            var selectedOffice = $('#office-filter').val();
            var selectedClient = $('#client-filter').val();
            var selectedWorkType = $('#work-filter').val();

            dataTable.columns(6).search(selectedOffice).columns(7).search(selectedClient).columns(8).search(selectedWorkType).draw();
        }

        $('.delete_project').click(function(){
            _conf("Are you sure to delete this job?", "delete_project", [$(this).attr('data-id')]);
        });
    });

    function delete_project(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_project',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>

