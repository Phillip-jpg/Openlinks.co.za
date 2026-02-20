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
						<th >Number of activities </th>
						<th>Count done of Job Done</th>
						<th>WIP Ratio</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php


                    $team_id=$_GET['team_id'];
                    
                    //Forstats

                $qry2 = $conn->query("
                    SELECT DISTINCT
                                    pl.id AS project_id,
                                    ad.user_id,
                                    pl.team_ids,
                                    pl.Job_Done,
                                    ts.team_name,
                                    ad.Done_Date,
                                    pl.name AS project_name,
                                    tl.start_time,
                                    tl.end_time,
                                    tl.task_name AS worktype,
                                    pl.start_date,
                                    pl.end_date,
                                    DATEDIFF(pl.end_date, pl.start_date) AS Duration_by_Expiry_condition, 
                                    DATEDIFF(pl.end_date, pl.start_date) * 24 * 60 AS Experiation_Min,
                                    up.name AS activity,
                                    c.company_name AS CLIENT,
                                    CONCAT(u.firstname, ' ', u.lastname) AS member,
                                    CONCAT(u1.firstname, ' ', u1.lastname) AS manager,
                                    CONCAT(u2.firstname, ' ', u2.lastname) AS operations_manager,
                                    up.duration,
                                    tl.target,
                                    CONCAT(
                                        FLOOR(
                                            TIME_TO_SEC(
                                                TIMEDIFF(tl.end_time, tl.start_time)
                                            ) / 3600
                                        ),
                                        '.',
                                        LPAD(
                                            FLOOR(
                                                MOD(
                                                    TIME_TO_SEC(
                                                        TIMEDIFF(tl.end_time, tl.start_time)
                                                    ),
                                                    3600
                                                ) / 60
                                            ),
                                            2,
                                            '0'
                                        )
                                    ) AS work_hours,
                                    TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)) / 60 * up.duration AS Activity_Duration_in_Min,
                                    TIME_TO_SEC(TIMEDIFF(tl.end_time, tl.start_time)) / 60 * DATEDIFF(pl.end_date, pl.start_date) AS Total_Minutes_by_duration_worktype
                                FROM
                                    project_list pl
                                LEFT JOIN team_schedule ts ON
                                    ts.team_id = pl.team_ids
                                LEFT JOIN assigned_duties ad ON
                                    pl.id = ad.project_id
                                LEFT JOIN users u ON
                                    u.id = ad.user_id
                                LEFT JOIN users u1 ON
                                    u1.id = ts.pm_manager
                                LEFT JOIN users u2 ON
                                    u2.id = ts.op_ids
                                LEFT JOIN task_list tl ON
                                    tl.id = ad.task_id
                                LEFT JOIN user_productivity up ON
                                    up.id = ad.activity_id
                                LEFT JOIN yasccoza_openlink_market.client c
                                ON
                                    c.CLIENT_ID = pl.CLIENT_ID
                                WHERE
                                    pl.team_ids = $team_id
                                ORDER BY
                                    `project_id` ASC;
                        ");
                      $sumoftotal = 0;
                      $Experirysum=0;
                      $countofpost=0;
                      $donecount=0;
                      $timespent=0;
                      $target=0;
                      $perfect_utilisation=0;
                      $took_longer=0;
                      $still_counting=0;
                      $not_fully_utlisated=0;
                      
                      
					while ($row = $qry2->fetch_assoc()){
						$total = $row['activity'] * $row['target'];
                        
                        $workhours=$row['work_hours'];
                        $team_name=$row['team_name'];
                        $worktype=$row['worktype'];
                        
                        $taskname= $row['task_name'];
                        
                        $manager= $row['manager'];
                        $operations_manager=$row['operations_manager'];
                      
                        
                        $Total_Min_by_duration_wt+=$row['Activity_Duration_in_Min'] / $row['target'];
                        
                        $Experiry_sum_of_activity+=$row['Duration_by_Expiry_condition'];


						if ($row['target'] != 0) {
							$sumoftotal += $row['duration_worktype'] / $row['target'];
						}
					
							$Experirysum +=$row['Experiation_Min']/$row['target'];
							
							$countofpost++;
							
							if($row['Done_Date'] != null){
							     $donecount++;
							}
							
							if($row['target'] != null){
							     $target+=$row['target'];
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
                                    
                                     $time_status = $time_spend_on_job / ($row['Activity_Duration_in_Min'] / $row['target']);
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
					
					
					//For Stats
					
					$qry4 = $conn->query("SELECT
                                COUNT(ts.team_members) as member_count
                            FROM
                                team_schedule ts
                            WHERE
                                ts.team_id = $team_id AND ts.status = 1;");
					
					while ($row4 = $qry4->fetch_assoc()){
					
					 $membercount=$row4['member_count'];

					}
					
					$qry3 = $conn->query("SELECT
                            COUNT(DISTINCT(pl.id)) AS total_projects,
                            COUNT(DISTINCT(tl.id)) AS total_tasks,
                            COUNT(*) AS ally,
                            CEILING(SUM(DISTINCT(tl.target))/COUNT(DISTINCT(tl.id))) AS Aimed_Target,
                            ROUND(
                                COUNT(
                                    CASE WHEN pl.assigned = 1 THEN 1
                                END
                            ) * 100.0 / COUNT(pl.id),
                            2
                        ) AS assigned_percentage,
                        ROUND(
                            COUNT(
                                CASE WHEN pl.assigned = 0 THEN 1
                            END
                        ) * 100.0 / COUNT(pl.id),
                        2
                        ) AS not_assigned_percentage
                        FROM
                            project_list pl
                        LEFT JOIN assigned_duties ad ON
                            pl.id = ad.project_id
                        LEFT JOIN task_list tl ON
                            tl.id = ad.task_id
                        LEFT JOIN user_productivity up ON
                            up.id = ad.activity_id
                        WHERE
                            pl.team_ids = $team_id");
					
					while ($row1 = $qry3->fetch_assoc()){
					
					 $total_projects=$row1['total_projects'];
					 $assigned=$row1['assigned_percentage'];
					 $not_assigned=$row1['not_assigned_percentage'];
					 $combined_tasks=$row1['ally'];
					 $Aimed=$row1['Aimed_Target'];
					 
		
					}
					
                    //For table            
                            $qry = $conn->query("SELECT
                                                    sub.worktype_id,
                                                    sub.task_name,
                                                    COUNT(*) AS total_count,
                                                    COUNT(CASE WHEN sub.jobdone IS NOT NULL THEN 1 END) AS done_count,
                                                    COUNT(CASE WHEN sub.jobdone IS NULL THEN 1 END) AS wip_count
                                                FROM
                                                    (
                                                    SELECT DISTINCT
                                                        ad.project_id,
                                                        ad.user_id,
                                                        pl.id as job_id,
                                                        up.id AS upid,
                                                        ad.Done_Date AS done,
                                                        up.name AS upname,
                                                        tl.start_time,
                                                        tl.end_time,
                                                        tl.id AS worktype_id,
                                                        tl.task_name,
                                                        pl.Job_Done as jobdone
                                                    FROM
                                                        project_list pl
                                                    LEFT JOIN assigned_duties ad ON
                                                        pl.id = ad.project_id
                                                    LEFT JOIN task_list tl ON
                                                        tl.id = ad.task_id
                                                    LEFT JOIN user_productivity up ON
                                                        up.id = ad.activity_id
                                                    WHERE
                                                        pl.team_ids = $team_id
                                                ) AS sub
                                                GROUP BY
                                                    sub.worktype_id
                                                ORDER BY
                                                    total_count
                                                DESC;
                                        
                        ");
                      
					while ($row = $qry->fetch_assoc()):
					    
					     $job= $row['total_projects'];
					?>
					<tr>
					    <td>
							<p><?php echo ucwords($row['task_name']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['total_count']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['done_count']) ?></p>
						</td>
						<td>
							<p><?php echo ucwords($row['wip_count']) ?></p>
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" >
		                      <a class="dropdown-item view_project" href="./index.php?page=team_expanded_view&worktype_id=<?php echo $row['worktype_id'] ?>&team_id=<?php echo $team_id ?>">View</a>
		                      
						
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
 <div class="container mt-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4>Team: <?php echo $team_name ?></h4>
                            <hr style="border:2px solid white">
                            <h4>Production Manager: <?php echo $manager ?></h4>
                            <hr style="border:2px solid white">
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p>Total Jobs Created by the Team : <span style="font-weight:bold"><?php echo number_format($total_projects); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p>Assigned Jobs %: <span style="font-weight:bold"><?php echo number_format($assigned); ?></span></p>
                                </div>
                                 <div class="col-md-4">
                                    <p>Not Yet assigned % : <span style="font-weight:bold"> <?php echo number_format($not_assigned); ?></span></p>
                                </div>
                            </div>
                                
                            <div class="row">
                                <div class="col-md-4">
                                     <p>Combined Tasks and Activities : <span style="font-weight:bold"> <?php echo number_format($combined_tasks); ?></span></p>
                                </div>
                              <div class="col-md-4">
                                    <p >Total Workload Time for Current Activities :<span style="font-weight:bold"> <?php echo number_format($Total_Min_by_duration_wt,2); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                     <p >Team Target Board: What We Aim to Achieve :<span style="font-weight:bold"> <?php echo number_format($Aimed); ?></span></p>
                                </div>
                            </div>
                                
                            <div class="row">
                                  <div class="col-md-4">
                                     <p >Material of Effort in Duration for this Team :<span style="font-weight:bold"> <?php echo number_format(0); ?></span></p>
                                </div>
                            
                                <div class="col-md-4">
                                    <p>  Daily Yardstick: Compete. Contribute. Per member to be Celebrate  : <span style="font-weight:bold"> <?php echo number_format($Aimed/$membercount); ?></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p>  Estimated Duration: Achievable by Peak Performance Standards : <span style="font-weight:bold"> <?php echo number_format($combined_tasks/$Aimed,3); ?></span></p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <p>  Number of Members : <span style="font-weight:bold"> <?php echo number_format($membercount); ?></span></p>
                                </div>
                             </div> 
                                
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