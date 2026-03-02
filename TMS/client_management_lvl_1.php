<?php include 'db_connect.php'; ?>

<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Higher Level Client Management</h4>
            <?php if ($_SESSION['login_type'] != 3): ?>
                <!-- Additional functionality for non-client users -->
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="form-row mb-3">
                <div class="col-md-3">
                    <label for="client-filter">Filter by Client:</label>
                    <select id="client-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $work_qry = $conn->query("SELECT DISTINCT company_name FROM yasccoza_openlink_market.client");
                        while ($work_row = $work_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $work_row['company_name']; ?>"><?php echo $work_row['company_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="month-filter">Filter by Month of Creation:</label>
                    <select id="month-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $month = $conn->query("SELECT DISTINCT MONTH(c.created) as month, MONTHNAME(c.created) as month_name FROM yasccoza_openlink_market.client c");
                        while ($month_row = $month->fetch_assoc()):
                        ?>
                            <option value="<?php echo $month_row['month_name']; ?>"><?php echo $month_row['month_name']; ?></option>
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
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                             <th>##</th>
                            <th>Client</th>
                            <th>Accounting Officer</th>
                             <th>Date of Officer Assignment</th>
                            <th>Creation</th>
                            <th>Month</th>
                            <th>Number of Jobs</th>
                            <th>Work Started</th>
                            <th>Total Work Not Started</th>
                            <th>Work Types</th>
                            <th>Action</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_clients = 0;
                        $total_work_managed = 0;
                        $total_work_started = 0;
                        $unmatched_count = 0;
                         $list = 0;
                         
            if($_SESSION['login_type']==2){
                
                $query = $conn->query("
                    SELECT 
    c.company_name AS CLIENT,
    CONCAT(u.firstname, ' ', u.lastname) AS accounting_officer,
    c.CLIENT_ID,
    latest_assignment.Last_Date_Assigned,
    COUNT(DISTINCT pl.id) AS Number_of_Clients_jobs,
    GROUP_CONCAT(DISTINCT CONCAT(lol.LINK, '|||', lol.LINK_NAME) SEPARATOR ', ') AS All_Links_With_Names,
    GROUP_CONCAT(DISTINCT pl.id SEPARATOR ', ') AS Project_IDs,
    GROUP_CONCAT(DISTINCT sr.POST_ID SEPARATOR ', ') AS Project_IDs_in_response,
    GROUP_CONCAT(DISTINCT CASE WHEN sr.POST_ID IS NULL THEN pl.id END SEPARATOR ', ') AS Unmatched_Project_IDs,
    COUNT(DISTINCT CASE WHEN sr.POST_ID IS NULL THEN pl.id END) AS Unmatched_Project_IDs_Count,
    DATE_FORMAT(c.created, '%d-%m-%Y') AS Month,
    MONTHNAME(c.created) AS Month_Created,
    COUNT(sr.POST_ID) AS responsed_count,
    COUNT(DISTINCT sr.POST_ID) AS responsed_count_date,
    GROUP_CONCAT(DISTINCT pl.task_ids) AS unique_task_ids,
    COUNT(*) OVER() AS total_rows
FROM yasccoza_tms_db.project_list AS pl
LEFT JOIN yasccoza_openlink_market.client AS c 
    ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN yasccoza_tms_db.level_one_links AS lol 
    ON pl.CLIENT_ID = lol.CLIENT_ID
LEFT JOIN yasccoza_openlink_market.scorecard_response AS sr 
    ON sr.POST_ID = pl.id
LEFT JOIN (
    SELECT 
        ao.CLIENT_ID,
        ao.Accounting_Officer_ID,
        MAX(ao.Date_assigned) AS Last_Date_Assigned
    FROM yasccoza_tms_db.accountng_officers AS ao
    GROUP BY ao.CLIENT_ID
) AS latest_assignment 
    ON pl.CLIENT_ID = latest_assignment.CLIENT_ID
LEFT JOIN yasccoza_tms_db.users AS u 
    ON latest_assignment.Accounting_Officer_ID = u.id
WHERE c.creator_id = {$_SESSION['login_id']}
GROUP BY 
    c.company_name, c.CLIENT_ID, u.firstname, u.lastname, latest_assignment.Last_Date_Assigned
ORDER BY 
    latest_assignment.Last_Date_Assigned DESC;


                        ");
                
                
            }elseif($_SESSION['login_type']==3){
                
                
            }else{
                
                $query = $conn->query("
                    SELECT 
    c.company_name AS CLIENT,
    CONCAT(u.firstname, ' ', u.lastname) AS accounting_officer, -- Accounting officer for the latest assignment
    c.CLIENT_ID AS CLIENT_ID,
    latest_assignment.Last_Date_Assigned,
    COUNT(DISTINCT pl.id) AS Number_of_Clients_jobs,
   GROUP_CONCAT(DISTINCT CONCAT(lol.LINK, '|||', lol.LINK_NAME) SEPARATOR ', ') AS All_Links_With_Names,
    GROUP_CONCAT(DISTINCT pl.id SEPARATOR ', ') AS Project_IDs,
    GROUP_CONCAT(DISTINCT sr.POST_ID SEPARATOR ', ') AS Project_IDs_in_response,
    GROUP_CONCAT(DISTINCT CASE WHEN sr.POST_ID IS NULL THEN pl.id END SEPARATOR ', ') AS Unmatched_Project_IDs,
    COUNT(DISTINCT CASE WHEN sr.POST_ID IS NULL THEN pl.id END) AS Unmatched_Project_IDs_Count,
    DATE_FORMAT(c.created, '%d-%m-%Y') AS Month,
    MONTHNAME(c.created) AS Month_Created,
    COUNT(sr.POST_ID) AS responsed_count,
    COUNT(DISTINCT(sr.POST_ID)) AS responsed_count_date,
    GROUP_CONCAT(DISTINCT pl.task_ids) AS unique_task_ids, -- Optimized unique task count in SQL
    COUNT(*) OVER() AS total_rows
FROM 
    yasccoza_tms_db.project_list pl
LEFT JOIN 
    yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN 
    yasccoza_tms_db.level_one_links lol ON pl.CLIENT_ID = lol.CLIENT_ID
LEFT JOIN 
    yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = pl.id
LEFT JOIN 
    (
        -- Subquery to get the latest Date_assigned and corresponding Accounting_Officer_ID for each client
        SELECT 
            ao.CLIENT_ID,
            ao.Accounting_Officer_ID,
            MAX(ao.Date_assigned) AS Last_Date_Assigned
        FROM 
            yasccoza_tms_db.accountng_officers ao
        GROUP BY 
            ao.CLIENT_ID
    ) latest_assignment ON pl.CLIENT_ID = latest_assignment.CLIENT_ID
LEFT JOIN 
    yasccoza_tms_db.users u ON latest_assignment.Accounting_Officer_ID = u.id 
GROUP BY 
    c.company_name, c.CLIENT_ID, u.firstname, u.lastname, latest_assignment.Last_Date_Assigned
ORDER BY 
    latest_assignment.Last_Date_Assigned DESC;

                        ");
            }

                        

                        while ($row = $query->fetch_assoc()):
                            
                            $task_ids=$row['unique_task_ids'];
                            $task_ids = $row['unique_task_ids'];
                            $task_ids_array = explode(',', $task_ids);
                            $unique_task_ids = array_unique($task_ids_array);
                            $unique_count = count($unique_task_ids);
                            $total_work_managed += $row['Number_of_Clients_jobs'];
                            $total_work_started += $row['responsed_count_date'];
                            $total_clients += $row['CLIENT_ID'];
                            $unmatched_count += $row['Unmatched_Project_IDs_Count'];
                            $total_rows = $row['total_rows'];
                            $list++;
                        ?>
                            <tr>
                                 <td><?php echo $list ?></td>
                                <td>
                                    <b>
                                        <?php 
                                        echo !empty($row['CLIENT']) 
                                            ? ucwords($row['CLIENT']) 
                                            : '<span style="color: red;">Not assigned during creation</span>'; 
                                        ?>
                                    </b>
                                </td>
                                    <td><?php echo $row['accounting_officer']; ?></td>
                                          <td><?php echo date('d-m-Y', strtotime($row['Last_Date_Assigned'])); ?></td>

                                <td><?php echo $row['Month']; ?></td>
                                <td><?php echo $row['Month_Created']; ?></td>
                                <td><?php echo number_format($row['Number_of_Clients_jobs']); ?></td>
                                <td><?php echo number_format($row['responsed_count_date']); ?></td>
                                <td><?php echo number_format($row['Unmatched_Project_IDs_Count']); ?></td>
                                <td><?php echo $unique_count ; ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        Action
                                    </button>
                                   <div class="dropdown-menu">
                                       <a class="dropdown-item view_project" href="./index.php?page=client_management_lvl_2&CLIENT_ID=<?php echo $row['CLIENT_ID']; ?>">Work Type Summary</a>
                                     
                                        <a class="dropdown-item view_project" href="./index.php?page=add_link<?php echo $CLIENT_ID; ?>&CLIENT_ID=<?php echo $row['CLIENT_ID']; ?>" data-id="<?php echo $row['CLIENT_ID']; ?>">Add link</a>
                                       
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
                            <h2>Client Management Summary</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><b>Total Clients: </b><?php echo number_format($total_rows); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Managed Work: </strong><?php echo number_format($total_work_managed); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p style="font-weight:bold">Total Work Not Started: <strong style="color:red"><?php echo number_format($unmatched_count); ?></strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p style="font-weight:bold">Work Started: <strong style="color:green"><?php echo number_format($total_work_started); ?></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </table>

                 <!-- End of summary card -->
            </div> <!-- End of table-responsive -->
        </div> <!-- End of card-body -->
    </div> <!-- End of card -->
</div> <!-- End of col-lg-12 -->

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
    // Initialize the DataTable
    var dataTable = $('#list').DataTable();

    // Trigger the filter when client or month filter is changed
    $('#client-filter, #month-filter').change(function() {
        filterTable();
    });

    // Function to filter the table based on selected client and month
    function filterTable() {
        var selectedClient = $('#client-filter').val();
        var selectedMonth = $('#month-filter').val();

        // Apply both filters and draw the table once
        dataTable
            .columns(0).search(selectedClient)  // Filter by client (column 1)
            .columns(4).search(selectedMonth)   // Filter by month (column 3)
            .draw();
    }

    // Delete confirmation logic for projects
    $('.delete_project').click(function() {
        var projectId = $(this).attr('data-id');
        _conf("Are you sure you want to delete this job?", "delete_project", [projectId]);
    });
});

// Function to handle project deletion
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
            } else {
                alert_toast("Failed to delete data", 'danger');
            }
        },
        error: function() {
            alert_toast("An error occurred while deleting the project", 'danger');
        }
    });
}
</script>
