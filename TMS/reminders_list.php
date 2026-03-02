<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$loginType = isset($_SESSION['login_type']) ? (int)$_SESSION['login_type'] : 0;
$loginId = isset($_SESSION['login_id']) ? (int)$_SESSION['login_id'] : 0;

$whereSql = '';
if ($loginType === 2) {
    $whereSql = "WHERE r.who = {$loginId}";
}

$sql = "
    SELECT DISTINCT
        r.*,
        CONCAT(COALESCE(u.firstname, ''), ' ', COALESCE(u.lastname, '')) AS manager_name,
        c.company_name,
        c.CLIENT_ID AS client_code,
        cr.REP_NAME AS account_rep_name,
        ts.team_name,
        wt.task_name
    FROM reminders r
    LEFT JOIN users u
        ON u.id = r.who
    LEFT JOIN yasccoza_openlink_market.client c
        ON c.CLIENT_ID = r.account
    LEFT JOIN client_rep cr
        ON cr.REP_ID = r.account_rep
    LEFT JOIN (
        SELECT team_id, MAX(team_name) AS team_name
        FROM team_schedule
        GROUP BY team_id
    ) ts
        ON ts.team_id = r.team
    LEFT JOIN task_list wt
        ON wt.id = r.work_type
    {$whereSql}
    ORDER BY r.id DESC
";

$qry = $conn->query($sql);
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header bg-info text-white">
            <div class="card-tools d-flex justify-content-start">
                <a class="btn btn-sm btn-light btn-flat border-primary mx-1" href="./index.php?page=reminders">
                    <i class="fa fa-plus"></i> Add Reminder
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover table-bordered table-condensed table-sm" id="list" style="width:100%">
                <thead style="background-color:#0a3d62 !important; color:#fff">
                    <tr>
                        <th class="text-center">Parent ID</th>
                        <th>Reminder Name</th>
                        <th>Every (Days)</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Account</th>
                        <th>Account Rep</th>
                        <th>Team</th>
                        <th>Work Type</th>
                        <th>Meeting Day</th>
                        <th>Meeting Time</th>
                        <th>Start Date</th>
                        <th>Scheduled End Date</th>
                        <th>Platform</th>
                        <th>Meeting Link</th>
                        <th>Description</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($qry && $qry->num_rows > 0): ?>
                        <?php while ($row = $qry->fetch_assoc()): ?>
                            <?php
                            $startDate = !empty($row['start_date']) ? date('Y-m-d H:i', strtotime((string)$row['start_date'])) : '';
                            $date_created = !empty($row['date_created']) ? date('Y-m-d H:i', strtotime((string)$row['date_created'])) : '';
                            $endDate = !empty($row['scheduled_end_date']) ? date('Y-m-d H:i', strtotime((string)$row['scheduled_end_date'])) : '';
                            $meetingTime = !empty($row['meeting_time']) ? date('H:i', strtotime((string)$row['meeting_time'])) : '';
                            $meetingLink = (string)($row['meeting_link'] ?? '');
                            $isActive = isset($row['status']) && (int)$row['status'] === 1;
                            ?>
                            <tr>
                                <th class="text-center"><?php echo (int)($row['id'] ?? 0); ?></th>
                                <td><?php echo htmlspecialchars((string)($row['reminder_name'] ?? '')); ?></td>
                                <td><?php echo (int)($row['every_days'] ?? 1); ?></td>
                                <td>
                                    <?php if ($isActive): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Deactive</span>
                                    <?php endif; ?>
	                                </td>
		                                <td class="text-center">
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        Action
                                      </button>
	                                      <div class="dropdown-menu">
	                                        <a class="dropdown-item" href="./index.php?page=reminders&id=<?php echo (int)$row['id']; ?>">
	                                          Edit
	                                        </a>
	                                        <div class="dropdown-divider"></div>
                                        <a
                                          class="dropdown-item toggle-reminder-status <?php echo $isActive ? 'text-danger' : 'text-success'; ?>"
                                          href="javascript:void(0)"
                                          data-id="<?php echo (int)$row['id']; ?>"
	                                          data-status="<?php echo (int)($row['status'] ?? 0); ?>">
	                                          <?php echo $isActive ? 'Deactivate' : 'Activate'; ?>
	                                        </a>
                                          <?php if (in_array($loginType, [1, 2], true)): ?>
	                                        <div class="dropdown-divider"></div>
                                          <a
                                            class="dropdown-item delete-reminder text-danger"
                                            href="javascript:void(0)"
                                            data-id="<?php echo (int)$row['id']; ?>">
                                            Delete
                                          </a>
                                          <?php endif; ?>
	                                      </div>
	                                    </div>
		                                </td>
                                <td><?php echo htmlspecialchars(trim((string)($row['manager_name'] ?? ''))); ?></td>
                                <td>
                                    <?php echo htmlspecialchars((string)($row['company_name'] ?? '')); ?>
                                    <?php if (!empty($row['client_code'])): ?>
                                        <br><small class="text-muted">(<?php echo htmlspecialchars((string)$row['client_code']); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars((string)($row['account_rep_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['team_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['task_name'] ?? '')); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars((string)($row['meeting_day'] ?? ''))); ?></td>
                                <td><?php echo htmlspecialchars($meetingTime); ?></td>
                                <td><?php echo htmlspecialchars($startDate); ?></td>
                                <td><?php echo htmlspecialchars($endDate); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['online_meeting'] ?? '')); ?></td>
                                <td>
                                    <?php if ($meetingLink !== ''): ?>
                                        <a href="<?php echo htmlspecialchars($meetingLink); ?>" target="_blank" rel="noopener noreferrer">
                                            Open Link
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars((string)($row['description'] ?? '')); ?></td>
                                 <td><?php echo htmlspecialchars($date_created); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
  overflow-x: auto;
  overflow-y: visible;
}
.table-responsive .dropdown-menu {
  z-index: 2050;
}
</style>

<script>
$(document).ready(function(){
  $('#list').DataTable({
    autoWidth: false
  });

	  $(document).on('click', '.toggle-reminder-status', function () {
    var id = parseInt($(this).attr('data-id'), 10);
    var currentStatus = parseInt($(this).attr('data-status'), 10) || 0;
    var newStatus = currentStatus === 1 ? 0 : 1;
    var actionText = newStatus === 1 ? 'activate' : 'deactivate';

    if (!confirm('Are you sure you want to ' + actionText + ' this reminder?')) {
      return;
    }

    start_load();
    $.ajax({
      url: 'update_reminder_status.php',
      method: 'POST',
      data: {
        id: id,
        status: newStatus,
        csrf_token: '<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES); ?>'
      },
      success: function(resp){
        resp = String(resp).trim();
        if(resp === '1'){
          alert_toast('Reminder status updated','success');
          setTimeout(function(){ location.reload(); }, 800);
        } else if(resp === 'csrf'){
          end_load();
          alert_toast('Session expired. Refresh and try again.','warning');
        } else if(resp === 'unauthorized'){
          end_load();
          alert_toast('Unauthorized','danger');
        } else {
          end_load();
          alert_toast('Update failed: ' + resp,'danger');
        }
      },
      error: function(xhr){
        end_load();
        console.log(xhr.status, xhr.responseText);
        alert_toast('Request failed','danger');
      }
    });
	  });

  $(document).on('click', '.delete-reminder', function () {
    var id = parseInt($(this).attr('data-id'), 10);
    if (!confirm('Are you sure you want to delete this reminder?')) {
      return;
    }

    start_load();
    $.ajax({
      url: 'delete_reminder.php',
      method: 'POST',
      data: {
        id: id,
        csrf_token: '<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES); ?>'
      },
      success: function(resp){
        resp = String(resp).trim();
        if(resp === '1'){
          alert_toast('Reminder deleted successfully','success');
          setTimeout(function(){ location.reload(); }, 800);
        } else if(resp === 'csrf'){
          end_load();
          alert_toast('Session expired. Refresh and try again.','warning');
        } else if(resp === 'unauthorized'){
          end_load();
          alert_toast('Unauthorized','danger');
        } else {
          end_load();
          alert_toast('Delete failed: ' + resp,'danger');
        }
      },
      error: function(xhr){
        end_load();
        console.log(xhr.status, xhr.responseText);
        alert_toast('Request failed','danger');
      }
    });
  });
});
</script>
