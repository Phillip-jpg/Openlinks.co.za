  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #032033;">
    <div class="dropdown">
   	<a href="./" class="brand-link" style="background-color: #032033;">
        <?php if($_SESSION['login_type'] == 1): ?>
        <?php else: ?>
        <h3 class="text-center p-0 m-0"><b></b></h3>
        <?php endif; ?>
        <img src="opl_logo.png" alt="..." width="100%" style=" height:130px">
    </a>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="sidebar pb-4 mb-4">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="./" class="nav-link nav-home">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <?php if ($_SESSION['login_type'] == 2 ): ?>
            <li class="nav-item">
            <a href="#" class="nav-link nav-configure">
              <i class="nav-icon fas fa-exclamation-triangle"></i>
              <p>
                Priority Jobs
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=priority_requests" class="nav-link nav-priority_requests tree-item">
                  <i class="fas fa-inbox nav-icon"></i>
                  <p>Team Requests</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=priority_jobs_done" class="nav-link nav-priority_jobs_done tree-item">
                  <i class="fas fa-door-open nav-icon"></i>
                  <p>Team Jobs to be closed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=priority_jobs_due" class="nav-link nav-priority_jobs_due tree-item">
                  <i class="fas fa-hourglass-half nav-icon"></i>
                  <p>Team Jobs due soon</p>
                </a>
              </li>
               <li class="nav-item">
                <a href="./index.php?page=my_priority_jobs_due" class="nav-link nav-my_priority_jobs_due tree-item">
                  <i class="fas fa-clock nav-icon"></i>
                  <p>My jobs due soon</p>
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>
           <?php if ($_SESSION['login_type'] == 3): ?>
                <li class="nav-item">
               <a href="./index.php?page=my_priority_jobs_due" class="nav-link nav-my_priority_jobs_due">
                  <i class="fas fa-clock nav-icon"></i>
                  <p>My Jobs Due Soon </p>
              </a>
               </li>
          <?php endif; ?>
            <?php if ($_SESSION['login_type'] == 3): ?>
                <li class="nav-item">
               <a href="./index.php?page=my_progress" class="nav-link nav-my_progress">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>My Progress </p>
              </a>
               </li>
                <li class="nav-item">
               <a href="./index.php?page=my_progress_calendar" class="nav-link nav-my_progress_calendar">
                  <i class="fas fa-calendar nav-icon"></i>
                  <p>My Calendar </p>
              </a>
               </li>
          <?php endif; ?>
          
          <?php if ($_SESSION['login_type'] == 2 ): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-configurer">
              <i class="fas fa-tasks nav-icon"></i>
              <p>
                Progress
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                 <li class="nav-item">
                 <a href="./index.php?page=my_progress" class="nav-link nav-my_progress tree-item">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>My Progress </p>
              </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=all_my_teams_progress" class="nav-link nav-all_my_teams_progress tree-item">
                  <i class="fas fa-users nav-icon"></i>
                  <p>My Team Progress</p>
              </a>
              </li>
             
            </ul>
          </li>
          
           <li class="nav-item">
            <a href="#" class="nav-link nav-configurers">
              <i class="fas fa-calendar nav-icon"></i>
              <p>
                Calendar
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                 <li class="nav-item">
               <a href="./index.php?page=my_progress_calendar" class="nav-link nav-my_progress_calendar tree-item">
                  <i class="fas fa-calendar nav-icon"></i>
                  <p>My Calendar </p>
              </a>
               </li>
              <li class="nav-item">
                <a href="./index.php?page=my_teams_progress_calendar" class="nav-link nav-my_teams_progress_calendar tree-item">
                  <i class="fas fa-users nav-icon"></i>
                  <p>My Team Calendar</p>
              </a>
              </li>
             
            </ul>
          </li>
          <?php endif; ?>
          
              <?php if ($_SESSION['login_type'] == 2 ): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-configurer">
              <i class="fas fa-clipboard-check nav-icon"></i>
              <p>
                All Production Oversight
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                 <li class="nav-item">
                 <a href="./index.php?page=jobs_to_manage" class="nav-link nav-jobs_to_manage tree-item">
                  <i class="fas fa-tasks nav-icon"></i>
                  <p>Hands-On <br>Team Participation</p>
              </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=my_team_jobs_to_manage" class="nav-link nav-my_team_jobs_to_manage tree-item">
                  <i class="fas fa-users nav-icon"></i>
                  <p>All Work Assigned to <br> Various Service Teams</p>
              </a>
              </li>
             
            </ul>
          </li>
          <?php endif; ?>
          
          
          </li>
          <?php if ($_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 3): ?>
          <li class="nav-item">
            <a href="./index.php?page=Productivity_Pipeline" class="nav-link nav-Productivity_Pipeline">
                  <i class="fas fa-business-time nav-icon"></i>
                  <p>Productivity Pipeline</p>
              </a>
          </li>
          <?php endif; ?>


           <?php if ($_SESSION['login_type'] == 3): ?>
          <li class="nav-item">
            <a href="./index.php?page=my_teams" class="nav-link nav-my_teams">
                  <i class="fas fa-users nav-icon"></i>
                  <p>My Teams</p>
              </a>
          </li>
          <?php endif; ?>


          <?php if ($_SESSION['login_type'] == 3): ?>
          <li class="nav-item">
            <a href="./index.php?page=my_teams_schedule" class="nav-link nav-my_teams_schedule">
                  <i class="fas fa-calendar-alt nav-icon"></i>
                  <p>My Teams Schedule</p>
              </a>
          </li>
          <?php endif; ?>
          
     <?php if ($_SESSION['login_type'] == 3 ): ?>
          <li class="nav-item">
            <a href="./index.php?page=jobs_to_manage" class="nav-link nav-jobs_to_manage">
                  <i class="fas fa-clipboard-check nav-icon"></i>
                  <p>Jobs to Manage </p>
              </a>
          </li>
    <?php endif; ?>
      
          <?php if ($_SESSION['login_type'] == 3): ?>
          <!--<li class="nav-item">-->
          <!--      <a href="./index.php?page=jobs_assigned" class="nav-link nav-task_list">-->
          <!--        <i class="fas fa-business-time nav-icon"></i>-->
          <!--        <p> Jobs Assigned to Me </p>-->
          <!--    </a>-->
          <!--</li>-->
          <?php endif; ?>
         
          <?php if ($_SESSION['login_type'] == 1): ?>
           <li class="nav-item">
             <a href="#" class="nav-link nav-edit_user">
              <i class="fas fa-tasks nav-icon"></i>
              <p>
               Progress
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            
                <!-- Display these navigation items if the login type is 1 -->
                <li class="nav-item">
                    <a href="./index.php?page=Progress_Calander" class="nav-link nav-Progress_Calander tree-item">
                        <i class="fas fa-angle-right nav-icon"></i>
                        <p>Calendar Progress</p>
                    </a>
                    </li>
                    <li class="nav-item">
                        <a href="./index.php?page=All_progress" class="nav-link nav-All_progress tree-item">
                            <i class="fas fa-angle-right nav-icon"></i>
                            <p>All Progress</p>
                        </a>
                    </li>
                
            </ul>
          </li>
         <?php endif; ?>
          
       
             <?php if($_SESSION['login_type'] == 1): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Jobs  
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=job_list" class="nav-link nav-job_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=jobs_not_responded" class="nav-link nav-jobs_not_responded tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Jobs not Responsed to</p>
                </a>
              </li>
               <li class="nav-item">
                <a href="./index.php?page=filter_job" class="nav-link nav-filter_job tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>filter Jobs</p>
                </a>
              </li>
            </ul>
          </li> 
        <?php endif; ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-configure">
              <i class="nav-icon fas fa-wrench"></i>
              <p>
                Configure
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=work_type" class="nav-link nav-work_type tree-item">
                  <i class="fas fa-briefcase nav-icon"></i>
                  <p>Work Type</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=job_type" class="nav-link nav-job_type tree-item">
                  <i class="fas fa-file-alt nav-icon"></i>
                  <p>Job Type</p>
                </a>
              </li>
          <?php if($_SESSION['login_type'] == 2): ?>
            <li class="nav-item">
                <a href="./index.php?page=reminders" class="nav-link nav-reminders">
                  <i class="fas fa-clock nav-icon"></i>
                  <p>Reminders</p>
                </a>
          </li>
          <?php endif; ?>
               <?php if($_SESSION['login_type'] == 1): ?>
               <li class="nav-item">
                <a href="./index.php?page=score_cards" class="nav-link nav-score_cards tree-item">
                  <i class="fas fa-calculator nav-icon"></i>
                  <p>Score Cards</p>
                </a>
              <li class="nav-item">
                <a href="./index.php?page=configure_list" class="nav-link nav-configure_list tree-item">
                  <i class="fas fa-money-check-alt nav-icon"></i>
                  <p>Claims</p>
                </a>
              </li>
                  <?php endif; ?>
               <?php if($_SESSION['login_type'] == 1): ?>
               <li class="nav-item">
                <a href="./index.php?page=contracts" class="nav-link nav-contracts tree-item">
                  <i class="fas fa-edit nav-icon"></i>
                  <p>Contracts</p>
                </a>
              </li>
             
           <li class="nav-item">
                <a href="./index.php?page=orbit_member" class="nav-link nav-reports">
                  <i class="fas fa-tram nav-icon"></i>
                  <p>Orbit Member</p>
                </a>
              </li>
               <?php endif; ?>
            </ul>
          </li>



          
         <!--<li class="nav-item">-->
         <!--       <a href="./index.php?page=filter_job" class="nav-link nav-filter">-->
         <!--         <i class="fas fa-filter nav-icon"></i>-->
    
         <!--         <p>Filter</p>-->
         <!--       </a>-->
         <!-- </li>-->
						
						
          
          <?php if($_SESSION['login_type'] != 3): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_user">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Admins
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                  <i class="fas fa-angle-righ nav-icon"></i>
                  <p>Add New Admin</p>
                </a>
              </li>
               <?php if($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2 ||$_SESSION['login_type'] == 4 ): ?>
              <li class="nav-item">
                <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-angle-righ nav-icon"></i>
                  <p>Admin List</p>
                </a>
              </li>
                <?php endif; ?>
              <li class="nav-item">
               <a href="./index.php?page=client_offices" class="nav-link nav-client_offices tree-item">
                      <i class="fas fa-angle-righ nav-icon"></i>
                      <p>Assign Account Officer</p>
                    </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        <li class="nav-item">
            <a href="#" class="nav-link nav-edit_client">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>
                Clients
                <i class="right fas fa-angle-left"></i>
                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=client_list" class="nav-link nav-client_list tree-item">
                  <i class="fas fa-angle-righ nav-icon"></i>
                  <p>Client List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=List_client_rep" class="nav-link nav-List_client_rep tree-item">
                  <i class="fas fa-angle-righ nav-icon"></i>
                  <p>Client Rep</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=client_management_lvl_1" class="nav-link nav-client_management_lvl_1 tree-item">
                  <i class="fas fa-angle-righ nav-icon"></i>
                  <p>Client job management</p>
                </a>
              </li>
            </ul>
            <?php if (in_array($_SESSION['login_type'], [1])): ?>
              <li class="nav-item">
                <a href="./index.php?page=filter_claims" class="nav-link nav-filter_claims">
                  <i class="fas fa-money-check-alt nav-icon"></i>
    
                  <p>Process Claims</p>
                </a>
          </li>
          
          
          
            <?php endif; ?>
          <?php if (in_array($_SESSION['login_type'], [1])): ?>  
                <li class="nav-item">
                <a href="./index.php?page=accounts_billing_summary" class="nav-link nav-accounts_billing_summary">
                  <i class="fas fa-wallet nav-icon"></i>
                  
                  <p>Accounts Billing Summary</p>
                </a>
          </li>
           <?php endif; ?>
          <?php if (in_array($_SESSION['login_type'], [1, 2])): ?>
           <li class="nav-item">
                <a href="./index.php?page=schedule_teams_lvl2" class="nav-link nav-schedule_teams_lvl2">
                  <i class="fas fa-user-friends nav-icon"></i>
                  <p>Schedule Teams</p>
                </a>
          </li>
          <?php endif; ?>
          <?php if (in_array($_SESSION['login_type'], [2, 3], true)): ?>
          <li class="nav-item">
                <a href="./index.php?page=team_reminder" class="nav-link nav-team_reminder">
                  <i class="fas fa-bell nav-icon"></i>
                  <p>Team Reminders</p>
                </a>
          </li>
          <?php endif; ?>
          <?php if($_SESSION['login_type'] != 3): ?>
           <li class="nav-item">
                <a href="./index.php?page=reports" class="nav-link nav-reports">
                  <i class="fas fa-chart-line nav-icon"></i>   
                  <p>Reports</p>
                </a>
          </li>
          <?php endif; ?>
          </li>
          <!--  <li class="nav-item">-->
          <!--      <a href="./index.php?page=development_view" class="nav-link nav-development">-->
          <!--        <i class="fas fa-th-list nav-icon"></i>-->
          <!--        <p>Individual Worktype</p>-->
          <!--      </a>-->
          <!--</li>-->
         
        </ul>
      </nav>
    </div>
  </aside>
  <script>
  	$(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
  		var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if(s!='')
        page = page+'_'+s;
  		if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
  			if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
  				$('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
  			}
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

  		}
     
  	})
  </script>
