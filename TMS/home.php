

<?php
include('db_connect.php');

$twhere = "";
if ($_SESSION['login_type'] != 1) {
    $twhere = " ";
}
?>

<?php
$where = "";
if ($_SESSION['login_type'] == 2) {
    $where = " where manager_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_type'] == 3) {
    $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}

$where2 = "";
if ($_SESSION['login_type'] == 2) {
    $where2 = " where p.manager_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_type'] == 3) {
    $where2 = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}

include('db_connect.php');

$itemsPerPage = 5;
$currentPage = isset($_GET['here']) ? (int)$_GET['here'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$query = "";
$totalQuery = "";
if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 4) {
    $query = "SELECT * FROM project_list ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset";
    $totalQuery = "SELECT COUNT(*) as total FROM project_list";
} elseif ($_SESSION['login_type'] == 2) {
    $query = "SELECT * FROM project_list WHERE manager_id = {$_SESSION['login_id']} ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset";
    $totalQuery = "SELECT COUNT(*) as total FROM project_list WHERE manager_id = {$_SESSION['login_id']}";
} else {
    $query = "SELECT DISTINCT project_list.*
              FROM project_list
              INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
              WHERE assigned_duties.user_id = {$_SESSION['login_id']}
              ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset";
    $totalQuery = "SELECT COUNT(DISTINCT project_list.id) as total
                   FROM project_list
                   INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
                   WHERE assigned_duties.user_id = {$_SESSION['login_id']}";
}

$qry = $conn->query($query);
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $itemsPerPage);

$login_id = $_SESSION['login_id'];
?>

<style>
    .dashboard-home {
        --surface: #ffffff;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e2e8f0;
        --primary-1: #0f4c81;
        --primary-2: #0b7db5;
        --primary-3: #5eb3f3;
        --success-1: #059669;
        --success-2: #34d399;
        --warning-1: #d97706;
        --warning-2: #fbbf24;
        --info-1: #2563eb;
        --info-2: #60a5fa;
    }

    .dashboard-home .welcome-card {
        border: 0;
        border-radius: 18px;
        background: linear-gradient(130deg, var(--primary-1) 0%, var(--primary-2) 50%, var(--primary-3) 100%);
        box-shadow: 0 12px 30px rgba(15, 76, 129, 0.22);
        overflow: hidden;
    }

    .dashboard-home .welcome-card .card-body {
        background: transparent !important;
        padding: 1.4rem 1.6rem;
        color: #fff;
    }

    .dashboard-home .welcome-card h4 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .dashboard-home .welcome-copy {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .dashboard-home .section-divider {
        border: 0;
        border-top: 1px solid var(--line);
        margin: 1rem 0 1.25rem;
    }

    .dashboard-home .jobs-card {
        border: 1px solid var(--line);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        background: var(--surface);
    }

    .dashboard-home .jobs-card-header {
        border: 0;
        background: linear-gradient(120deg, #0f172a 0%, #1e3a5f 45%, #2563eb 100%);
        color: #fff;
        padding: 0.95rem 1.2rem;
    }

    .dashboard-home .jobs-card-header h5 {
        margin: 0;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    .dashboard-home .jobs-table {
        margin: 0;
    }

    .dashboard-home .jobs-table thead th {
        border: 0;
        background: #0f172a;
        color: #e2e8f0;
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.7rem 0.75rem;
        white-space: nowrap;
    }

    .dashboard-home .jobs-table tbody td {
        border-top: 1px solid #edf2f7;
        color: #334155;
        font-size: 0.89rem;
        vertical-align: middle;
        padding: 0.7rem 0.75rem;
    }

    .dashboard-home .jobs-table tbody tr:hover {
        background: #f8fafc;
    }

    .dashboard-home .job-id {
        color: var(--primary-1);
        font-weight: 700;
    }

    .dashboard-home .view-job-btn {
        border: 0;
        border-radius: 999px;
        padding: 0.34rem 0.82rem;
        font-size: 0.78rem;
        font-weight: 600;
        background: linear-gradient(120deg, var(--primary-1), var(--primary-2));
        color: #fff;
        box-shadow: 0 6px 16px rgba(11, 125, 181, 0.28);
    }

    .dashboard-home .view-job-btn:hover {
        transform: translateY(-1px);
        color: #fff;
    }

    .dashboard-home .badge {
        border-radius: 999px;
        padding: 0.38em 0.72em;
        font-size: 0.73rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .dashboard-home .badge-info {
        background: #dff3ff;
        color: #075985;
    }

    .dashboard-home .badge-warning {
        background: #fff3d4;
        color: #92400e;
    }

    .dashboard-home .badge-danger {
        background: #ffe3e3;
        color: #991b1b;
    }

    .dashboard-home .badge-success {
        background: #ddfce7;
        color: #166534;
    }

    .dashboard-home .pagination .page-item .page-link {
        border-radius: 999px;
        border: 1px solid var(--line);
        color: #1e293b;
        font-size: 0.82rem;
        font-weight: 600;
        margin: 0 0.25rem;
        padding: 0.42rem 0.88rem;
        transition: all 0.2s ease;
    }

    .dashboard-home .pagination .page-item .page-link:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .dashboard-home .metric-card {
        position: relative;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: linear-gradient(145deg, #ffffff, #f8fbff);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
        min-height: 116px;
        overflow: hidden;
    }

    .dashboard-home .metric-card::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 4px;
    }

    .dashboard-home .metric-card .inner {
        padding: 1rem 1.1rem;
    }

    .dashboard-home .metric-card h3 {
        margin: 0;
        color: var(--ink);
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.15;
    }

    .dashboard-home .metric-card p {
        margin: 0.3rem 0 0;
        color: var(--muted);
        font-size: 0.85rem;
        letter-spacing: 0.01em;
    }

    .dashboard-home .metric-icon {
        position: absolute;
        right: 14px;
        top: 12px;
        font-size: 2rem;
        opacity: 0.2;
    }

    .dashboard-home .metric-primary::after {
        background: linear-gradient(90deg, var(--primary-1), var(--primary-3));
    }

    .dashboard-home .metric-primary .metric-icon {
        color: var(--primary-1);
    }

    .dashboard-home .metric-success::after {
        background: linear-gradient(90deg, var(--success-1), var(--success-2));
    }

    .dashboard-home .metric-success .metric-icon {
        color: var(--success-1);
    }

    .dashboard-home .metric-warning::after {
        background: linear-gradient(90deg, var(--warning-1), var(--warning-2));
    }

    .dashboard-home .metric-warning .metric-icon {
        color: var(--warning-1);
    }

    .dashboard-home .metric-info::after {
        background: linear-gradient(90deg, var(--info-1), var(--info-2));
    }

    .dashboard-home .metric-info .metric-icon {
        color: var(--info-1);
    }

    .dashboard-home .metric-extra::after {
        background: linear-gradient(90deg, #4f46e5, #818cf8);
    }

    .dashboard-home .metric-extra .metric-icon {
        color: #4f46e5;
    }

    @media (max-width: 768px) {
        .dashboard-home .welcome-card h4 {
            font-size: 1.15rem;
        }

        .dashboard-home .jobs-card-header h5 {
            font-size: 1rem;
        }

        .dashboard-home .metric-card h3 {
            font-size: 1.45rem;
        }

        .dashboard-home .jobs-table thead th,
        .dashboard-home .jobs-table tbody td {
            font-size: 0.8rem;
            padding: 0.6rem 0.55rem;
        }
    }

    /* Readability overrides */
    .dashboard-home {
        font-size: 0.98rem;
    }

    .dashboard-home .jobs-table thead th {
        font-size: 0.82rem;
    }

    .dashboard-home .jobs-table tbody td {
        font-size: 0.93rem;
    }

    .dashboard-home .welcome-copy,
    .dashboard-home .metric-card p {
        font-size: 0.95rem;
    }

    .dashboard-home .badge {
        font-size: 0.8rem;
    }

    .dashboard-home .pagination .page-item .page-link {
        font-size: 0.9rem;
    }
</style>

<div class="dashboard-home">
<div class="col-12">
    <div class="card welcome-card shadow-sm">
        <div class="card-body bg-light">
            <h4>Welcome <?php echo $_SESSION['login_name']; ?>!</h4>
            <span class="welcome-copy">Here is a quick snapshot of current activity.</span>
        </div>
    </div>
</div>
<hr class="section-divider">

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-success shadow-sm jobs-card">
            <div class="card-header bg-primary text-white jobs-card-header">
                <h5><b>Jobs</b></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-0 jobs-table">
                        <colgroup>
                            <col width="5%">
                            <col width="25%">
                            <col width="15%">
                            <col width="15%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Job</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 0;
                            while ($row = $qry->fetch_assoc()) {
                                $words = explode(' ', $row['name']);
                                $shortenedJobName = count($words) >= 2 ? implode(' ', array_slice($words, 0, 3)) . '...' : $row['name'];

                                echo "<tr>";
                                echo "<td class='job-id'>" . $row['id'] . "</td>";
                                echo "<td>" . $shortenedJobName . "</td>";
                                echo "<td>" . $row['start_date'] . "</td>";
                                echo "<td>" . $row['end_date'] . "</td>";
                                echo "<td>";
                                if ($row['status'] == 'In-progress') {
                                    echo "<span class='badge badge-info'>{$row['status']}</span>";
                                } elseif ($row['status'] == 'On-Hold') {
                                    echo "<span class='badge badge-warning'>{$row['status']}</span>";
                                } elseif ($row['status'] == 'Dropped') {
                                    echo "<span class='badge badge-danger'>{$row['status']}</span>";
                                } elseif ($row['status'] == 'Done') {
                                    echo "<span class='badge badge-success'>{$row['status']}</span>";
                                }
                                echo "</td>";
                                $jobPayload = (string) ((int) $row['id']);
                                $jobHash = hash_hmac('sha256', $jobPayload, 'my_app_secret_key');
                                $jobToken = base64_encode($jobPayload . '|' . $jobHash);
                                echo "<td><a class='btn btn-primary btn-sm view-job-btn' href='./index.php?page=view_job&job=" . urlencode($jobToken) . "&back=home'><i class='fas fa-folder'></i> View</a></td>";
                                echo "</tr>";
                                $count++;
                            }
                            ?>  
                        </tbody>
                    </table>

                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-3 mb-0">
                            <?php if ($currentPage > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="../TMS?home&here=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo; Previous</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($currentPage < $totalPages) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="../TMS?home&here=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">Next &raquo;</span>
                                    </a>
                                </li>
                        
                            <?php endif; ?>
                        </ul>
                        <br>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar stats -->
    <div class="col-md-4 stats-col">
        <div class="row">
            <!-- Total Jobs -->
            <div class="col-12">
                <div class="small-box bg-light shadow-sm border metric-card metric-primary">
                    <div class="inner">
                        <h3>
                            <?php 
                            if ($_SESSION['login_type'] == 2) {
                                $qry = $conn->query("SELECT project_list.* FROM project_list WHERE manager_id = {$_SESSION['login_id']} ORDER BY date_created DESC");
                                echo $qry->num_rows;
                            } elseif($_SESSION['login_type'] == 3){
                                $qry = $conn->query("SELECT DISTINCT project_list.*
                                                     FROM project_list
                                                     INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
                                                     WHERE assigned_duties.user_id = {$_SESSION['login_id']}
                                                     ORDER BY date_created DESC");
                                echo $qry->num_rows;
                            } else {
                                $qry = $conn->query("SELECT project_list.* FROM project_list");
                                echo $qry->num_rows;
                            }
                            ?>
                        </h3>
                        <p>Total Jobs</p>
                    </div>
                    <div class="icon metric-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            
             <div class="col-12">
                <div class="small-box bg-light shadow-sm border metric-card metric-success">
                    <div class="inner">
                        <h3>
                            <?php 
                            if ($_SESSION['login_type'] == 2) {
                                $qry = $conn->query("SELECT project_list.*
                                                        FROM project_list
                                                        WHERE manager_id = {$_SESSION['login_id']}
                                                          AND Job_Done IS NOT NULL
                                                        ORDER BY date_created DESC;");
                                echo $qry->num_rows;
                            } elseif($_SESSION['login_type'] == 3){
                                $qry = $conn->query("SELECT DISTINCT project_list.*
                                                     FROM project_list
                                                     INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
                                                     WHERE assigned_duties.user_id = {$_SESSION['login_id']} AND assigned_duties.Done_Date IS NOT NULL
                                                     ORDER BY date_created DESC");
                                echo $qry->num_rows;
                            } else {
                                $qry = $conn->query("SELECT project_list.* FROM project_list WHERE Job_Done IS NOT NULL");
                                echo $qry->num_rows;
                            }
                            ?>
                        </h3>
                        <p>Total Jobs Done</p>
                    </div>
                    <div class="icon metric-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="small-box bg-light shadow-sm border metric-card metric-warning">
                    <div class="inner">
                        <h3>
                            <?php 
                            if ($_SESSION['login_type'] == 2) {
                                $qry = $conn->query("SELECT project_list.*
                                                        FROM project_list
                                                        WHERE manager_id = {$_SESSION['login_id']}
                                                          AND Job_Done IS NULL
                                                        ORDER BY date_created DESC;");
                                echo $qry->num_rows;
                            } elseif($_SESSION['login_type'] == 3){
                                $qry = $conn->query("SELECT DISTINCT project_list.*
                                                     FROM project_list
                                                     INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
                                                     WHERE assigned_duties.user_id = {$_SESSION['login_id']} AND assigned_duties.Done_Date IS NULL
                                                     ORDER BY date_created DESC");
                                echo $qry->num_rows;
                            } else {
                                $qry = $conn->query("SELECT project_list.* FROM project_list WHERE Job_Done IS NULL");
                                echo $qry->num_rows;
                            }
                            ?>
                        </h3>
                        <p>Total Jobs Not Done</p>
                    </div>
                    <div class="icon metric-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            
               <div class="col-12">
                <div class="small-box bg-light shadow-sm border metric-card metric-info">
                    <div class="inner">
                        <h3>
                            <?php 
                            if ($_SESSION['login_type'] == 2) {
                                $qry = $conn->query(" SELECT
                                        client.*,
                                        title,
                                        office,
                                        GROUP_CONCAT(DISTINCT CONCAT('(', client_rep.REP_NAME, ')') ORDER BY client_rep.REP_NAME ASC) AS reps
                                    FROM yasccoza_openlink_market.client
                                    LEFT JOIN yasccoza_openlink_association_db.industry_title
                                        ON client.industry_id = industry_title.TITLE_ID
                                    LEFT JOIN yasccoza_openlink_association_db.industry
                                        ON client.office_id = industry.INDUSTRY_ID
                                    LEFT JOIN client_rep
                                        ON client_rep.CLIENT_ID = yasccoza_openlink_market.client.CLIENT_ID
                                    WHERE yasccoza_openlink_market.client.creator_id = {$_SESSION['login_id']}
                                    GROUP BY client.CLIENT_ID, title, office");
                                echo $qry->num_rows;
                            } elseif($_SESSION['login_type'] == 3){
                                $qry = $conn->query(" SELECT
                                client.*,
                                title,
                                office,
                                    GROUP_CONCAT(DISTINCT CONCAT('(', client_rep.REP_NAME, ')') ORDER BY client_rep.REP_NAME ASC) AS reps
                                FROM yasccoza_openlink_market.client
                                LEFT JOIN yasccoza_openlink_association_db.industry_title
                                    ON client.industry_id = industry_title.TITLE_ID
                                LEFT JOIN yasccoza_openlink_association_db.industry
                                    ON client.office_id = industry.INDUSTRY_ID
                                LEFT JOIN client_rep
                                    ON client_rep.CLIENT_ID = yasccoza_openlink_market.client.CLIENT_ID
                                 LEFT JOIN users
                                    ON users.creator_id = yasccoza_openlink_market.client.creator_id
                                WHERE users.id ={$_SESSION['login_id']}
                                GROUP BY client.CLIENT_ID, title, office;");
                                echo $qry->num_rows;
                            } else {
                                $qry = $conn->query("SELECT project_list.* FROM project_list WHERE Job_Done IS NULL");
                                echo $qry->num_rows;
                            }
                            ?>
                        </h3>
                        <p>Account Serviced</p>
                    </div>
                    <div class="icon metric-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <!-- Job Types -->
          

            <!-- Other stats -->
            <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 4) : ?>
         
                <div class="col-12">
                    <div class="small-box bg-light shadow-sm border metric-card metric-extra">
                        <div class="inner">
                            <h3>
                                <?php $qry2 = $conn->query("SELECT * FROM yasccoza_openlink_market.client"); echo $qry2->num_rows; ?>
                                <br><p>Total Clients in the system</p>
                            </h3>
                        </div>
                        <div class="icon metric-icon">
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>



<!-- JavaScript -->
<script>
    $(document).ready(function(){
        $('#list').dataTable();

        $('.view_user').click(function(){
            uni_modal("<i class='fa fa-id-card'></i> User Details", "view_user.php?id=" + $(this).attr('data-id'));
        });

        $('.delete_user').click(function(){
            _conf("Are you sure to delete this user?", "delete_user", [$(this).attr('data-id')]);
        });
    });

    function delete_user(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_user',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
