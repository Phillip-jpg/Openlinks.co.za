<?php include 'db_connect.php'; ?>
<?php
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$type_arr = array('', 'Super Admin', 'Entity', 'Member', 'Admin Assistant');
$user = null;

if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    if ($userId > 0) {
        $qry = $conn->query("
            SELECT
                u.*,
                CONCAT(u.firstname, ' ', u.lastname) AS name,
                COALESCE(sector_data.titles, '') AS titles,
                COALESCE(sector_data.offices, '') AS offices
            FROM users u
            LEFT JOIN (
                SELECT
                    s.ADMIN_ID,
                    GROUP_CONCAT(DISTINCT it.title ORDER BY it.title SEPARATOR ' , ') AS titles,
                    GROUP_CONCAT(DISTINCT i.office ORDER BY i.office SEPARATOR ' , ') AS offices
                FROM yasccoza_openlink_admin_db.admin_sector s
                LEFT JOIN yasccoza_openlink_association_db.industry_title it
                    ON it.TITLE_ID = s.INDUSTRY_ID
                LEFT JOIN yasccoza_openlink_association_db.industry i
                    ON i.INDUSTRY_ID = s.OFFICE_ID
                GROUP BY s.ADMIN_ID
            ) AS sector_data
                ON sector_data.ADMIN_ID = u.id
            WHERE u.id = {$userId}
            LIMIT 1
        ");

        if ($qry && $qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            foreach ($user as $k => $v) {
                $$k = $v;
            }
        }
    }
}

$name = $user['name'] ?? '';
$email = $user['email'] ?? '';
$firstname = $user['firstname'] ?? '';
$lastname = $user['lastname'] ?? '';
$avatar = $user['avatar'] ?? '';
$type = (int)($user['type'] ?? 0);
$number = $user['number'] ?? '';
$titles = trim((string)($user['titles'] ?? ''));
$offices = trim((string)($user['offices'] ?? ''));

if ($titles === '') {
    $titles = 'No industries linked';
}
if ($offices === '') {
    $offices = 'No offices linked';
}
?>
<div class="container-fluid">
    <div class="card card-widget widget-user shadow">
        <div class="widget-user-header bg-dark">
            <h3 class="widget-user-username"><?php echo ucwords($name) ?></h3>
            <h5 class="widget-user-desc"><?php echo e($email) ?></h5>
        </div>
        <div class="widget-user-image">
            <?php if (empty($avatar) || (!empty($avatar) && !is_file('assets/uploads/' . $avatar))): ?>
                <span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px">
                    <h4><?php echo strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)) ?></h4>
                </span>
            <?php else: ?>
                <img class="img-circle elevation-2" src="assets/uploads/<?php echo e($avatar) ?>" alt="User Avatar" style="width: 90px;height:90px;object-fit: cover">
            <?php endif ?>
        </div>
        <div class="card-footer">
            <div class="container-fluid">
                <dl>
                    <dt>Role</dt>
                    <dd><?php echo e($type_arr[$type] ?? 'Unknown') ?></dd>

                    <dt>Phone</dt>
                    <dd><?php echo e($number) ?></dd>

                    <dt>Industries</dt>
                    <dd><?php echo e($titles) ?></dd>

                    <dt>Offices</dt>
                    <dd><?php echo e($offices) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer display p-0 m-0">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<style>
    #uni_modal .modal-footer {
        display: none;
    }
    #uni_modal .modal-footer.display {
        display: flex;
    }
</style>
