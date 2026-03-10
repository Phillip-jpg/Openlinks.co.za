  <style>
    .modern-sidebar {
      --sidebar-ink: #d7e8ff;
      --sidebar-muted: #8fb6de;
      --sidebar-line: rgba(143, 182, 222, 0.22);
      --sidebar-accent-1: #0b7db5;
      --sidebar-accent-2: #5eb3f3;
      --sidebar-bg-1: #07192b;
      --sidebar-bg-2: #0b2d47;
      --sidebar-bg-3: #103a5a;
      background: linear-gradient(180deg, var(--sidebar-bg-1) 0%, var(--sidebar-bg-2) 56%, var(--sidebar-bg-3) 100%) !important;
      border-right: 1px solid rgba(94, 179, 243, 0.2);
      box-shadow: 14px 0 32px rgba(2, 6, 23, 0.34) !important;
    }

    .modern-sidebar .brand-link {
      align-items: center;
      background: linear-gradient(180deg, rgba(94, 179, 243, 0.08), rgba(94, 179, 243, 0)) !important;
      border-bottom: 1px solid var(--sidebar-line);
      display: flex;
      justify-content: center;
      margin: 0.2rem 0.7rem 0;
      min-height: 104px;
      padding: 0.9rem 0.75rem 0.72rem;
      border-radius: 14px;
    }

    .modern-sidebar .brand-logo-img {
      width: 100%;
      max-width: 180px;
      max-height: 90px;
      object-fit: contain;
      filter: drop-shadow(0 6px 14px rgba(15, 23, 42, 0.36));
    }

    .modern-sidebar .brand-spacer {
      height: 0.95rem;
    }

    .modern-sidebar .sidebar {
      height: calc(100vh - 152px);
      overflow-y: auto;
      padding: 0.35rem 0.5rem 1.3rem;
    }

    .modern-sidebar .sidebar::-webkit-scrollbar {
      width: 5px;
    }

    .modern-sidebar .sidebar::-webkit-scrollbar-track {
      background: rgba(143, 182, 222, 0.16);
      border-radius: 999px;
    }

    .modern-sidebar .sidebar::-webkit-scrollbar-thumb {
      background: rgba(94, 179, 243, 0.52);
      border-radius: 999px;
    }

    .modern-sidebar .nav-sidebar > .nav-item {
      margin-bottom: 0.18rem;
    }

    .modern-sidebar .nav-sidebar .nav-link {
      border-radius: 12px;
      color: var(--sidebar-ink);
      font-size: 0.86rem;
      font-weight: 500;
      margin: 0;
      padding: 0.54rem 0.68rem;
      transition: all 0.2s ease;
    }

    .modern-sidebar .nav-sidebar .nav-link:hover {
      background: rgba(94, 179, 243, 0.16);
      color: #ffffff;
      transform: translateX(2px);
    }

    .modern-sidebar .nav-sidebar .nav-link.active {
      background: linear-gradient(120deg, rgba(11, 125, 181, 0.95), rgba(94, 179, 243, 0.82));
      box-shadow: 0 8px 16px rgba(11, 125, 181, 0.28);
      color: #ffffff !important;
    }

    .modern-sidebar .nav-sidebar .menu-open > .nav-link {
      background: rgba(94, 179, 243, 0.14);
      color: #ffffff;
    }

    .modern-sidebar .nav-sidebar .nav-link p {
      margin: 0;
      line-height: 1.24;
    }

    .modern-sidebar .nav-sidebar .nav-icon {
      color: var(--sidebar-muted);
      font-size: 0.9rem;
      margin-right: 0.18rem;
      text-align: center;
      width: 1.35rem;
    }

    .modern-sidebar .nav-sidebar .nav-link:hover .nav-icon,
    .modern-sidebar .nav-sidebar .nav-link.active .nav-icon {
      color: #ffffff;
    }

    .modern-sidebar .nav-sidebar .nav-treeview {
      border-left: 1px solid var(--sidebar-line);
      margin: 0.25rem 0 0.55rem 0.78rem;
      padding-left: 0.48rem;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-item {
      margin-bottom: 0.16rem;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-link {
      color: #bfd8f8;
      font-size: 0.8rem;
      min-height: 34px;
      padding: 0.42rem 0.58rem;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-link:hover {
      background: rgba(94, 179, 243, 0.14);
      color: #ffffff;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-link.active {
      background: rgba(94, 179, 243, 0.24);
      color: #ffffff;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-icon {
      color: #89bce8;
      font-size: 0.72rem;
      width: 1rem;
    }

    .modern-sidebar .nav-sidebar .nav-link > .right {
      margin-top: 0.16rem;
      color: #92bee7;
    }

    @media (max-width: 991.98px) {
      .modern-sidebar .brand-link {
        min-height: 94px;
      }

      .modern-sidebar .brand-logo-img {
        max-height: 76px;
      }

      .modern-sidebar .sidebar {
        height: calc(100vh - 138px);
      }
    }

    /* Readability overrides */
    .modern-sidebar {
      font-size: 0.98rem;
    }

    .modern-sidebar .nav-sidebar .nav-link {
      font-size: 0.94rem;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-link {
      font-size: 0.89rem;
    }

    .modern-sidebar .nav-sidebar .nav-icon {
      font-size: 1rem;
    }

    .modern-sidebar .nav-sidebar .nav-treeview .nav-icon {
      font-size: 0.85rem;
    }
  </style>

  <aside class="main-sidebar sidebar-dark-primary elevation-4 modern-sidebar">
    <div class="dropdown">
   	<a href="./" class="brand-link">
        <?php if($_SESSION['login_type'] == 1): ?>
        <?php else: ?>
        <h3 class="text-center p-0 m-0"><b></b></h3>
        <?php endif; ?>
        <img src="opl_logo.png" alt="OpenLinks Logo" class="brand-logo-img">
    </a>
    </div>
    <div class="brand-spacer"></div>
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
                <a href="./index.php?page=reminders_list" class="nav-link nav-reminders_list">
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
