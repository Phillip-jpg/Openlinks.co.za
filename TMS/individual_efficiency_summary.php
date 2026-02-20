<?php include'db_connect.php' ?>
<div class="col-lg-12">
		<div class="card card-outline card-success shadow-sm">
		<div class="card-header bg-primary text-white">
			<div class="card-tools">
				
			</div>
           
		</div>
		<div class="card-body">
             <br>
             	<div class="table-responsive">
				<table class="table tabe-hover table-condensed" id="list">
			<colgroup>
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead style="background-color:#032033 !important; color:white">
					<tr>
					   <th >Work Type</th>
						<th >Assigned to</th>
						<th>Done</th>
						<th>WIP Ratio</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php


                $qry2 = $conn->query("
                    SELECT
                        ad.project_id,
                        pl.name AS project_name,
                        pl.start_date,
                        pl.Job_Done,
                        ad.Done_Date,
                        tl.end_time, 
                        tl.start_time,
                        DATEDIFF(pl.end_date, pl.start_date) AS difference_in_days,
                        DATEDIFF(pl.end_date, pl.start_date) * 24 * 60 AS Experiation_Min,
                        CONCAT(u.firstname, ' ', u.lastname) AS member,
                        up.name AS activity_name,
                        CONCAT(
                                      FLOOR(TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)) / 3600),
                                      '.',
                                      LPAD(FLOOR(MOD(TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)), 3600) / 60), 2, '0')
                                    ) AS work_hours,
                                    TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)) / 60 * up.duration AS duration_worktype,
                        up.duration,
                        tl.target,
                        tl.task_name,
                        tl.id AS worktype_id,
                        c.company_name,
                        (
                            SELECT COUNT(*)
                            FROM user_productivity upr
                            WHERE upr.task_id = tl.id
                        ) AS activity
                    FROM assigned_duties ad
                    JOIN user_productivity up ON up.id = ad.activity_id
                    JOIN users u ON u.id = ad.user_id
                    JOIN project_list pl ON pl.id = ad.project_id
                    JOIN task_list tl ON tl.id = ad.task_id
                    LEFT JOIN yasccoza_openlink_market.client c ON c.CLIENT_ID = ad.CLIENT_ID
                    WHERE ad.user_id = {$_SESSION['login_id']}
                   
                ");

                // Initialize aggregates
                $sumoftotal = 0;
                $Experirysum = 0;
                $countofpost = 0;
                $donecount = 0;
                $timespent = 0;
                $target = 0;
                $perfect_utilisation = 0;
                $took_longer = 0;
                $still_counting = 0;
                $not_fully_utlisated = 0;
                
                while ($row = $qry2->fetch_assoc()) {
                    $total = $row['activity'] * $row['target'];
                    $workhours = $row['work_hours'];
                    $member = $row['member'];
                
                    if (!empty($row['target']) && $row['target'] != 0) {
                        $sumoftotal += $row['duration_worktype'] / $row['target'];
                        $Experirysum += $row['Experiation_Min'] / $row['target'];
                    }
                
                    $countofpost++;
                
                    if (!empty($row['Done_Date'])) {
                        $donecount++;
                    }
                
                    if (!empty($row['target'])) {
                        $target += $row['target'];
                    }
                    
                                    $work_start_hour = $row['start_time'];
                                    $work_end_hour = $row['end_time'];
                                    
                                    $time_spend_on_job="No time";
                                    
                                    if (empty($row['Job_Done']) || $row['Job_Done'] == '0000-00-00 00:00:00') {
                                        
                                        $time_spend_on_job="Not finished";
                                        
                                    } else {
                                        $start = new DateTime($row['start_date']);
                                        $end = new DateTime($row['Job_Done']);
                                    
                                        if ($start > $end) {
                                            
                                        } else {
                                            $total_hours = 0;
                                            $current = clone $start;
                                    
                                            while ($current->format('Y-m-d') <= $end->format('Y-m-d')) {
                                                $weekday = $current->format('N');
                                                if ($weekday < 6) {
                                                    $work_start = clone $current;
                                                    $work_start->setTime($work_start_hour, 0);
                                    
                                                    $work_end = clone $current;
                                                    $work_end->setTime($work_end_hour, 0);
                                    
                                                    if ($current->format('Y-m-d') == $start->format('Y-m-d')) {
                                                        $actual_start = max($start, $work_start);
                                                        $actual_end = min($end, $work_end);
                                                    } elseif ($current->format('Y-m-d') == $end->format('Y-m-d')) {
                                                        $actual_start = $work_start;
                                                        $actual_end = min($end, $work_end);
                                                    } else {
                                                        $actual_start = $work_start;
                                                        $actual_end = $work_end;
                                                    }
                                    
                                                    $interval = $actual_end->getTimestamp() - $actual_start->getTimestamp();
                                                    $hours = max(0, $interval / 3600);
                                                    $total_hours += $hours;
                                                }
                                                $current->modify('+1 day');
                                            }
                                    
                                            $time_spend_on_job = round($total_hours * 60);
                                            $timespent += $time_spend_on_job;
                                        }
                                    }
                                    
                                    $time_status = $time_spend_on_job / ($row['duration_worktype'] / $row['target']);
                                if ($time_status >= 0.76 && $time_status <= 1.2) {
                                    $perfect_utilisation++;
                                } elseif ($time_status >= 1.3) {
                                    $took_longer++;
                                } elseif ($time_status < 0.75 && $time_status > 0.01) {
                                    $not_fully_utlisated++;
                                } elseif ($time_spend_on_job="Not finished"){
                                    $still_counting++;
                                }
                                }
                                
                            $qry = $conn->query("SELECT
                                        tl.task_name,
                                        tl.id AS worktype_id,
                                        COUNT(ad.project_id) AS project_count,
                                        COUNT(CASE WHEN ad.Done_Date IS NOT NULL THEN 1 END) AS completed_count,
                                        COUNT(ad.project_id) - COUNT(CASE WHEN ad.Done_Date IS NOT NULL THEN 1 END) AS wip_count
                                    FROM
                                        assigned_duties ad
                                    JOIN
                                        task_list tl ON tl.id = ad.task_id
                                    JOIN
                                        project_list pl ON pl.id = ad.project_id
                                    WHERE
                                        ad.user_id = {$_SESSION['login_id']}
                                    GROUP BY
                                        tl.task_name, tl.id;
                                        
                        ");
                      
					while ($row = $qry->fetch_assoc()):
					?>
					<tr>
					    <td>
							<p><?php echo ucwords($row['task_name']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['project_count']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['completed_count']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['wip_count']) ?></p>
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=individual_worktype_detailed&worktype_id=<?php echo $row['worktype_id'] ?>">View</a>
		                      
						
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
				<div class="container mt-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4>Resource: <?php echo $member ?></h4>
                            <hr style="border:2px solid white">
                             <h4>Summary of resource: Work Areas</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p>Total Minutes by duration of work type: <span style="font-weight:bold"><?php echo number_format($sumoftotal, 2); ?></span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Expiry  sum of activity : <span style="font-weight:bold"> <?php echo number_format($Experirysum, 2); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p>Total Assigned Activities : <span style="font-weight:bold"> <?php echo number_format($countofpost); ?></span></p>
                                </div>
                              </div>
                              <div class="row">     
                              <div class="col-md-4">
                                    <p >Total Done Activities :<span style="font-weight:bold"> <?php echo number_format($donecount); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p >Sum of actual time :<span style="font-weight:bold"> <?php echo number_format($timespent); ?></span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Average time to complete activity : <span style="font-weight:bold"> <?php echo number_format($timespent/$donecount); ?></span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p> WIP : <span style="font-weight:bold"> <?php echo number_format($countofpost-$donecount); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p >Member of Contribution to Target : <span style="font-weight:bold"> <?php echo number_format($countofpost/($target/$countofpost)*100,2); ?>%</span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Target Sequential Throughput : <span style="font-weight:bold"> <?php echo number_format($donecount/$sumoftotal,5); ?></span></p>
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p>Sequential Throughput: <span style="font-weight:bold"> <?php echo number_format($donecount/$timespent,7); ?> <span style=""></span>
                                <span><br></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p>Actual Vs Target Sequence throughput performance: <span style="font-weight:bold"> <?php echo number_format(($donecount/$timespent)/($donecount/$sumoftotal),5) *100; ?>%</span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Concurrent Throughput : <span style="font-weight:bold"> <?php echo number_format(($countofpost-$donecount)/($timespent/$donecount),5); ?></span></p>
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p>Expected Concurrent Throughput  : <span style="font-weight:bold"> <?php echo number_format(($countofpost-$donecount)/($sumoftotal/$countofpost),5); ?></span></p>
                                </div>
                           
                            <div class="col-md-4">
                                    <p>
                                    Actual Vs Target Concurrent Throughput performance:  <span style="font-weight:bold">
                                    <?php
                                    $actualThroughput = ($countofpost - $donecount) / ($timespent / $donecount);
                                    $targetThroughput = ($countofpost - $donecount) / ($sumoftotal / $countofpost);
                                    $performanceRatio = $actualThroughput / $targetThroughput;
                                    echo number_format($performanceRatio, 5)*100;
                                    ?>
                               %</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                                    <p>
                                    Minimum expected progress per day:  <span style="font-weight:bold">
                                    <?php
                                    $output =  $sumoftotal / 60;
                                    $finaloutput = $output/7 ;
                                    $minimum_expected_progress = $countofpost / $finaloutput;
                                    echo number_format($minimum_expected_progress,3);
                                    ?>
                               </span>
                            </p>
                        </div>
                         </div>
                        </div>
                      <div class="card-header" style="background-color: #032033; color: white; padding: 20px; border-radius: 10px; text-align: center;">
                            <?php $totalz = $perfect_utilisation + $fully_utlisated + $took_longer + $still_counting; ?>
                        
                            <h2 style="margin-bottom: 10px;">
                                Time Utilization Status Total: <?php echo $totalz; ?> --> 100%
                            </h2>
                            <hr style="border: 1px solid white; margin: 10px 0;">
                            <p style="margin: 10px 0; font-size: 16px;">
                                ✅ Perfect utilization: <strong><?php echo number_format($perfect_utilisation); ?></strong>
                                --> <span><?php echo number_format(($perfect_utilisation / $totalz) * 100); ?>%</span>
                            </p>
                        
                            <p style="margin: 10px 0; font-size: 16px;">
                                ⚠️ Time was not fully used: <strong><?php echo number_format($fully_utlisated); ?></strong>
                                --> <span><?php echo number_format(($fully_utlisated / $totalz) * 100); ?>%</span>
                            </p>
                        
                            <p style="margin: 10px 0; font-size: 16px;">
                                🕒 Task took longer than expected: <strong><?php echo number_format($took_longer); ?></strong>
                                --> <span><?php echo number_format(($took_longer / $totalz) * 100); ?>%</span>
                            </p>
                        
                            <p style="margin: 10px 0; font-size: 16px;">
                                ⏳ Time still Counting: <strong><?php echo number_format($still_counting); ?></strong>
                                --> <span><?php echo number_format(($still_counting / $totalz) * 100); ?>%</span>
                            </p>
                        </div>
                    </div>
                </div>
			</table>
		</div>
		</div>
	</div>
</div>
<style>
	table p{
		margin: unset !important;
	}
	table td{
		vertical-align: middle !important
	}
</style>
<script>
$(document).ready(function(){
    // Initialize DataTable
    var dataTable = $('#list').DataTable();

    // Event listener for each filter dropdown
    $('#jobtype-filter').change(function(){
        filterTable();
    });
    
    $('#month-filter').change(function(){
        filterTable();
    });

    $('#created-filter').change(function(){
        filterTable();
    });

    $('#assigned-filter').change(function(){
        filterTable();
    });

    $('#status-filter').change(function(){
        filterTable();
    });

    // Function to filter the DataTable
    function filterTable() {
        var selectedJobType = $('#jobtype-filter').val();
        var selectedmonth = $('#month-filter').val();
        var selectedCreator = $('#created-filter').val();
        var selectedAssigned = $('#assigned-filter').val();
        var selectedStatus = $('#status-filter').val();

        // Apply filter for each column:
        dataTable.column(2).search(selectedJobType) 
         dataTable.column(3).search(selectedmonth) /// Job Type filter on 3rd column (index 2)
            .column(4).search(selectedCreator)      // Who Created it filter on 5th column (index 4)
            .column(5).search(selectedAssigned)     // Assigned filter on 6th column (index 5)
            .column(6).search(selectedStatus)       // Status filter on 7th column (index 6)
            .draw();  // Redraw the table with the new filters
    }

    // Handle deletion of projects

});


	$(document).ready(function(){
		$('#list').dataTable()
	
	$('.delete_project').click(function(){
	_conf("Are you sure to delete this job?","delete_project",[$(this).attr('data-id')])
	})
	})
	function delete_project($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_project',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>