<?php include'db_connect.php'; ?>

<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Job Report</h4>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="form-row mb-3">
                <!-- Office Filter -->
                <div class="col-md-3">
                    <label for="office-filter">Filter by Office:</label>
                    <select id="office-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $office_qry = $conn->query("SELECT DISTINCT nda.office 
FROM yasccoza_openlink_association_db.industry nda 
JOIN project_list pl ON nda.INDUSTRY_ID = pl.OFFICE_ID 
WHERE nda.INDUSTRY_ID != 0");
                        while($office_row = $office_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $office_row['office']; ?>"><?php echo $office_row['office']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Client Filter -->
                <div class="col-md-3">
                    <label for="client-filter">Filter by Client:</label>
                    <select id="client-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $client_qry = $conn->query("SELECT DISTINCT COALESCE(c.company_name, smme.Legal_name) as company_name
FROM project_list pl
LEFT JOIN yasccoza_openlink_market.client c ON c.CLIENT_ID = pl.CLIENT_ID
LEFT JOIN yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID");
                        while($client_row = $client_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $client_row['company_name']; ?>"><?php echo $client_row['company_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Month Filter -->
                <div class="col-md-3">
                    <label for="month-filter">Filter by Month:</label>
                    <select id="month-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $month = $conn->query("SELECT DISTINCT month FROM working_week_periods");
                        while($month_row = $month->fetch_assoc()):
                        ?>
                            <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Work Type Filter -->
                <div class="col-md-3">
                    <label for="work-filter">Filter by Work Type:</label>
                    <select id="work-filter" class="form-control">
                        <option value="">All</option>
                        <?php
                        $work_qry = $conn->query("SELECT task_name FROM task_list");
                        while($work_row = $work_qry->fetch_assoc()):
                        ?>
                            <option value="<?php echo $work_row['task_name']; ?>"><?php echo $work_row['task_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped table-condensed" id="list">
                    <colgroup>
                        <col width="10%">
                        <col width="10%">
                        <col width="15%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>Job ID</th>
                            <th>What Happened</th>
                            <th>Completion Status</th>
                            <th>Job Name</th>
                            <th>Office</th>
                            <th>Client</th>
                            <th>Work Type</th>
                            <th>Manager</th>
                            <th>Assigned</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qry = $conn->query("WITH RECURSIVE split_ids AS (
                            SELECT pl.id AS job_id, TRIM(SUBSTRING_INDEX(pl.task_ids, ',', 1)) AS task_id,
                            SUBSTRING(pl.task_ids, LENGTH(SUBSTRING_INDEX(pl.task_ids, ',', 1)) + 2) AS rest_ids
                            FROM project_list pl
                            UNION ALL
                            SELECT job_id, TRIM(SUBSTRING_INDEX(rest_ids, ',', 1)),
                            SUBSTRING(rest_ids, LENGTH(SUBSTRING_INDEX(rest_ids, ',', 1)) + 2) 
                            FROM split_ids WHERE rest_ids <> ''
                        )
                        SELECT
                            wwp.period, wwp.month, pl.id as Job_ID, nda.office as OFFICE,
                            CONCAT(u.firstname, ' ', u.lastname) AS Job_Manager, COALESCE(c.company_name, smme.Legal_name) as CLIENT,
                            wt.task_name, pl.status, pl.assigned as Assigned_Resources,
                            MIN(pl.start_date) AS start_date, MAX(pl.end_date) AS end_date, pl.name as Job_Name,
                            CASE WHEN pl.Job_Done >= wwp.start_week AND pl.Job_Done <= wwp.end_week THEN 'Finished' ELSE 'In Progress' END AS What_Happened
                        FROM
                            project_list pl
                        JOIN working_week_periods wwp ON (pl.date_created >= wwp.start_week AND pl.date_created <= wwp.end_week)
                        LEFT JOIN users u ON pl.manager_id = u.id
                        LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
                        LEFT JOIN yasccoza_openlink_smmes.register smme ON pl.CLIENT_ID = smme.SMME_ID
                        LEFT JOIN yasccoza_openlink_association_db.industry nda ON nda.INDUSTRY_ID = pl.OFFICE_ID
                        LEFT JOIN split_ids si ON si.job_id = pl.id
                        LEFT JOIN yasccoza_tms_db.task_list wt ON wt.id = si.task_id
                        GROUP BY wwp.start_week, wwp.end_week, wwp.period, pl.name, pl.status, pl.manager_id, pl.id, wt.task_name
                        ORDER BY wwp.start_week, wwp.end_week, wwp.period;");
                        
                        while($row = $qry->fetch_assoc()):
                            $shortenedJobName = (count(explode(' ', $row['Job_Name'])) >= 2) ? implode(' ', array_slice(explode(' ', $row['Job_Name']), 0, 5)) . '...' : $row['Job_Name'];
                        ?>
                        <tr>
                            <td class="text-center text-danger font-weight-bold"><?php echo $row['period'] ?></td>
                            <td class="text-success"><b><?php echo ucwords($row['month']) ?></b></td>
                            <td class="text-primary font-weight-bold"><?php echo $row['Job_ID'] ?></td>
                            <td><b><?php echo ucwords($row['What_Happened']) ?></b></td>
                            <td><b><?php echo ucwords($row['status'] == 'Done' ? 'Finished' : 'In Progress') ?></b></td>
                            <td><b><?php echo ucwords($shortenedJobName) ?></b></td>
                            <td><b><?php echo $row['OFFICE'] ?></b></td>
                            <td><b><?php echo $row['CLIENT'] ?></b></td>
                            <td><b><?php echo $row['task_name'] ?></b></td>
                            <td><b><?php echo ucwords($row['Job_Manager']) ?></b></td>
                            <td><p style="color: <?php echo ($row['Assigned_Resources'] == 1) ? 'green' : 'red'; ?>;"><b><?php echo ($row['Assigned_Resources'] == 1) ? 'Yes' : 'No'; ?></b></p></td>
                            <td><b><?php echo $row['start_date'] ?></b></td>
                            <td><b><?php echo $row['end_date'] ?></b></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info dropdown-toggle" data-toggle="dropdown">Action</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="./index.php?page=view_job&id=<?php echo $row['Job_ID'] ?>">View</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
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
    .card-header {
        background-color: #007bff;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
    }
    .badge {
        font-size: 0.875rem;
    }
    .btn-default {
        background-color: white;
        border-color: #ddd;
    }
</style>

<!-- JavaScript for filtering -->
<script>
    $(document).ready(function(){
        var dataTable = $('#list').DataTable();

        $('#office-filter, #client-filter, #month-filter, #work-filter').change(function(){
            filterTable();
        });

        function filterTable() {
            var selectedOffice = $('#office-filter').val();
            var selectedClient = $('#client-filter').val();
            var selectedMonth = $('#month-filter').val();
            var selectedWorkType = $('#work-filter').val();

            dataTable.columns(1).search(selectedMonth)
                .columns(6).search(selectedOffice)
                .columns(7).search(selectedClient)
                .columns(8).search(selectedWorkType).draw();
        }
    });
</script>
