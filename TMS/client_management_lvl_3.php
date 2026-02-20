<?php include 'db_connect.php'; 

$CLIENT_ID = htmlspecialchars($_GET['CLIENT_ID']);
$task_id = htmlspecialchars($_GET['task_id']);

?>



<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Second Level Client Management</h4>
            <?php if ($_SESSION['login_type'] != 3): ?>
                <!-- Additional functionality for non-client users -->
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="form-row mb-3" style="margin:auto">
                <div class="col-md-3">
                    <label for="client-filter">Project Manager:</label>
                    <select id="manager-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                            $manager_qry = $conn->query("SELECT CONCAT(u.firstname, ' ', u.lastname) AS pm_manager
                                    FROM users u
                                    WHERE type = 2;
                                    ");
                        while ($manager_row = $manager_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $manager_row['pm_manager']; ?>"><?php echo $manager_row['pm_manager']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
 <div class="col-md-3">
                    <label for="month-filter">Filter by Month of Creation:</label>
                    <select id="month-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $month = $conn->query("SELECT DISTINCT month as month_name FROM working_week_periods");
                        while ($month_row = $month->fetch_assoc()):
                        ?>
                            <option value="<?php echo $month_row['month_name']; ?>"><?php echo $month_row['month_name']; ?></option>
                            <!--<option value="September">September</option>-->
                            <!-- <option value="October">October</option>-->
                            <!--  <option value="November">November</option>-->
                            <!--   <option value="December">December</option>-->
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="client-filter">Status:</label>
                    <select id="status-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $work_qry = $conn->query("SELECT DISTINCT pl.status FROM project_list pl");
                        while ($work_row = $work_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $work_row['status']; ?>"><?php echo $work_row['status']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <br>
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                         <col width="10%">
                         <col width="10%">
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>#</th>
                            <th>Job_ID</th>
                            <th>Job title</th>
                             <th>PM Responsible</th>
                                  <th>members</th>
                                  <th>Respondent</th>
                            <th>Month</th>
                             <th>Start date</th>
                            <th>Closing date</th>
                            <th>Done date</th>
                            <th>Job Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $total_work_managed = 0;
                        $total_work_started = 0;
                        $matched_count = 0;
                         $list = 0;

                        $query = $conn->query("
            SELECT 
    tl.task_name,
    tl.id AS task_id,
    pl.id,
    pl.start_date,
    MONTHNAME(pl.start_date) AS month,
    pl.end_date,
    pl.Job_Done,
    pl.status,
    pl.name,
    c.company_name AS client,
    CONCAT(u.firstname, ' ', u.lastname) AS accounting_officer,
    GROUP_CONCAT(DISTINCT CONCAT(lol.LINK, '|||', lol.LINK_NAME) SEPARATOR ', ') AS All_Links_With_Names,
    GROUP_CONCAT(DISTINCT CONCAT(u2.firstname, ' ', u2.lastname) SEPARATOR ', ') AS member, 
    CONCAT(u1.firstname, ' ', u1.lastname) AS manager,
    COUNT(DISTINCT sr.POST_ID) AS responsed_count,
    (COUNT(DISTINCT pl.id) - COUNT(DISTINCT sr.POST_ID)) AS Unmatched_Project_IDs_Count
FROM 
    yasccoza_tms_db.project_list pl
LEFT JOIN 
    yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN 
    yasccoza_tms_db.task_list tl ON FIND_IN_SET(tl.id, pl.task_ids) > 0
LEFT JOIN 
    yasccoza_tms_db.level_three_links lol ON pl.CLIENT_ID = lol.CLIENT_ID AND tl.id = lol.WorkType_ID AND pl.id =lol.JOB_ID
 LEFT JOIN
    yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = pl.id
LEFT JOIN 
    (
        SELECT 
            ao.CLIENT_ID,
            ao.Accounting_Officer_ID,
            MAX(ao.Date_assigned) AS Last_Date_Assigned
        FROM 
            yasccoza_tms_db.accountng_officers ao
        GROUP BY 
            ao.CLIENT_ID
    ) latest_ao ON pl.CLIENT_ID = latest_ao.CLIENT_ID
LEFT JOIN 
    yasccoza_tms_db.users u ON latest_ao.Accounting_Officer_ID = u.id
LEFT JOIN 
    yasccoza_tms_db.assigned_duties ad ON ad.task_id = tl.id AND ad.project_id = pl.id
LEFT JOIN 
    yasccoza_tms_db.users u1 ON pl.manager_id = u1.id
LEFT JOIN 
    yasccoza_tms_db.users u2 ON ad.user_id = u2.id
WHERE 
    c.CLIENT_ID = $CLIENT_ID
    AND tl.id =$task_id
GROUP BY 
    pl.id, tl.task_name, pl.start_date, pl.end_date, pl.Job_Done, pl.status, pl.name, c.company_name, u.firstname, u.lastname, u1.firstname, u1.lastname  
ORDER BY 
    pl.id DESC;
                        ");

                        while ($row = $query->fetch_assoc()):
                            
                             $accounting_officer=$row['accounting_officer'];
                           $total_work_managed += ($row['status'] == "Done") ? 1 : 0;
                              $client =$row['client'];
                            $task_name=$row['task_name'];
                            	$words = explode(' ', $row['name']);
						$shortenedJobName = (count($words) >= 2) ? implode(' ', array_slice($words, 0, 5)) . '...' : $row['name'];
						 $matched_count+=$row['responsed_count'];
						 $list++;
                        ?>
                            <tr>
                                <td><?php echo $list ?></td>
                                <td><b><?php echo $row['id']; ?></b></td>
                                   <td><b><?php echo $shortenedJobName ?></b></td>
                                     <td><?php echo ($row['manager']); ?></td>
                                 
                                <td><?php echo !empty($row['member']) ? $row['member'] : 'No member assigned'; ?></td>

                                    <td><?php echo ($row['responsed_count']); ?></td>
                                <td><?php echo ($row['month']); ?></td>
                                 <td><?php echo ($row['start_date']); ?></td>
                                 <td><?php echo ($row['end_date']); ?></td>
                                 <td><?php echo !empty($row['Job_Done']) ? $row['Job_Done'] : 'Not Done'; ?></td>

                                  <td><?php echo ($row['status']); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['id']; ?>" data-id="<?php echo $row['POST_ID']; ?>">View</a>
                                        
                                        
                                          <a class="dropdown-item view_project" href="./index.php?page=add_link_lvl3&CLIENT_ID=<?php echo $CLIENT_ID; ?>&task_id=<?php echo $row['task_id']; ?>&job_id=<?php echo $row['id']; ?>">Add link</a>
                                          
                                            <?php
                                        if (!empty($row['All_Links_With_Names'])) {
                                            $combinedArray = explode(', ', $row['All_Links_With_Names']);
                                        
                                            foreach ($combinedArray as $combined) {
                                                // Split each pair back into its components
                                                list($link, $linkname) = explode('|||', $combined);
                                                echo '<a class="dropdown-item view_project" style="color:#007bff" href="' . htmlspecialchars($link) . '" target="_blank">' . htmlspecialchars($linkname) . '</a><br>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <div class="container mt-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h2>Client Activties : <?php echo $task_name."--".$client?></h2>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><b>Total number of Jobs: </b><?php echo number_format($query->num_rows); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Client work order Done: </strong><?php echo number_format($total_work_managed); ?></p>
                                       
                                    </div>
                                      <div class="col-md-6">
                                        <p><strong>Total Client response to date: </strong><span style="color:green"><?php echo number_format($matched_count); ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Accounting Officer: </strong><span style="color:blue"><?php echo ($accounting_officer); ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </table>
            </div>
        </div>
    </div>
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
    $(document).ready(function() {
        var dataTable = $('#list').DataTable();

         // Trigger the filter when client or month filter is changed
   $('#month-filter, #manager-filter, #status-filter').change(function() {
    filterTable();
});

// Function to filter the table based on selected manager, month, and status
function filterTable() {
    var selectedManager = $('#manager-filter').val();
    var selectedMonth = $('#month-filter').val();
    var selectedStatus = $('#status-filter').val();
    
    // Apply filters and draw the table
    dataTable
        .columns(3).search(selectedManager)  // Filter by manager (column 2)
        .columns(6).search(selectedMonth)     // Filter by month (column 5)
        .columns(10).search(selectedStatus)    // Filter by status (column 9)
        .draw();
}


        $('.delete_project').click(function() {
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
