<?php include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$loginId = (int)($_SESSION['login_id'] ?? 0);
?>
<style>
  .pipeline-modern {
    --surface: #ffffff;
    --ink: #0f172a;
    --muted: #64748b;
    --line: #dbe7f5;
    --brand-1: #0f4c81;
    --brand-2: #0b7db5;
    --brand-3: #5eb3f3;
  }

  .pipeline-modern table p{ margin: unset !important; }
  .pipeline-modern table td{ vertical-align: middle !important; }

  .pipeline-modern .pipeline-card {
    border: 1px solid var(--line);
    border-radius: 18px;
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    background: var(--surface);
  }

  .pipeline-modern .pipeline-header {
    background: linear-gradient(120deg, #0f172a 0%, #1e3a5f 45%, #2563eb 100%);
    border-radius: 18px 18px 0 0;
    border: 0;
    padding: 0.85rem 1rem;
  }

  .pipeline-modern .pipeline-header .card-tools {
    float: none;
    display: flex;
    justify-content: flex-end;
  }

  .pipeline-modern .add-job-btn {
    background: linear-gradient(125deg, var(--brand-1), var(--brand-2));
    border: 0 !important;
    border-radius: 999px;
    box-shadow: 0 8px 18px rgba(11, 125, 181, 0.28);
    color: #fff !important;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.42rem 0.95rem;
  }

  .pipeline-modern .add-job-btn:hover {
    transform: translateY(-1px);
    color: #fff !important;
  }

  .pipeline-modern .pipeline-filter-panel {
    background: #f8fbff;
    border: 1px solid var(--line);
    border-radius: 14px;
    margin-bottom: 0.9rem;
    padding: 0.8rem 0.7rem 0.2rem;
  }

  .pipeline-modern .pipeline-filter-panel label {
    color: #1e3a5f;
    display: block;
    font-size: 0.73rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
  }

  .pipeline-modern .pipeline-filter-panel .form-control {
    border: 1px solid #c9dcf3;
    border-radius: 10px;
    color: #334155;
    font-size: 0.82rem;
    height: calc(2rem + 2px);
    padding: 0.28rem 0.6rem;
  }

  .pipeline-modern .pipeline-filter-panel .form-control:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 0.17rem rgba(96, 165, 250, 0.16);
  }

  .pipeline-modern .table-responsive {
    border: 1px solid var(--line);
    border-radius: 14px;
    overflow-x: auto;
    overflow-y: visible;
  }

  .pipeline-modern #list{
    width: 100% !important;
    table-layout: fixed;
  }

  .pipeline-modern #list th,
  .pipeline-modern #list td{
    width: 10%;
    word-wrap: break-word;
  }

  .pipeline-modern .pipeline-table {
    margin: 0;
  }

  .pipeline-modern .pipeline-table thead th {
    border: 0;
    background: #0f172a;
    color: #dbeafe;
    font-size: 0.71rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    padding: 0.66rem 0.5rem;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .pipeline-modern .pipeline-table tbody td,
  .pipeline-modern .pipeline-table tbody th {
    border-top: 1px solid #edf2f7;
    color: #334155;
    font-size: 0.81rem;
    padding: 0.58rem 0.5rem;
  }

  .pipeline-modern .pipeline-table tbody tr:hover {
    background: #f8fafc;
  }

  .pipeline-modern .pipeline-id {
    color: var(--brand-2);
    font-weight: 700;
  }

  .pipeline-modern .assigned-yes {
    color: #047857;
    font-weight: 700;
  }

  .pipeline-modern .assigned-no {
    color: #dc2626;
    font-weight: 700;
  }

  .pipeline-modern .badge {
    border-radius: 999px;
    font-size: 0.69rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    padding: 0.37em 0.66em;
  }

  .pipeline-modern .badge-info {
    background: #dff3ff;
    color: #075985;
  }

  .pipeline-modern .badge-warning {
    background: #fff3d4;
    color: #92400e;
  }

  .pipeline-modern .badge-danger {
    background: #ffe3e3;
    color: #991b1b;
  }

  .pipeline-modern .badge-success {
    background: #ddfce7;
    color: #166534;
  }

  .pipeline-modern .badge-secondary {
    background: #e2e8f0;
    color: #334155;
  }

  .pipeline-modern .pipeline-action-btn {
    background: #ffffff;
    border: 1px solid #bfd8f8 !important;
    border-radius: 999px;
    color: #0f4c81 !important;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.3rem 0.82rem;
  }

  .pipeline-modern .pipeline-action-btn:hover {
    background: #eff6ff;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
  }

  .pipeline-modern .pipeline-action-menu {
    border: 1px solid #d8e6f7;
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
    padding: 0.25rem;
  }

  .pipeline-modern .pipeline-action-menu .dropdown-item {
    border-radius: 8px;
    color: #334155;
    font-size: 0.8rem;
    padding: 0.45rem 0.6rem;
  }

  .pipeline-modern .pipeline-action-menu .dropdown-item:hover {
    background: #eff6ff;
    color: #0f4c81;
  }

  .pipeline-modern .dataTables_wrapper .dataTables_length label,
  .pipeline-modern .dataTables_wrapper .dataTables_filter label,
  .pipeline-modern .dataTables_wrapper .dataTables_info,
  .pipeline-modern .dataTables_wrapper .dataTables_paginate {
    color: #64748b;
    font-size: 0.78rem;
  }

  .pipeline-modern .dataTables_wrapper .dataTables_filter input,
  .pipeline-modern .dataTables_wrapper .dataTables_length select {
    border: 1px solid #c9dcf3;
    border-radius: 8px;
    color: #334155;
    font-size: 0.78rem;
    padding: 0.2rem 0.45rem;
  }

  @media (max-width: 768px) {
    .pipeline-modern .pipeline-header .card-tools {
      justify-content: center;
    }

    .pipeline-modern .pipeline-filter-panel {
      padding: 0.7rem 0.52rem 0.15rem;
    }

    .pipeline-modern .pipeline-table thead th,
    .pipeline-modern .pipeline-table tbody td,
    .pipeline-modern .pipeline-table tbody th {
      font-size: 0.75rem;
      padding: 0.5rem 0.4rem;
    }
  }

  /* Readability overrides */
  .pipeline-modern {
    font-size: 0.98rem;
  }

  .pipeline-modern .pipeline-filter-panel label {
    font-size: 0.82rem;
  }

  .pipeline-modern .pipeline-filter-panel .form-control {
    font-size: 0.92rem;
  }

  .pipeline-modern .pipeline-table thead th {
    font-size: 0.8rem;
  }

  .pipeline-modern .pipeline-table tbody td,
  .pipeline-modern .pipeline-table tbody th {
    font-size: 0.9rem;
  }

  .pipeline-modern .badge,
  .pipeline-modern .pipeline-action-btn,
  .pipeline-modern .pipeline-action-menu .dropdown-item,
  .pipeline-modern .add-job-btn {
    font-size: 0.82rem;
  }
</style>

<div class="col-lg-12 pipeline-modern">
  <div class="card card-outline card-success shadow-sm pipeline-card">
    <div class="card-header bg-primary text-white pipeline-header">
      <div class="card-tools">
        <a class="btn btn-block btn-sm btn-default btn-flat border-primary add-job-btn" href="./index.php?page=new_job">
          <i class="fa fa-plus"></i> Add New Job
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="form-row mb-3 pipeline-filter-panel">
        <div class="col-md-3">
          <label for="jobtype-filter">Filter by Job type:</label>
          <select id="jobtype-filter" class="form-control">
            <option value="">All</option>
            <?php
              $job_qry = $conn->query("
                SELECT DISTINCT pl.JOB_TYPE AS job_type_name
                FROM project_list pl
                LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
                WHERE (pl.manager_id = $loginId OR ts.team_members = $loginId)
                  AND pl.assigned = 0
                  AND COALESCE(pl.JOB_TYPE, '') <> ''
                ORDER BY pl.JOB_TYPE ASC
              ");
              while($job_row = $job_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($job_row['job_type_name']); ?>">
                <?php echo htmlspecialchars($job_row['job_type_name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label for="month-filter">Filter by Month:</label>
          <select id="month-filter" class="form-control">
            <option value="">All</option>
            <?php
              $month_qry = $conn->query("
                SELECT DISTINCT wwp.month
                FROM project_list pl, working_week_periods wwp
                WHERE wwp.start_week>= pl.date_created AND wwp.end_week>=pl.date_created
              ");
              while($month_row = $month_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($month_row['month']); ?>">
                <?php echo htmlspecialchars($month_row['month']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label for="team-filter">Filter by Team:</label>
          <select id="team-filter" class="form-control">
            <option value="">All</option>
            <?php
              $pmId = $loginId;
              $team_qry = $conn->query("
                SELECT DISTINCT
	                        ts_pm.team_name AS team_name,
	                        ts_pm.team_id
	                      FROM team_schedule ts_pm
	                      JOIN team_schedule ts_me
	                        ON ts_me.team_id = ts_pm.team_id
	                      LEFT JOIN schedule_work_team swt
	                        ON swt.Work_Team = ts_pm.team_id
	                      WHERE ts_pm.team_members = $pmId
	                        AND ts_me.team_members = $loginId
                        ORDER BY ts_pm.team_name ASC
              ");
              while($team_row = $team_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($team_row['team_name']); ?>">
                <?php echo htmlspecialchars($team_row['team_name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label for="created-filter">Filter by Who created it:</label>
          <select id="created-filter" class="form-control">
            <option value="">All</option>
            <?php
              $creator_qry = $conn->query("
                SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) as P_name
                FROM project_list pl
                LEFT JOIN users u ON pl.Creator_ID = u.id
              ");
              while($creator_row = $creator_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($creator_row['P_name']); ?>">
                <?php echo htmlspecialchars($creator_row['P_name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label for="entity-filter">Filter by Entity:</label>
          <select id="entity-filter" class="form-control">
            <option value="">All</option>
            <?php
              $entity_qry = $conn->query("
                SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) AS entity_name
                FROM project_list pl
                LEFT JOIN users u ON pl.manager_id = u.id
                WHERE u.id IS NOT NULL
                ORDER BY entity_name ASC
              ");
              while($entity_row = $entity_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($entity_row['entity_name']); ?>">
                <?php echo htmlspecialchars($entity_row['entity_name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label for="assigned-filter">Filter by Assigned:</label>
          <select id="assigned-filter" class="form-control">
            <option value="">All</option>
            <?php
              $assigned_qry = $conn->query("
                SELECT DISTINCT CASE WHEN Assigned = 1 THEN 'yes' ELSE 'no' END AS assigned_status
                FROM project_list
              ");
              while($assigned_row =  $assigned_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($assigned_row['assigned_status']); ?>">
                <?php echo htmlspecialchars($assigned_row['assigned_status']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label for="status-filter">Filter by Status:</label>
          <select id="status-filter" class="form-control">
            <option value="">All</option>
            <?php
              $status_qry = $conn->query("SELECT DISTINCT status FROM project_list");
              while($status_row = $status_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($status_row['status']); ?>">
                <?php echo htmlspecialchars($status_row['status']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <br>

	      <div class="table-responsive">
	        <table class="table tabe-hover table-condensed pipeline-table" id="list">
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
	            <col width="10%">
	          </colgroup>

          <thead>
            <tr>
              <th>Job_ID</th>
              <th>Job</th>
              <th>Job Type</th>
              <th>Team Name</th>
              <th>Entity</th>
              <th>Date Created</th>
              <th>Who Created it</th>
              <th>Assigned</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php
              $qry = $conn->query("
                SELECT DISTINCT
                  CONCAT(u.firstname, ' ', u.lastname) AS c_name,
                  CONCAT(u1.firstname, ' ', u1.lastname) AS entity_name,
                  pl.*,
                  ts.team_name
                FROM project_list pl
                LEFT JOIN users u ON pl.Creator_ID = u.id
                LEFT JOIN users u1 ON pl.manager_id = u1.id
                LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
                WHERE (pl.manager_id = $loginId OR ts.team_members = $loginId)
                  AND pl.assigned = 0
                ORDER BY pl.id DESC
              ");

              while ($row = $qry->fetch_assoc()):
                $words = explode(' ', $row['name']);
                $shortenedJobName = (count($words) >= 2)
                  ? implode(' ', array_slice($words, 0, 9)) . '...'
                  : $row['name'];

                // ✅ encode id for View URL
                $jobPayload = (string)((int)$row['id']);
                $jobHash = hash_hmac('sha256', $jobPayload, 'my_app_secret_key');
                $jobRef = urlencode(base64_encode($jobPayload . '|' . $jobHash));
            ?>
              <tr>
                <th class="text-center pipeline-id"><?php echo (int)$row['id']; ?></th>

                <td><p><b><?php echo htmlspecialchars(ucwords($shortenedJobName)); ?></b></p></td>
                <td><p><b><?php echo htmlspecialchars(ucwords($row['JOB_TYPE'])); ?></b></p></td>
                <td><p><b><?php echo htmlspecialchars(ucwords($row['team_name'])); ?></b></p></td>
                <td><p><b><?php echo empty($row['entity_name']) ? 'N/A' : htmlspecialchars(ucwords($row['entity_name'])); ?></b></p></td>
                <td><p><b><?php echo htmlspecialchars($row['date_created']); ?></b></p></td>

                <td>
                  <p><b>
                    <?php echo empty($row['c_name']) ? "N/A" : htmlspecialchars(ucwords($row['c_name'])); ?>
                  </b></p>
                </td>

                <?php if ((int)$row['assigned'] === 1): ?>
                  <td><p class="assigned-yes">Yes</p></td>
                <?php else: ?>
                  <td><p class="assigned-no">No</p></td>
                <?php endif; ?>

                <td class="text-center">
                  <?php
                    if ($row['status'] == 'In-progress') {
                      echo "<span class='badge badge-info'>".htmlspecialchars($row['status'])."</span>";
                    } elseif ($row['status'] == 'On-Hold') {
                      echo "<span class='badge badge-warning'>".htmlspecialchars($row['status'])."</span>";
                    } elseif ($row['status'] == 'Dropped') {
                      echo "<span class='badge badge-danger'>".htmlspecialchars($row['status'])."</span>";
                    } elseif ($row['status'] == 'Done') {
                      echo "<span class='badge badge-success'>".htmlspecialchars($row['status'])."</span>";
                    } else {
                      echo "<span class='badge badge-secondary'>".htmlspecialchars($row['status'])."</span>";
                    }
                  ?>
                </td>

                <td class="text-center">
                  <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle pipeline-action-btn"
                          data-toggle="dropdown" aria-expanded="true">
                    Action
                  </button>

                  <div class="dropdown-menu pipeline-action-menu">
                    <!-- ✅ View uses job= (base64) instead of id= -->
                    <a class="dropdown-item view_project"
                       href="./index.php?page=view_job&job=<?php echo $jobRef; ?>&back=productivity_pipeline">
                      View
                    </a>

                    <?php if ($_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 3): ?>
                      <!-- Keeping existing id links as-is. If you want, we can also convert these to job= -->
                      <a class="dropdown-item view_project"
                         href="./index.php?page=assign_duties&job=<?php echo $jobRef ?>">
                        Assign
                      </a>
                    <?php endif; ?>

                    <?php if($_SESSION['login_type'] == 2): ?>
                      <a class="dropdown-item"
                         href="./index.php?page=edit_job&id=<?php echo $jobRef; ?>">
                        Edit
                      </a>

                      <a class="dropdown-item delete_project"
                         href="javascript:void(0)"
                         data-id="<?php echo (int)$row['id']; ?>">
                        Delete
                      </a>
                    <?php endif; ?>
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

<script>
$(document).ready(function(){

  // ✅ Initialize once
  var dataTable = $('#list').DataTable({
    order: [[0, 'desc']]
  });

  // Filters
  $('#jobtype-filter, #month-filter, #team-filter, #created-filter, #entity-filter, #assigned-filter, #status-filter').change(function(){
    filterTable();
  });

  function filterTable() {
    var selectedJobType  = $('#jobtype-filter').val();
    var selectedMonth    = $('#month-filter').val();
    var selectedTeam     = $('#team-filter').val();
    var selectedCreator  = $('#created-filter').val();
    var selectedEntity   = $('#entity-filter').val();
    var selectedAssigned = $('#assigned-filter').val();
    var selectedStatus   = $('#status-filter').val();

    // ✅ Column indexes with Job_ID included:
    // 0 Job_ID
    // 1 Job
    // 2 Job Type
    // 3 Team Name
    // 4 Entity
    // 5 Date Created
    // 6 Who Created it
    // 7 Assigned
    // 8 Status

    dataTable
      .column(2).search(selectedJobType)
      .column(3).search(selectedTeam)
      .column(4).search(selectedEntity)
      // NOTE: Month isn't an actual column in the table, so DataTables can't search it directly.
      // If you want month filter to work properly, we must add a Month column (hidden or visible)
      // OR use a custom DataTables filter on Date Created.
      .column(6).search(selectedCreator)
      .column(7).search(selectedAssigned)
      .column(8).search(selectedStatus)
      .draw();
  }

  // Delete
  $('.delete_project').click(function(){
    _conf("Are you sure to delete this job?","delete_project",[$(this).attr('data-id')])
  });

});

function delete_project(id){
  start_load()
  $.ajax({
    url:'ajax.php?action=delete_project',
    method:'POST',
    data:{
      id: id,
      csrf_token: '<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES); ?>'
    },
    success:function(resp){
      if(resp==1){
        alert_toast("Data successfully deleted",'success')
        setTimeout(function(){ location.reload() },1500)
      } else {
        alert_toast("Delete failed: " + resp,'error')
      }
    }
  })
}
</script>
