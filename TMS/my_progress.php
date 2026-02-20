<?php
include('db_connect.php');

if (isset($_SESSION['login_id']) && is_numeric($_SESSION['login_id'])) {
    
    $login_id = $_SESSION['login_id'];

$qry = $conn->query("SELECT DISTINCT
    pl.name AS jobname,
    ad.request_days,
    ad.request_done,
    ad.project_id,
    pl.start_date AS job_start_date,
    pl.end_date AS job_end_date,
    tl.task_name,
    pl.id,
    ad.activity_id,
    ad.manager_id,
    up.name,
    CONCAT(u.firstname, ' ', u.lastname) AS manager,
    ad.duration,
    MONTHNAME(ad.start_date) AS MONTH,
    ad.start_date,
    ad.end_date,
    ad.my_quantities,
    ad.my_comment,
    ad.pm_quantities,
    ad.pm_comment,
    c.company_name,
    ad.days_left,
    ad.status,
    ts.team_name,
    CONCAT(u1.firstname,'',u1.lastname) as ops_manager
FROM assigned_duties ad
LEFT JOIN user_productivity up ON ad.activity_id = up.id
LEFT JOIN task_list tl ON up.task_id = tl.id
LEFT JOIN project_list pl ON ad.project_id = pl.id
LEFT JOIN users u ON ad.manager_id = u.id
LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
LEFT JOIN team_schedule ts ON pl.team_ids = ts.team_id
LEFT JOIN users u1 ON ts.op_ids =u1.id
WHERE ad.user_id = $login_id
AND request_done <> 2
ORDER BY pl.id;
"


);



}



?>

<div class="col-lg-12">
    <a href="index.php?page=my_priority_jobs_due" class="btn btn-primary" style="margin-left:20px">My Jobs Due Soon</a>
  <a href="index.php?page=individual_efficiency_summary" style="margin-left:20px" class="btn btn-primary" >Individual Efficiency Summary</a>
  <a href="index.php?page=my_wallet" class="btn btn-primary" style="margin-left:20px" >My Wallet</a>
  <br>
  <br>
    <div class="d-flex gap-3 flex-wrap">

</div>
    	<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
                <b>My Progress</b>
            </div>
<div class="card-body">
<div class="form-row mb-3">
		    <div class="col-md-3">
                <label for="month-filter">Filter by Month:</label>
                <select id="month-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $month_qry = $conn->query("SELECT DISTINCT wwp.month
FROM project_list pl, working_week_periods wwp WHERE wwp.start_week>= pl.date_created AND wwp.end_week>=pl.date_created");
                    while($month_row = $month_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $month_row['month']; ?>"><?php echo $month_row['month']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="col-md-3">
                <label for="created-filter">Filter by Client:</label>
                <select id="created-filter" class="form-control">
                    <option value="">All</option> 
                    <?php
                    $company_qry = $conn->query("SELECT DISTINCT c.company_name FROM yasccoza_openlink_market.client c, project_list pl WHERE c.CLIENT_ID = pl.CLIENT_ID");
                    while($company_row = $company_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $company_row['company_name']; ?>"><?php echo $company_row['company_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
          

             <div class="col-md-3">
                <label for="status-filter">Filter by Status:</label>
                <select id="status-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $status_qry = $conn->query("SELECT DISTINCT status FROM `assigned_duties`");
                    while($status_row = $status_qry->fetch_assoc()):
                    ?>
                        <option value="<?php echo $status_row['status']; ?>"><?php echo $status_row['status']; ?></option>
                      
                    <?php endwhile; ?>
               
                </select>
            </div>
              <div class="col-md-3">
                <label for="Days-filter">Filter by Days Requested:</label>
                <select id="Days-filter" class="form-control">
                    <option value="">All</option>
                    <?php
                    $days_qry = $conn->query("SELECT DISTINCT 
    CASE
        WHEN request_days = 1 THEN 'Requested'
        WHEN request_days = 2 THEN 'Job Complete'
        WHEN request_days = 3 THEN 'Denied'
        WHEN request_days = 5 THEN 'Granted'
    END as status_label
FROM `assigned_duties`

");
                    while($days_row = $days_qry->fetch_assoc()):
    if (!empty($days_row ['status_label'])): // Skip empty values
?>
        <option value="<?php echo $days_row['status_label']; ?>"><?php echo $days_row['status_label']; ?></option>
<?php 
    endif;
endwhile; 
?>

                </select>
            </div>
            <div class="col-md-3">
                <label for="request_done">Filter by Done Requested:</label>
                <select id="request_done" class="form-control">
                    <option value="">All</option>
               <?php
$done_qry = $conn->query("
   SELECT DISTINCT 
    CASE 
        WHEN request_done = 1 THEN 'Requested'
        WHEN request_done = 2 THEN 'Granted'
        WHEN request_done = 3 THEN 'Denied'
    END as done_label
FROM `assigned_duties`
");
while($done_row = $done_qry->fetch_assoc()):
    if (!empty($done_row['done_label'])): // Skip empty values
?>
        <option value="<?php echo $done_row['done_label']; ?>"><?php echo $done_row['done_label']; ?></option>
<?php 
    endif;
endwhile; 
?>


                </select>
            </div>
           
         
               	</div>
               	 <div class="table-responsive">
            	<table class="table table-hover table-bordered table-condensed" id="list">
                        <colgroup>
                            <col width="5%">
                            <col width="5%">
                            <col width="10%">
                            <col width="5%">
                            <col width="5%">
                            <col width="5%">
                            <col width="7%">
                            <col width="1%">
                            <col width="5%">
                            <col width="5%">
                              <col width="5%">
                        </colgroup>
                    		<thead style="background-color:#032033 !important; color:white">
                            <tr>
                                
                                <th style="width:300px ! Important">Job_ID</th>
                                 <th>Month</th>
                                 <th>Team Name</th>
                                  <th>Production Manager</th>
                                    <th>Team leader</th>
                                     <th>Client</th>
                                       <th>Activity</th>
                                        <!--<th>Job</th>-->
                                        <!--<th>Job Start Date</th>-->
                                        <th>Job End Date</th>
                                <!--<th>My Closed Quantity</th>-->
                                <th>PM Closed Quantity </th>
								<!--<th>(working) Days left</th>-->
								<th>Status</th>
								  <th>Request Done</th>
								<!--<th>Request Mored Days</th>-->
                            </tr>
                        </thead>
                        <tbody>
						<?php
                          $i = 1;
                          while ($row = $qry->fetch_assoc()) {
                              $start_date = $row['start_date'];
                              $days_left = $row['days_left'];
                                $end_date = $row['end_date'];

                                // Check if $end_date is a string or a DateTime object
                                if (is_string($end_date)) {
                                    // If $end_date is already a string, use it directly
                                    $end_date_formatted = $end_date;
                                } elseif ($end_date instanceof DateTime) {
                                    // If $end_date is a DateTime object, format it as 'Y-m-d'
                                    $end_date_formatted = $end_date->format('Y-m-d');
                                } 
                              // Create DateTime objects for the specific date and current time
                              $current_time = new DateTime();

                              $start_date_obj = new DateTime($start_date);
                          
                              // Calculate the difference in days (including weekends)
                              $interval = $start_date_obj->diff($current_time);
                              $total_days_difference = $interval->days;
                          
                              // Calculate the number of weekend days within the interval
                              $weekend_days = 0;
                              for ($day = 0; $day <= $total_days_difference; $day++) {
                                  $current_day = clone $start_date_obj;
                                  $current_day->modify("+$day day");
                                  $day_of_week = $current_day->format('N'); // 1 (Monday) to 7 (Sunday)
                                  
                                  // Check if it's a weekend day (Saturday or Sunday)
                                  if ($day_of_week >= 6) {
                                      $weekend_days++;
                                  }
                              }
                          
                              // Calculate the number of business days (excluding weekends)
                              $business_days_difference = $total_days_difference - $weekend_days;


                               // Calculate the end date by adding business days to the start date
                             
                              
                                   $start_day = new DateTime($row['start_date']);
                                   $duration = $row['duration'];
                               
                                   // Calculate the end date based on duration and working days
                                   $end_date = clone $start_day;
                                    
                                   // Add the specified duration in working days
                                   for ($day = 1; $day <= $duration; $day++) {
                                    // Add one day at a time while skipping weekends (Saturday and Sunday)
                                    do {
                                        $end_date->modify("+1 day");
                                    } while ($end_date->format('N') >= 6 && $end_date->format('N') <= 7); // Skip Saturday (6) and Sunday (7)
                                }

                                   $end_date->modify("-1 day");
                                    //$end_date_formatted = $end_date->format('Y-m-d');
                              // Subtract the business days count from days left
                              $days_left -= $business_days_difference;
                          
                          $words = explode(' ', $row['jobname']);
                            
                            // If there are at least two words, display them followed by '...'
                            $shortenedJobName = '';
                            if (count($words) >= 3) {
                                $shortenedJobName = implode(' ', array_slice($words, 0, 3)) . '...';
                            } else {
                                // If there are fewer than two words, just display the original content
                                $shortenedJobName = $row['jobname'];
                            }
                            echo "<tr>";
                            echo "<td style='color:#428bca;font-weight:bold'>" . $row['id'] . "</td>";
                            echo "<td>" . $row['MONTH'] . "</td>";
                            echo "<td style='width:300px !important'>" . $row['team_name'] . "</td>";
                            echo "<td style='width:300px !important'>" . $row['manager'] . "</td>";
                            echo "<td style='width:300px !important'>" . $row['ops_manager'] . "</td>";
                            echo "<td>" . $row['company_name'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            //   echo "<td>" . $shortenedJobName . "</td>";
                                // echo "<td style='width:300px !important'>" . $row['job_start_date'] . "</td>";
                                 echo "<td style='width:300px !important'>" . $row['job_end_date'] . "</td>";
                            // echo "<td style='width:300px !important'>" . $row['my_quantities'] . "</td>";
                            echo "<td style='width:300px !important'>" . $row['pm_quantities'] . "</td>";
                                //  if ($row['status'] == 'Done' || $row['status'] == 'Dropped') {
                                //     echo "<td style='font-weight:bold; text-align: center; color:blue'>Non</td>";
                                // } else {
                                //     echo "<td style='font-weight:bold; text-align: center;'>" . $days_left . "</td>";
                                // }
                              
                            
                            
                            //   echo "<td>" . $row['duration'] . "</td>";
                             
                             
                             


                        echo "<td>";
                              if ($row['status'] == 'Done') {
                                echo '<a href="index.php?page=job_finalised&activity_id=' . $row['activity_id'] . '&project_id=' . $row['project_id'] . '" style="color:white"><span class="badge badge-success">' . $row['status'] . ': View</span></a>';


                            } elseif ($row['status'] == 'On-Hold') {
                                echo "<span class='badge badge-warning'>{$row['status']}</span>";
                            } elseif ($days_left < 0) {
                                echo "<span class='badge badge-danger'>Over Due</span>";
                            } elseif ($days_left == 0) {
                                echo "<span class='badge badge-warning'>Due Today</span>";
                            } elseif ($days_left > 0) {
                                echo "<span class='badge badge-info'>In-progress</span>";
                            }
                             echo "</td>";
                             
                             if ($row['request_done'] == 0) {
                                    echo '<td><button class="badge badge-info" style="border-radius: 5px;"><a href="./index.php?page=job_finalisation&activity_id=' . $row['activity_id'] . '&project_id=' . $row['project_id'] . '&pm_id=' . $row['manager_id'] . '" style="color:white">Request</a></button></td>';
                                } elseif ($row['request_done'] == 1) {
                                    echo "<td><span class='badge badge-warning'>Done Requested</span></td>";
                                } elseif ($row['request_done'] == 2) {
                                    echo "<td><span class='badge badge-success'>Granted</span></td>";
                                } elseif ($row['request_done'] == 3) {
                                    echo "<td><span class='badge badge-danger'>Denied</span></td>";
                                }

                             
                             
                             
                            //   if ($row['request_days']== 0){
                            //     echo '<td>
                            //             <form id="assign-form" method="post" action="./index.php?page=save_request">
                            //               <input type="hidden" name="done" value="000">
                            //                 <input type="hidden" name="login_id" value="' . $login_id . '">
                            //                 <input type="hidden" name="activity_id" value="' . $row['activity_id'] . '">
                            //                 <input type="hidden" name="project_id" value="' . $row['project_id'] . '">
                            //                 <button class="badge badge-info" style="border-radius: 5px;" type="submit">
                            //                     Request
                            //                 </button>
                            //             </form>
                            //           </td>';
                            //   } elseif ($row['request_days']== 1){
                            //      echo "<td><span class='badge badge-warning'>Requested</span></td>";
                            //   } elseif ($row['request_days']== 2){
                            //     echo "<td><span class='badge badge-success'>Job Complete</span></td>";
                            //  }
                            //  elseif ($row['request_days']== 3){
                            //     echo "<td><span class='badge badge-danger'>Denied</span></td>";
                            //  }
                            //   elseif ($row['request_days']== 5){
                            //     echo "<td><span class='badge badge-success'> Granted</span></td>";
                            //  }
                             
                           
                             


               
                              echo "</tr>";
                          }
                        ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

<style>
    table p {
        margin: unset !important;
    }list

    table td {
        
         vertical-align: middle !important;
       
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var dataTable = $('#list').DataTable();

        // Event listener for each filter dropdown
        $('#Days-filter').change(function() {
            filterTable();
        });

        $('#month-filter').change(function() {
            filterTable();
        });
        

        $('#created-filter').change(function() {
            filterTable();
        });

        $('#status-filter').change(function() {
            filterTable();
        });

        $('#request_done').change(function() {
            filterTable();
        });

        // Function to filter the DataTable
        function filterTable() {
            
             
            var selectedMonth = $('#month-filter').val();
            var selectedClient = $('#created-filter').val();
            var selectedStatus = $('#status-filter').val();
            var selectedDays = $('#Days-filter').val();
            var selectedRequestDone = $('#request_done').val();

            // Apply filter for each column:
            dataTable
                .column(1).search(selectedMonth) // Month filter on 2nd column (index 1)
                .column(4).search(selectedClient)
                // Client filter on 5th column (index 4)
                .column(8).search(selectedStatus) // Status filter on 9th column (index 8)
                .column(9).search(selectedDays) // Days Request filter on 10th column (index 9)
                .column(10).search(selectedRequestDone) // Done Request filter on 11th column (index 10)
                .draw(); // Redraw the table with the new filters
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('#list').dataTable();
    });
</script>
