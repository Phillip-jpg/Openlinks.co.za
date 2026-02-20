<?php
if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
    
} else {
    echo "No dates provided.";
}
?>

<div class="col-lg-12">
     <div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
        <?php
        echo "Start Date: " . $start;
        echo "<br>";
        echo "End Date: " . $end;
        ?>
        <div class="card-header">
            <?php if($_SESSION['login_type'] != 3): ?>
                <!-- You can add content here if needed -->
            <?php endif; ?>
        </div>
        </div>
         </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="10%">
                        <col width="45%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                   <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>start_week</th>
                            <th>end_week</th>
                            <th>Done</th>
                            <th>Done On TIME</th>
                            <th>Member</th>
                            <th>Job_ID</th>
                            <th>Job_Name</th>
                            <th>Client</th>
                            <th>PM Manager</th>
                            <th>Work_Type</th>
                            <th>Activity</th>
                            <th>Activity Duration</th>
                            <th>Scorecard</th>
                            <th>Job Start Date</th>
                            <th>start date assigned</th>
                            <th>Actual Done Date</th>
                            <th>Job End Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                        $where = "";
                        if($_SESSION['login_type'] == 2){
                            $where = " where manager_id = '{$_SESSION['login_id']}' ";
                        }elseif($_SESSION['login_type'] == 3){
                            $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
                        }
                        
                         $end .= ' 23:59:59';
                         
                        $qry = $conn->query("SELECT
                                            subquery.period,
                                            subquery.month,
                                            subquery.start_week,
                                            subquery.end_week,
                                            subquery.Done,
                                            subquery.Done_On_TIME,
                                            subquery.Member,
                                            subquery.Project_Id,
                                            subquery.Job_Name,
                                            subquery.Client,
                                            subquery.Project_Manager,
                                            subquery.Work_Type,
                                            subquery.Activity,
                                            subquery.Activity_Duration,
                                            subquery.scorecard,
                                            subquery.Job_Start_Date,
                                            subquery.Job_End_Date,
                                            subquery.start_date_assigned,
                                            subquery.Actual_Done_Date,
                                            subquery.Estimated_Completion_Date
                                        FROM (
                                            SELECT
                                                CONCAT(u.firstname, ' ', u.lastname) AS Member,
                                                pl.name AS Job_Name,
                                                pl.id AS Project_Id,
                                                COALESCE(c.company_name, smme.Legal_name) AS Client,
                    
                                                YEARWEEK(ad.Done_Date, 1) AS period,
                                                MONTHNAME(ad.Done_Date) AS month,
                                        
                                                /* Monday of the week */
                                                DATE_SUB(ad.Done_Date, INTERVAL WEEKDAY(ad.Done_Date) DAY) AS start_week,
                                        
                                                /* Friday of the week */
                                                DATE_ADD(
                                                    DATE_SUB(ad.Done_Date, INTERVAL WEEKDAY(ad.Done_Date) DAY),
                                                    INTERVAL 4 DAY
                                                ) AS end_week,
                                        
                                                tl.task_name AS Work_Type,
                                                up.name AS Activity,
                                                pl.scorecard,
                                                pl.start_date AS Job_Start_Date,
                                                pl.end_date AS Job_End_Date,
                                                ad.start_date AS start_date_assigned,
                                                ad.Done_Date AS Actual_Done_Date,
                                                CONCAT(pm.firstname, ' ', pm.lastname) AS Project_Manager,
                                                ad.task_id,
                                                ad.activity_id,
                                                up.duration AS Activity_Duration,
                                        
                                                /* Done inside the same Mon–Fri week */
                                                CASE
                                                    WHEN ad.Done_Date >= DATE_SUB(ad.Done_Date, INTERVAL WEEKDAY(ad.Done_Date) DAY)
                                                     AND ad.Done_Date <= DATE_ADD(
                                                            DATE_SUB(ad.Done_Date, INTERVAL WEEKDAY(ad.Done_Date) DAY),
                                                            INTERVAL 4 DAY
                                                        )
                                                    THEN 'yes'
                                                    ELSE 'no'
                                                END AS Done,
                                        
                                                /* Done before or on estimated end date */
                                                CASE
                                                    WHEN ad.Done_Date <= pl.end_date THEN 'yes'
                                                    ELSE 'no'
                                                END AS Done_On_TIME,
                                        
                                                CASE
                                                    WHEN ad.Done_Date IS NOT NULL
                                                    THEN DATE_ADD(ad.Done_Date, INTERVAL up.duration DAY)
                                                    ELSE NULL
                                                END AS Estimated_Completion_Date
                                        
                                            FROM assigned_duties ad
                                            LEFT JOIN users u ON ad.user_id = u.id
                                            LEFT JOIN project_list pl ON ad.project_id = pl.id
                                            LEFT JOIN task_list tl ON ad.task_id = tl.id
                                            LEFT JOIN user_productivity up ON ad.activity_id = up.id
                                            LEFT JOIN yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID
                                            LEFT JOIN users pm ON ad.manager_id = pm.id
                                            LEFT JOIN yasccoza_openlink_market.client c ON ad.CLIENT_ID = c.CLIENT_ID
                                        
                                            WHERE
                                                ad.Done_Date BETWEEN '$start' AND '$end'
                                                AND pl.manager_id = '{$_SESSION['login_id']}'
                                        
                                            GROUP BY
                                                period,
                                                u.id,
                                                Member,
                                                Job_Name,
                                                Project_Id,
                                                Client,
                                                Work_Type,
                                                Activity,
                                                Activity_Duration,
                                                scorecard,
                                                Job_Start_Date,
                                                start_date_assigned,
                                                Actual_Done_Date,
                                                Project_Manager,
                                                ad.task_id,
                                                ad.activity_id
                                        ) AS subquery
                                        WHERE
                                            subquery.Done = 'yes'
                                        ORDER BY
                                            subquery.period;
                                        ");
                                    while($row = $qry->fetch_assoc()):
                            $words = explode(' ', $row['Job_Name']);
                            $shortenedJobName = count($words) >= 2 ? implode(' ', array_slice($words, 0, 5)) . '...' : $row['Job_Name'];
                        ?>
                        
                        <tr data-office="<?php echo $row['OFFICE']; ?>" data-client="<?php echo $row['CLIENT']; ?>">
                            <td class="text-center" style="color:red; font-weight:bold"><?php echo ($row['period']) ?></td>
                            <td style="color:green"><p><b><?php echo ucwords($row['month']) ?></b></p></td>
                            <td class="text-center"><?php echo ($row['start_week']) ?></td>
                            <td class="text-center"><?php echo ($row['end_week']) ?></td>
                            <td><p><b><?php echo ucwords($row['Done']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Done_On_TIME']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Member']) ?></b></p></td>
                            <td class="text-center"><?php echo ($row['Project_Id']) ?></td>
                            <td><p><b><?php echo($shortenedJobName) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Client']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Project_Manager']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Work_Type']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Activity']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Activity_Duration']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['scorecard']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Job_Start_Date']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['start_date_assigned']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Actual_Done_Date']) ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Job_End_Date']) ?></b></p></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Action</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['Job_ID'] ?>" data-id="<?php echo $row['Job_ID'] ?>">View</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
<style>
    .table-responsive {
        overflow-x: auto;
    }
    table p {
        margin: unset !important;
    }
    table td {
        vertical-align: middle !important;
    }
</style>



<script>
    $(document).ready(function(){
        var dataTable = $('#list').DataTable();

        $('#office-filter').change(function(){
            filterTable();
        });

        $('#client-filter').change(function(){
            filterTable();
        });
        
        $('#work-filter').change(function(){
            filterTable();
        });

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

    function delete_project(id){
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_project',
            method: 'POST',
            data: {id: id},
            success: function(resp){
                if(resp == 1){
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
