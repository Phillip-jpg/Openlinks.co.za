<?php include'db_connect.php'; ?>

<div class="col-lg-12">
   	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
		    	<h4 class="card-title">Job Not Responded to</h4>
            <?php if($_SESSION['login_type'] != 3): ?>
                
            <?php endif; ?>
        </div>
        <div class="card-body">
           <div class="form-row mb-3">
                <!-- Office Filter -->
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
           <div class="col-md-3">
                <label for="client-filter">Filter by Work Type:</label>
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
          <div class="col-md-3">
                <label for="client-filter">Filter by Month:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $month = $conn->query("SELECT DISTINCT month
FROM working_week_periods
");
                    while($month_row = $month->fetch_assoc()):
                    ?>
                        <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="table-responsive">
                <br>
                			<table class="table table-hover table-bordered table-condensed" id="list">
                <colgroup>
                    <col width="10%">
                     <col width="10%">
                    <col width="30%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>
               <thead style="background-color:#032033 !important; color:white">
                    <tr>
                        <th>POST_ID</th>
                            <th>Month</th>
                        <th>Name</th>
                        <th>Client</th>
                        <th>Work Type</th>
                        <th>Created</th>
                        <th>EXPIRY</th>
                          <th>Action</th>
                         
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                    $where = "";
                    if ($_SESSION['login_type'] == 2) {
                        $where = " WHERE manager_id = '{$_SESSION['login_id']}' ";
                    } elseif ($_SESSION['login_type'] == 3) {
                        $where = " WHERE CONCAT('[', REPLACE(user_ids, ',', '],['), ']') LIKE '%[{$_SESSION['login_id']}]%' ";
                    }

                    // First query
                    $query1 = $conn->query("
                        SELECT POST_ID
                        FROM (
                            SELECT m.Title, m.EXPIRY, m.Created, m.POST_ID, m.SCORECARD_ID, c.company_name AS Company,
                                   COUNT(DISTINCT CONCAT(sr.USER_ID, sr.COMPANY)) AS TotalResponses
                            FROM yasccoza_openlink_market.market_post m
                            LEFT JOIN yasccoza_openlink_market.client c ON m.CLIENT_ID = c.CLIENT_ID
                            LEFT JOIN yasccoza_openlink_market.scorecard_response sr ON sr.POST_ID = m.POST_ID
                            GROUP BY m.Title, m.EXPIRY, m.Created, m.POST_ID, m.SCORECARD_ID, Company
                            HAVING COUNT(DISTINCT sr.USER_ID) > 0
                        ) AS Subquery
                        GROUP BY Title, EXPIRY, Created, POST_ID, SCORECARD_ID, Company
                    ");

                    $post_ids_query1 = [];
                    while ($row = $query1->fetch_assoc()) {
                        $post_ids_query1[] = $row['POST_ID'];
                    }

                    // Second query
                    $query2 = $conn->query("SELECT POST_ID FROM yasccoza_openlink_market.market_post");

                    $post_ids_query2 = [];
                    while ($row = $query2->fetch_assoc()) {
                        $post_ids_query2[] = $row['POST_ID'];
                    }

                    $non_matching_post_ids = array_diff($post_ids_query2, $post_ids_query1);
            
                  
                  

                    if (!empty($non_matching_post_ids)) {
                        $sanitized_ids = implode(',', array_map('intval', $non_matching_post_ids));
                        
                     
                    
                        // Third query
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
    m.SCORECARD_ID, 
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
ORDER BY 
    m.POST_ID DESC;
                        ");

                        while ($row3 = $query3->fetch_assoc()):
                            $words = explode(' ', $row3['Title']);
                            $shortenedJobName = count($words) >= 2 ? implode(' ', array_slice($words, 0, 9)) . '...' : $row3['Title'];
                    ?>
                            <tr>
                                <td class="text-center" style="color:#007bff; font-weight:bold"><?php echo $row3['POST_ID']; ?></td>
                                <td>
                                    <p style='color:green'><b><?php echo ucwords($row3['Month']); ?></b></p>
                                </td>
                                <td>
                                    <p><b><?php echo ucwords($shortenedJobName); ?></b></p>
                                </td>
                                <td>
                                    <p><b><?php echo ucwords($row3['CLIENT']); ?></b></p>
                                </td>
                                  <td>
                                    <p><b><?php echo ucwords($row3['task_name']); ?></b></p>
                                </td>
                                <td>
                                    <p><b><?php echo ucwords($row3['Created']); ?></b></p>
                                </td>
                                <td>
                                    <p style="color:red"><b><?php echo ucwords($row3['EXPIRY']); ?></b></p>
                                </td>
                                <td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                 Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=view_job&id=<?php echo $row3['POST_ID']  ?>" data-id="<?php echo $row3['POST_ID'] ?>">View</a>
						
		                    </div>
						</td>
                            </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No non-matching POST_IDs found.</td></tr>";
                    }
                    ?>
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
        var dataTable = $('#list').DataTable({
            order: [[0, 'desc']]
        });

        $('#office-filter').change(function(){
            filterTable();
        });

        $('#client-filter').change(function(){
            filterTable();
        });
        
    $('#work-filter').change(function(){
        filterTable();
    });

 $('#month-filter').change(function(){
            filterTable();
        })
        
       function filterTable() {
                var selectedClient = $('#client-filter').val();
                var selectedWorkType = $('#work-filter').val();
                var selectedmonth = $('#month-filter').val();

           dataTable.columns(1).search(selectedmonth).columns(3).search(selectedClient).columns(4).search(selectedWorkType).draw();
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
                if(resp == 1){
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
