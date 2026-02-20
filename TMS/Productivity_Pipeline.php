<?php include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<style>
  table p{ margin: unset !important; }
  table td{ vertical-align: middle !important; }
  #list{
    width: 100% !important;
    table-layout: fixed;
  }
  #list th,
  #list td{
    width: 11.11%;
    word-wrap: break-word;
  }
</style>

<div class="col-lg-12">
  <div class="card card-outline card-success shadow-sm">
    <div class="card-header bg-primary text-white">
      <div class="card-tools">
        <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_job">
          <i class="fa fa-plus"></i> Add New Job
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="form-row mb-3">
        <div class="col-md-3">
          <label for="jobtype-filter">Filter by Job type:</label>
          <select id="jobtype-filter" class="form-control">
            <option value="">All</option>
            <?php
              $job_qry = $conn->query("SELECT DISTINCT JOB_TYPE FROM project_list");
              while($job_row = $job_qry->fetch_assoc()):
            ?>
              <option value="<?php echo htmlspecialchars($job_row['JOB_TYPE']); ?>">
                <?php echo htmlspecialchars($job_row['JOB_TYPE']); ?>
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
	        <table class="table tabe-hover table-condensed" id="list">
	          <colgroup>
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	            <col width="11.11%">
	          </colgroup>

          <thead style="background-color:#032033 !important; color:white">
            <tr>
              <th>Job_ID</th>
              <th>Job</th>
              <th>Job Type</th>
              <th>Team Name</th>
              <th>Date Created</th>
              <th>Who Created it</th>
              <th>Assigned</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php
              $loginId = (int)$_SESSION['login_id'];

              $qry = $conn->query("
                SELECT DISTINCT
                  CONCAT(u.firstname, ' ', u.lastname) AS c_name,
                  pl.*,
                  ts.team_name
                FROM project_list pl
                LEFT JOIN users u ON pl.Creator_ID = u.id
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
                $jobRef = urlencode(
                        base64_encode($row['id'] . '|' . bin2hex(random_bytes(6)))
                    );
            ?>
              <tr>
                <th class="text-center" style="color:#007bff"><?php echo (int)$row['id']; ?></th>

                <td><p><b><?php echo htmlspecialchars(ucwords($shortenedJobName)); ?></b></p></td>
                <td><p><b><?php echo htmlspecialchars(ucwords($row['JOB_TYPE'])); ?></b></p></td>
                <td><p><b><?php echo htmlspecialchars(ucwords($row['team_name'])); ?></b></p></td>
                <td><p><b><?php echo htmlspecialchars($row['date_created']); ?></b></p></td>

                <td>
                  <p><b>
                    <?php echo empty($row['c_name']) ? "N/A" : htmlspecialchars(ucwords($row['c_name'])); ?>
                  </b></p>
                </td>

                <?php if ((int)$row['assigned'] === 1): ?>
                  <td><p style="font-weight:bold; color:green">Yes</p></td>
                <?php else: ?>
                  <td><p style="font-weight:bold; color:red">No</p></td>
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
                  <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle"
                          data-toggle="dropdown" aria-expanded="true">
                    Action
                  </button>

                  <div class="dropdown-menu">
                    <!-- ✅ View uses job= (base64) instead of id= -->
                    <a class="dropdown-item view_project"
                       href="./index.php?page=view_job&job=<?php echo $jobRef; ?>">
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
  var dataTable = $('#list').DataTable();

  // Filters
  $('#jobtype-filter, #month-filter, #created-filter, #assigned-filter, #status-filter').change(function(){
    filterTable();
  });

  function filterTable() {
    var selectedJobType  = $('#jobtype-filter').val();
    var selectedMonth    = $('#month-filter').val();
    var selectedCreator  = $('#created-filter').val();
    var selectedAssigned = $('#assigned-filter').val();
    var selectedStatus   = $('#status-filter').val();

    // ✅ Column indexes with Job_ID included:
    // 0 Job_ID
    // 1 Job
    // 2 Job Type
    // 3 Team Name
    // 4 Date Created
    // 5 Who Created it
    // 6 Assigned
    // 7 Status

    dataTable
      .column(2).search(selectedJobType)
      // NOTE: Month isn't an actual column in the table, so DataTables can't search it directly.
      // If you want month filter to work properly, we must add a Month column (hidden or visible)
      // OR use a custom DataTables filter on Date Created.
      .column(5).search(selectedCreator)
      .column(6).search(selectedAssigned)
      .column(7).search(selectedStatus)
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
