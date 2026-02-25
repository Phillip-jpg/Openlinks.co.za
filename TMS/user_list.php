<?php
// file: user_management.php
include 'db_connect.php';

/**
 * Escape output for safe HTML rendering.
 */
function e(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Map numeric user type to label.
 */
function userTypeLabel($type): string
{
	switch ((int)$type) {
		case 1: return 'Super Admin';
		case 2: return 'Project Manager';
		case 3: return 'Member';
		case 4: return 'Admin Assistant';
		default: return 'Unknown';
	}
}

/**
 * Split an email into two lines for display.
 */
function splitEmail(string $email): string
{
	$len = strlen($email);
	$half = (int)floor($len / 2);

	$first = substr($email, 0, $half);
	$second = substr($email, $half);

	return '<b>' . e($first) . "</b><br><b>" . e($second) . '</b>';
}

/**
 * Sanitize comma-separated IDs into a safe "1,2,3" list.
 * Returns empty string if none.
 */
function normalizeIdList(?string $csv): string
{
	if ($csv === null) return '';
	$csv = trim($csv);

	if ($csv === '' || $csv === '0') return '';

	$parts = array_filter(array_map('trim', explode(',', $csv)), fn($v) => $v !== '');
	$ints = array_values(array_filter(array_map('intval', $parts), fn($v) => $v > 0));

	return $ints ? implode(',', $ints) : '';
}

/**
 * Fetch task names for a sanitized id list.
 */
function fetchTaskNames(mysqli $conn, string $idList): array
{
	if ($idList === '') return [];

	$res = $conn->query("SELECT task_name FROM task_list WHERE id IN ($idList)");
	if (!$res) return [];

	$names = [];
	while ($r = $res->fetch_assoc()) {
		$names[] = (string)$r['task_name'];
	}
	return $names;
}

/**
 * Fetch task names created by a specific creator (task_list.creator_id).
 */
function fetchTaskNamesByCreator(mysqli $conn, int $creatorId): array
{
	if ($creatorId <= 0) return [];

	$stmt = $conn->prepare("SELECT task_name FROM task_list WHERE creator_id = ? ORDER BY task_name ASC");
	if (!$stmt) return [];

	$stmt->bind_param('i', $creatorId);
	$stmt->execute();
	$res = $stmt->get_result();

	$names = [];
	while ($res && ($r = $res->fetch_assoc())) {
		$names[] = (string)$r['task_name'];
	}
	$stmt->close();

	return $names;
}

/**
 * Render task names in two lines.
 */
function renderTaskNames(array $taskNames): string
{
	if (!$taskNames) {
		return '<b style="font-size:14px; color:red">No work type assigned</b>';
	}

	$half = (int)ceil(count($taskNames) / 2);
	$line1 = implode(', ', array_slice($taskNames, 0, $half));
	$line2 = implode(', ', array_slice($taskNames, $half));

	return '<b>' . e($line1) . '<br>' . e($line2) . '</b>';
}

$loginType = (int)($_SESSION['login_type'] ?? 0);
$loginId = (int)($_SESSION['login_id'] ?? 0);

// KEEP SQL AS-IS (only formatting/whitespace changed)
if ($loginType == 2) {
	$qry = $conn->query("
        SELECT
            u.*,
            CONCAT(u.firstname, ' ', u.lastname) AS name,
              CONCAT(u1.firstname, ' ', u1.lastname) AS project_manager,
            CONCAT('(', GROUP_CONCAT(DISTINCT i.office ORDER BY i.office SEPARATOR ' , '), ')') AS offices,
            CONCAT('(', GROUP_CONCAT(DISTINCT it.title ORDER BY it.title SEPARATOR ' , '), ')') AS titles
        FROM
            users AS u
        LEFT JOIN yasccoza_openlink_admin_db.admin_sector AS s
            ON u.id = s.ADMIN_ID
        LEFT JOIN users u1
              ON u1.id = u.creator_id 
        LEFT JOIN yasccoza_openlink_association_db.industry_title AS it
            ON it.TITLE_ID = s.INDUSTRY_ID
        LEFT JOIN yasccoza_openlink_association_db.industry AS i
            ON i.INDUSTRY_ID = s.OFFICE_ID
        WHERE
            u.creator_id = {$loginId} OR u.id = {$loginId}
        GROUP BY
            u.id
        ORDER BY
            name ASC
    ");
} else {
	$qry = $conn->query("
        SELECT
  u.*,
  CONCAT(u.firstname, ' ', u.lastname) AS name,
  CONCAT(u1.firstname, ' ', u1.lastname) AS project_manager,
  COALESCE(a.offices, '()') AS offices,
  COALESCE(a.titles, '()')  AS titles
FROM users u
LEFT JOIN users u1
  ON u1.id = u.creator_id          -- project manager row
LEFT JOIN (
  SELECT
    s.ADMIN_ID,
    CONCAT('(',
      GROUP_CONCAT(DISTINCT i.office ORDER BY i.office SEPARATOR ' , '),
    ')') AS offices,
    CONCAT('(',
      GROUP_CONCAT(DISTINCT it.title ORDER BY it.title SEPARATOR ' , '),
    ')') AS titles
  FROM yasccoza_openlink_admin_db.admin_sector s
  LEFT JOIN yasccoza_openlink_association_db.industry_title it
    ON it.TITLE_ID = s.INDUSTRY_ID
  LEFT JOIN yasccoza_openlink_association_db.industry i
    ON i.INDUSTRY_ID = s.OFFICE_ID
  GROUP BY s.ADMIN_ID
) a
  ON a.ADMIN_ID = u.id
ORDER BY name ASC;

    ");
}

?>
<div class="col-lg-12">
	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
			<h4 class="card-title mb-0">User Management</h4>
			<div class="card-tools">
				<a class="btn btn-sm btn-light border-primary" href="./index.php?page=new_user">
					<i class="fa fa-plus"></i> Add New User
				</a>
			</div>
		</div>

		<div class="card-body">
			<div class="form-row mb-3">
				<div class="col-md-4">
					<label for="entity-filter" class="mb-1"><b>Filter by Entity</b></label>
					<select id="entity-filter" class="form-control form-control-sm">
						<option value="">All Entities</option>
					</select>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-hover table-bordered table-condensed" id="list">
					<thead style="background-color:#032033 !important; color:white">
						<tr>
							<th style="width: 5%;">Member ID</th>
							<th style="width: 5%;">Name</th>
							<th style="width: 5%;">Date Created</th>
						    <th style="width: 5%;">Entity</th>
							<th style="width: 5%;">Email</th>
							<th style="width: 5%;">Type</th>
							<th style="width: 5%;">Work Types</th>
							<th style="width: 5%;">Industries</th>
							<th style="width: 5%;">Orbited</th>
							
							<th style="width: 5%;">Action</th>
						</tr>
					</thead>

					<tbody>
						<?php if ($qry): ?>
							<?php while ($row = $qry->fetch_assoc()): ?>
								<?php
								$userId = (int)$row['id'];
								$rowType = (int)($row['type'] ?? 0);

								$idList = normalizeIdList($row['task_ids'] ?? '');
								$taskNames = fetchTaskNames($conn, $idList);

								// Entity users (type 2) should show work types from task_list.creator_id.
								// This applies in admin (else query) view as well.
								if ($rowType === 2) {
									$entityTaskNames = fetchTaskNamesByCreator($conn, $userId);
									if (!empty($entityTaskNames)) {
										$taskNames = $entityTaskNames;
									}
								}

								// Keep secret out of source in real deployments (env/config).
								$secret = 'my_app_secret_key';
								$payload = (string)$userId;
								$hash = hash_hmac('sha256', $payload, $secret);
								$encoded = base64_encode($payload . ':' . $hash);
								?>
								<tr>
									<td><?= e((string)$userId) ?></td>
									<td><?= e((string)($row['name'] ?? '')) ?></td>
									<td><?= e((string)($row['date_created'] ?? '')) ?></td>
								<td>
                                     
                                        <?= e(trim((string)($row['project_manager'] ?? '')) !== '' ? $row['project_manager'] : 'NA/PM/SA') ?>
                                  
                                    </td>
									<td style="word-wrap: break-word;">
										<?= splitEmail((string)($row['email'] ?? '')) ?>
									</td>
									<td><?= e(userTypeLabel($row['type'] ?? 0)) ?></td>
									<td><?= renderTaskNames($taskNames) ?></td>
									<td>
										Industry: <?= e((string)($row['titles'] ?? '')) ?><br>
										Office: <b class="text-primary"><?= e((string)($row['offices'] ?? '')) ?></b>
									</td>

									<td><?= e(trim((string)($row['orbiter_id'] ?? 0)) !== '0' ? 'Yes' : 'No') ?></td>
									
									<td class="text-center">
										<button
											type="button"
											class="btn btn-default btn-sm btn-flat border-info text-info dropdown-toggle"
											data-toggle="dropdown"
											aria-expanded="false"
										>
											Action
										</button>

										<div class="dropdown-menu">
											<a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?= e((string)$userId) ?>">View</a>

											<?php if ($loginType === 2): ?>
												<div class="dropdown-divider"></div>
												<?php if ($row['orbiter_id'] == 0): ?>
												<a class="dropdown-item" href="./index.php?page=edit_user&id=<?= e(urlencode($encoded)) ?>">
													Edit
												</a>
												<?php endif; ?>
												<div class="dropdown-divider"></div>

												<!-- <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?= e((string)$userId) ?>">Delete</a> -->
											<?php endif; ?>
										</div>
									</td>
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
	.table-responsive { overflow-x: auto; }
	table p { margin: unset !important; }
	table td, table th { vertical-align: middle !important; }
	.table-hover tbody tr:hover { background-color: #f1f1f1; }
	.btn-default { background-color: #fff; border-color: #ddd; }
	.dropdown-menu { min-width: 0; }
	.dropdown-item:hover { background-color: #f1f1f1; }
</style>

	<script>
		$(document).ready(function () {
			var table = $('#list').DataTable();
			var ENTITY_COLUMN_INDEX = 3; // Member ID=0, Name=1, Date=2, Entity=3

			// Build entity filter values from the Entity column.
			var entities = {};
			table.column(ENTITY_COLUMN_INDEX).data().each(function (value) {
				var text = $('<div>').html(value).text().trim();
				if (text !== '') {
					entities[text] = true;
				}
			});

		Object.keys(entities).sort().forEach(function (entityName) {
			$('#entity-filter').append(
				$('<option>', { value: entityName, text: entityName })
			);
		});

			$('#entity-filter').on('change', function () {
				var selected = $(this).val();
				if (selected) {
					table.column(ENTITY_COLUMN_INDEX).search('^' + $.fn.dataTable.util.escapeRegex(selected) + '$', true, false).draw();
				} else {
					table.column(ENTITY_COLUMN_INDEX).search('').draw();
				}
			});

		$(document).on('click', '.view_user', function () {
			const id = $(this).attr('data-id');
			uni_modal("<i class='fa fa-id-card'></i> User Details", "view_user.php?id=" + id);
		});

		$(document).on('click', '.delete_user', function () {
			const id = $(this).attr('data-id');
			_conf("Are you sure to delete this user?", "delete_user", [id]);
		});
	});

	function delete_user(id) {
		start_load();
		$.ajax({
			url: 'ajax.php?action=delete_user',
			method: 'POST',
			data: { id: id },
			success: function (resp) {
				if (resp == 1) {
					alert_toast("Data successfully deleted", 'success');
					setTimeout(function () { location.reload(); }, 1500);
				}
			}
		});
	}
</script>
