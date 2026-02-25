

<?php
include('db_connect.php');

$twhere = "";
if ($_SESSION['login_type'] != 1) {
    $twhere = " ";
}
?>

<div class="col-12">
    <div class="card shadow-sm">
        <div class="card-body bg-light">
            <h4>Welcome <?php echo $_SESSION['login_name']; ?>!</h4>
        </div>
    </div>
</div>
<hr>

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
    $query = "SELECT * FROM project_list WHERE manager_id = {$_SESSION['login_id']} ORDER BY date_created DESC LIMIT $itemsPerPage OFFSET $offset";
    $totalQuery = "SELECT COUNT(*) as total FROM project_list WHERE manager_id = {$_SESSION['login_id']}";
} else {
    $query = "SELECT DISTINCT project_list.*
              FROM project_list
              INNER JOIN assigned_duties ON project_list.id = assigned_duties.project_id
              WHERE assigned_duties.user_id = {$_SESSION['login_id']}
              ORDER BY date_created DESC LIMIT $itemsPerPage OFFSET $offset";
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

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5><b>Jobs</b></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-0">
                        <colgroup>
                            <col width="5%">
                            <col width="25%">
                            <col width="15%">
                            <col width="15%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                       	<thead style="background-color:#032033 !important; color:white">
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
                                echo "<td style='color:#428bca;font-weight:bold'>" . $row['id'] . "</td>";
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
                                echo "<td><a class='btn btn-primary btn-sm' href='./index.php?page=view_job&job=" . urlencode($jobToken) . "'><i class='fas fa-folder'></i> View</a></td>";
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
    <div class="col-md-4">
        <div class="row">
            <!-- Total Jobs -->
            <div class="col-12">
                <div class="small-box bg-light shadow-sm border">
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
                    <div class="icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            
             <div class="col-12">
                <div class="small-box bg-light shadow-sm border">
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
                    <div class="icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="small-box bg-light shadow-sm border">
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
                    <div class="icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            
               <div class="col-12">
                <div class="small-box bg-light shadow-sm border">
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
                    <div class="icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <!-- Job Types -->
          

            <!-- Other stats -->
            <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 4) : ?>
         
                <div class="col-12">
                    <div class="small-box bg-light shadow-sm border">
                        <div class="inner">
                            <h3>
                                <?php $qry2 = $conn->query("SELECT * FROM yasccoza_openlink_market.client"); echo $qry2->num_rows; ?>
                                <br><p style="font-weight: normal; font-size: 16px;">Total Clients in the system</p>
                            </h3>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
