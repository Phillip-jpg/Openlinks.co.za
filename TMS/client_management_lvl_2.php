<?php include 'db_connect.php'; 

$CLIENT_ID = htmlspecialchars($_GET['CLIENT_ID']);


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
                    <label for="client-filter">Work Type:</label>
                    <select id="work-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $work_qry = $conn->query("SELECT task_name FROM task_list");
                        while ($work_row = $work_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $work_row['task_name']; ?>"><?php echo $work_row['task_name']; ?></option>
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
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>Work Type</th>
                                <th>Accounting Officer</th>
                            <th>Jobs with This Worktype</th>
                            <th>Jobs Responded To</th>
                            <th>Jobs Not Responded To</th>
                               <th>Work types done</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_work_managed = 0;
                        $total_work_started = 0;
                        $unmatched_count = 0;
                        $worktype_done = 0;
                        $query = $conn->query("
               SELECT 
    tl.task_name,
    tl.id,
    GROUP_CONCAT(pl.id) AS jobs,
    c.company_name AS CLIENT,
    GROUP_CONCAT(pl.status) AS status,
    COUNT(DISTINCT CASE WHEN pl.status = 'Done' THEN pl.id END) AS work_type_done,
    GROUP_CONCAT(DISTINCT CONCAT(lol.LINK, '|||', lol.LINK_NAME) SEPARATOR ', ') AS All_Links_With_Names,
    CONCAT(u.firstname, ' ', u.lastname) AS accounting_officer, -- Latest accounting officer
    COUNT(DISTINCT pl.id) AS Number_of_Clients_jobs,
    COUNT(DISTINCT sr.POST_ID) AS responsed_count,
    (COUNT(DISTINCT pl.id) - COUNT(DISTINCT sr.POST_ID)) AS Unmatched_Project_IDs_Count
FROM 
    yasccoza_tms_db.project_list pl
LEFT JOIN 
    yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN 
    yasccoza_tms_db.task_list tl ON FIND_IN_SET(tl.id, pl.task_ids) > 0
LEFT JOIN 
    yasccoza_tms_db.level_two_links lol ON pl.CLIENT_ID = lol.CLIENT_ID AND tl.id = lol.WorkType_ID
LEFT JOIN 
    yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = pl.id
LEFT JOIN 
    (
        -- Subquery to get the latest Date_assigned for each client and officer
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
WHERE 
    c.CLIENT_ID = $CLIENT_ID
GROUP BY 
    tl.task_name, c.company_name, u.firstname, u.lastname;

                        ");

                        while ($row = $query->fetch_assoc()):
                            $total_work_managed += $row['Number_of_Clients_jobs'];
                            $total_work_started += $row['responsed_count'];
                            $unmatched_count += $row['Unmatched_Project_IDs_Count'];
                            $worktype_done += $row['work_type_done'];
                            $client=$row['CLIENT'];
                        ?>
                            <tr>
                                <td><b><?php echo $row['task_name']; ?></b></td>
                                   <td><b><?php echo $row['accounting_officer']; ?></b></td>
                                <td><?php echo number_format($row['Number_of_Clients_jobs']); ?></td>
                                <td><?php echo number_format($row['responsed_count']); ?></td>
                                <td><?php echo number_format($row['Unmatched_Project_IDs_Count']); ?></td>
                                 <td><?php echo number_format($row['work_type_done']); ?></td>
                              <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        Action
                                    </button>
                                   <div class="dropdown-menu">
                                        <a class="dropdown-item view_project" href="./index.php?page=client_management_lvl_3&CLIENT_ID=<?php echo $CLIENT_ID; ?>&task_id=<?php echo $row['id']; ?>" data-id="<?php echo $row['CLIENT_ID']; ?>">Client Activities</a>
                                        
                                         <a class="dropdown-item view_project" href="./index.php?page=add_link_lvl2&CLIENT_ID=<?php echo $CLIENT_ID; ?>&task_id=<?php echo $row['id']; ?>" data-id="<?php echo $row['CLIENT_ID']; ?>">Add link</a>
                                         
                                         
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
                                <h2>Client Work Type Summary: <?php echo ( $client) ; ?></h2>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><b>Total number of Work Types: </b><?php echo number_format($query->num_rows); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Client work type orders : </strong><?php echo number_format($total_work_managed); ?></p>
                                       
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Client jobs not responded to: </strong><span style="color:red"><?php echo number_format($unmatched_count); ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Client jobs responsed to date: </strong><span style="color:green"><?php echo number_format($total_work_started); ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Work types orders done: </strong><span style="color:green"><?php echo number_format($worktype_done); ?></span></p>
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

        $('#work-filter').change(function() {
            filterTable();
        });

        function filterTable() {
            var selectedwork = $('#work-filter').val();
         

            dataTable.columns(0).search(selectedwork).draw();
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
