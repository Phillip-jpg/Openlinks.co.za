<?php
if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = htmlspecialchars($_GET['start']);
    $end = htmlspecialchars($_GET['end']);
} else {
    echo "No dates provided.";
    exit();
}
?>

<div class="col-lg-12">
       <div class="card card-outline card-success shadow-sm">
	<div class="card-header bg-primary text-white">
        <?php
        echo "Start Date: " . $start;
        echo "<br>";
        echo "End Date: " . $end;
        ?>
        <div class="card-header">
            <?php if ($_SESSION['login_type'] != 3): ?>
                <!-- You can add content here if needed -->
            <?php endif; ?>
        </div>
                </div>
                        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed" id="list">
                    <colgroup>
                        <col width="10%">
                        <col width="15%">
                        <col width="20%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead style="background-color:#032033 !important; color:white">
                        <tr>
                            <th>POST_ID</th>
                            <th>Month</th>
                            <th>Job Name</th>
                            <th>Client</th>
                            <th>Work Type</th>
                            <th>Created</th>
                            <th>Expiry</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include('db_connect.php');
                        
                         $end .= ' 23:59:59';
                        
                        // First query to get POST_IDs with responses
                        $query1 = $conn->query("
                            SELECT POST_ID
                            FROM (
                                SELECT m.POST_ID
                                FROM yasccoza_openlink_market.market_post m
                                LEFT JOIN yasccoza_openlink_market.client c ON m.CLIENT_ID = c.CLIENT_ID
                                LEFT JOIN yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = m.POST_ID
                                GROUP BY m.POST_ID
                                HAVING COUNT(DISTINCT sr.USER_ID) > 0
                            ) AS Subquery
                        ");
                        
                        $post_ids_query1 = [];
                        while ($row = $query1->fetch_assoc()) {
                            $post_ids_query1[] = $row['POST_ID'];
                        }

                        // Second query to get all POST_IDs
                        $query2 = $conn->query("SELECT POST_ID FROM yasccoza_openlink_market.market_post");
                        
                        $post_ids_query2 = [];
                        while ($row = $query2->fetch_assoc()) {
                            $post_ids_query2[] = $row['POST_ID'];
                        }

                        // Get non-matching POST_IDs
                        $non_matching_post_ids = array_diff($post_ids_query2, $post_ids_query1);

                        if (!empty($non_matching_post_ids)) {
                            $sanitized_ids = implode(',', array_map('intval', $non_matching_post_ids));

                            // Third query to get details of non-responded jobs
                            $query3 = $conn->query("
                                WITH RECURSIVE split_ids AS (
                                    SELECT 
                                        mp.POST_ID AS job_id,
                                        TRIM(SUBSTRING_INDEX(mp.WORKTYPE, ',', 1)) AS task_id,
                                        SUBSTRING(mp.WORKTYPE, LENGTH(SUBSTRING_INDEX(mp.WORKTYPE, ',', 1)) + 2) AS rest_ids
                                    FROM 
                                        yasccoza_openlink_market.market_post mp
                                    WHERE 
                                        mp.WORKTYPE IS NOT NULL
                                    UNION ALL
                                    SELECT 
                                        job_id,
                                        TRIM(SUBSTRING_INDEX(rest_ids, ',', 1)),
                                        SUBSTRING(rest_ids, LENGTH(SUBSTRING_INDEX(rest_ids, ',', 1)) + 2)
                                    FROM 
                                        split_ids
                                    WHERE 
                                        rest_ids <> ''
                                )
                                SELECT DISTINCT
                                    m.Title, 
                                    m.EXPIRY, 
                                    m.Created, 
                                    MONTHNAME(m.Created) AS Month,
                                    m.POST_ID, 
                                    wt.task_name, 
                                    COALESCE(c.company_name, smme.Legal_name) AS CLIENT
                                FROM 
                                    yasccoza_openlink_market.market_post m
                                LEFT JOIN 
                                    yasccoza_openlink_market.client c ON m.CLIENT_ID = c.CLIENT_ID
                                LEFT JOIN 
                                    yasccoza_openlink_smmes.register smme ON m.CLIENT_ID = smme.SMME_ID
                                LEFT JOIN 
                                    yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = m.POST_ID
                                LEFT JOIN 
                                    split_ids si ON m.POST_ID = si.job_id
                                LEFT JOIN 
                                    yasccoza_tms_db.task_list wt ON wt.id = si.task_id
                                WHERE 
                                    m.POST_ID IN ($sanitized_ids)
                                AND m.Created >= '$start' AND m.Created <= '$end'
                                AND m.ASSIGNED_TO='{$_SESSION['login_id']}'
                                ORDER BY 
                                    m.EXPIRY DESC
                            ");

                            while ($row = $query3->fetch_assoc()):
                                $words = explode(' ', $row['Title']);
                                $shortenedJobName = count($words) >= 2 ? implode(' ', array_slice($words, 0, 5)) . '...' : $row['Title'];
                        ?>
                        <tr data-office="<?php echo $row['OFFICE']; ?>" data-client="<?php echo $row['CLIENT']; ?>">
                            <td class="text-center" style="color:red;"><?php echo $row['POST_ID']; ?></td>
                            <td class="text-center" style="color:green; font-weight:bold"><?php echo $row['Month']; ?></td>
                            <td><p><b><?php echo $shortenedJobName; ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['CLIENT']); ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['task_name']); ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['Created']); ?></b></p></td>
                            <td><p><b><?php echo ucwords($row['EXPIRY']); ?></b></p></td>
                            <td>
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Action</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row['POST_ID']; ?>" data-id="<?php echo $row['POST_ID']; ?>">View</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php } ?>
                    </tbody>
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
    $(document).ready(function(){
        var dataTable = $('#list').DataTable();

        $('#office-filter').change(function(){
            filterTable();
        });

        $('#client-filter').change(function(){
            filterTable();
        });
        
        $('#work-filter').change(function(){
            filterTable();
        });

        function filterTable() {
            var selectedOffice = $('#office-filter').val();
            var selectedClient = $('#client-filter').val();
            var selectedWorkType = $('#work-filter').val();

            dataTable.columns(6).search(selectedOffice).columns(7).search(selectedClient).columns(8).search(selectedWorkType).draw();
        }

        $('.delete_project').click(function(){
            _conf("Are you sure to delete this job?", "delete_project", [$(this).attr('data-id')]);
        });
    });

    function delete_project(id){
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_project',
            method: 'POST',
            data: {id: id},
            success: function(resp){
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
