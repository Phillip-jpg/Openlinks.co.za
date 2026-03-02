<!-- Navbar -->
<?php ob_start(); ?>
<?php ob_end_flush(); ?>

<?php
include('db_connect.php');

$login_id   = isset($_SESSION['login_id']) ? intval($_SESSION['login_id']) : 0;
$login_type = isset($_SESSION['login_type']) ? intval($_SESSION['login_type']) : 0;

$notif_qry   = null;
$notif_count = 0;

if ($login_id > 0) {

    // PM NOTIFICATIONS
    if ($login_type == 2) {
        $notif_qry = $conn->query("
            SELECT DISTINCT nt.*,
                (SELECT CONCAT(us.firstname,' ',us.lastname) FROM users us WHERE us.id = nt.Member_ID LIMIT 1) AS member,
                (SELECT IF(us.type = 3, 'Member', NULL) FROM users us WHERE us.id = nt.Member_ID LIMIT 1) AS Role,
                (SELECT cl.company_name FROM yasccoza_openlink_market.client cl WHERE cl.CLIENT_ID = nt.Member_ID ORDER BY cl.client_pri_id DESC LIMIT 1) AS orbited_client_name,
                pl.name AS Job_Name,
                rm.reminder_name AS reminder_name,
                up.name AS activity_name,
                c.company_name,
                rc.company_name AS reminder_client_name,
                CONCAT(u1.firstname,' ',u1.lastname) as Manager,
                CONCAT(uc.firstname, ' ', uc.lastname) AS Manager_Created,
                ts.team_name
            FROM pm_notifications nt
            LEFT JOIN project_list pl ON pl.id = nt.Job_ID
            LEFT JOIN reminders rm ON rm.id = nt.Job_ID
            LEFT JOIN users u1 ON u1.id=pl.manager_id
            LEFT JOIN users uc ON uc.id = $login_id
            LEFT JOIN user_productivity up ON up.id = nt.Activity_ID
            LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
            LEFT JOIN yasccoza_openlink_market.client rc ON rc.CLIENT_ID = rm.account
            LEFT JOIN team_schedule ts ON ts.team_id=nt.team_id
            WHERE nt.PM_ID = $login_id
            ORDER BY nt.id DESC
        ");
        $notif_count = $notif_qry->num_rows;
    }

    // MEMBER NOTIFICATIONS
    elseif ($login_type == 3) {
        $notif_qry = $conn->query("
            SELECT DISTINCT nt.*,
                (SELECT CONCAT(us.firstname, ' ', us.lastname) FROM users us WHERE us.id = nt.Member_ID LIMIT 1) AS member,
                pl.name AS Job_Name,
                rm.reminder_name AS reminder_name,
                up.name AS activity_name,
                (SELECT us.email FROM users us WHERE us.id = nt.Member_ID LIMIT 1) AS member_email,
                ts.team_name,
                c.company_name,
                rc.company_name AS reminder_client_name,
                CONCAT(u1.firstname, ' ', u1.lastname) AS Manager,
                CONCAT(uc.firstname, ' ', uc.lastname) AS Manager_Created,
                (SELECT IF(us.type = 3, 'Member', NULL) FROM users us WHERE us.id = nt.Member_ID LIMIT 1) AS Role
            FROM member_notifications nt
            LEFT JOIN team_schedule ts ON ts.team_id = nt.team_id
            LEFT JOIN project_list pl ON pl.id = nt.Job_ID
            LEFT JOIN reminders rm ON rm.id = nt.Job_ID
            LEFT JOIN users u1 ON u1.id = pl.manager_id
            LEFT JOIN users uc ON uc.id = nt.PM_ID
            LEFT JOIN user_productivity up ON up.id = nt.Activity_ID
            LEFT JOIN yasccoza_openlink_market.client c ON pl.CLIENT_ID = c.CLIENT_ID
            LEFT JOIN yasccoza_openlink_market.client rc ON rc.CLIENT_ID = rm.account
            WHERE nt.Member_ID = $login_id
            ORDER BY nt.id DESC;
        ");
        $notif_count = $notif_qry->num_rows;
    }
}
?>

<nav class="main-header navbar navbar-expand navbar-dark" style="background: linear-gradient(135deg, #032033 0%, #04324c 100%);">
    <ul class="navbar-nav">
        <?php if (isset($_SESSION['login_id'])): ?>
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#">
                    <i class="fas fa-bars" style="color:white;"></i>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item d-none d-md-block">
            <a class="nav-link" href="./">
                <!-- <b style="color:white;"><?php echo $_SESSION['system']['name']; ?></b> -->
            </a>
        </li>

        <!-- MOBILE BRAND NAME -->
        <li class="nav-item d-md-none">
            <a class="nav-link" href="./">
                <b style="color:white; font-size: 14px;">
                    <?php
                        $system_name = isset($_SESSION['system']['name']) && $_SESSION['system']['name'] !== ''
                            ? (string)$_SESSION['system']['name']
                            : 'OpenLinks';
                        echo strlen($system_name) > 20 ? substr($system_name, 0, 20).'...' : $system_name;
                    ?>
                </b>
            </a>
        </li>
    </ul>

    <!-- RIGHT NAV -->
    <ul class="navbar-nav ml-auto">
        <ul class="navbar-nav ml-auto">

            <!-- QUICK ACCESS (NEXT TO BELL) -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="quickAccessDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">
                    <i class="fas fa-bolt mr-1"></i>
                    <span class="d-none d-md-inline">Quick Access</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="quickAccessDropdown">
                    <?php if (isset($_SESSION['login_type']) && $_SESSION['login_type'] == 2): ?>
                        <a class="dropdown-item" href="index.php?page=my_teams_progress_calendar">
                            <i class="fas fa-calendar-alt mr-2"></i> Team Progress Calendar
                        </a>
                        <a class="dropdown-item" href="index.php?page=my_team_jobs_to_manage">
                            <i class="fas fa-tasks mr-2"></i> My Team Jobs To Manage
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="index.php?page=my_progress_calendar">
                            <i class="fas fa-calendar-alt mr-2"></i> My Progress Calendar
                        </a>
                        <a class="dropdown-item" href="index.php?page=jobs_to_manage">
                            <i class="fas fa-tasks mr-2"></i> My Jobs To Manage
                        </a>
                    <?php elseif (isset($_SESSION['login_type']) && $_SESSION['login_type'] == 3): ?>
                        <a class="dropdown-item" href="index.php?page=my_progress_calendar">
                            <i class="fas fa-calendar-alt mr-2"></i> My Progress Calendar
                        </a>
                        <a class="dropdown-item" href="index.php?page=jobs_to_manage">
                            <i class="fas fa-tasks mr-2"></i> My Jobs To Manage
                        </a>
                    <?php endif; ?>
                </div>
            </li>

            <!-- NOTIFICATION BELL -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" aria-expanded="false">
                    <i class="far fa-bell" style="color:white;"></i>
                    <?php if ($notif_count > 0): ?>
                        <span class="badge badge-danger navbar-badge"><?php echo $notif_count; ?></span>
                    <?php endif; ?>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notif-box">
                    <span class="dropdown-header"><?php echo $notif_count; ?> Notifications</span>
                    <div class="dropdown-divider"></div>

                    <?php if ($notif_count > 0): ?>
                        <?php while ($row = $notif_qry->fetch_assoc()): ?>
                            <?php
                                $notifText = "";
                                $notifLink = "javascript:void(0)";

                                // TYPE 1 — REQUEST DONE
                                if ($row['Notification_Type'] == 1) {
                                    if ($login_type == 2) {
                                        $notifText = "Member: <b>{$row['member']}</b> requested DONE<br>
                                            Job ID: (<b>{$row['Job_ID']}</b>)<br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Team Assigned: {$row['team_name']}<br>
                                            Activity: {$row['activity_name']}<br>
                                            Client: <b>{$row['company_name']}</b>";
                                        $notifLink = "index.php?page=priority_requests";
                                    }
                                    if ($login_type == 3) {
                                        $notifText = "You <b>{$row['member']}</b> requested DONE<br>
                                            Job ID: <b>{$row['Job_ID']}</b><br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Entity: <b>{$row['Manager']}</b><br>
                                            Team Assigned: <b>{$row['team_name']}</b><br>
                                            Activity: <b>{$row['activity_name']}</b><br>
                                            Client: <b>{$row['company_name']} </b>";
                                        $notifLink = "index.php?page=my_progress";
                                    }
                                }

                                // TYPE 2 DONE APPROVED
                                if ($row['Notification_Type'] == 2) {
                                    if ($login_type == 2) {
                                        $notifText = "You <b>{$row['Manager']}</b> approved DONE for<br>
                                            Member: <b>{$row['member']}</b><br>
                                            Job ID: <b>{$row['Job_ID']}</b><br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Team Assigned: <b> {$row['team_name']}</b><br>
                                            Activity: <b>{$row['activity_name']}</b><br>
                                            Client: <b>{$row['company_name']}</b>";
                                        $notifLink = "index.php?page=team_progress";
                                    }
                                    if ($login_type == 3) {
                                        $notifText = "<b>{$row['member']}</b>, your Activity has been Approved by<br>
                                            Project Manager: <b>{$row['Manager']}</b><br>
                                            Job ID: <b>{$row['Job_ID']}</b><br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Activity: <b>{$row['activity_name']}</b><br>
                                            Client: <b>{$row['company_name']}</b>";
                                        $notifLink = "index.php?page=my_progress";
                                    }
                                }

                                // TYPE 3
                                if ($row['Notification_Type'] == 3) {
                                    if ($login_type == 2) {
                                        $notifText = "A Job has been CREATED<br>
                                            Job ID: <b>{$row['Job_ID']}</b><br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Entity: <b>{$row['Manager']}</b><br>
                                            Client: <b>{$row['company_name']}</b>
                                            Team: <b>{$row['team_name']}</b>";
                                        $notifLink = "index.php?page=Productivity_Pipeline";
                                    }
                                    if ($login_type == 3) {
                                        $notifText = "An Activity has been <b>ASSIGNED</b> to you, <b>{$row['member']}</b><br>
                                            Activity: <b>{$row['activity_name']}</b><br>
                                            Job ID: <b>{$row['Job_ID']}</b><br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Entity: <b>{$row['Manager']}</b><br>
                                            Client: <b>{$row['company_name']}</b>";
                                        $notifLink = "index.php?page=my_progress";
                                    }
                                }

                                // TYPE 4
                                if ($row['Notification_Type'] == 4) {
                                    if ($login_type == 2) {
                                        $notifText = "<b>{$row['Manager']}</b>, a Job has been <b>Closed</b><br>
                                            Job ID: <b>{$row['Job_ID']}</b><br>
                                            Job Name: <b>{$row['Job_Name']}</b><br>
                                            Team Assigned: <b>{$row['team_name']}</b><br>
                                            Client: <b>{$row['company_name']}</b>";
                                        $notifLink = "index.php?page=job_archive";
                                    }
                                }

                                // TYPE 6
                                if ($row['Notification_Type'] == 6) {
                                    if ($login_type == 3) {
                                        $notifText = " Hi <b>{$row['member']} </b> welcome to <b>Openlinks </b> this notifications confirms that your member account has been successfully CREATED on our system and has been assigned to the following <br>
                                            Entity: <b>{$row['Manager_Created']}</b><br>
                                            Your Role: <b>{$row['Role']}</b><br>";
                                    }
                                }

	                                // TYPE 7
	                                if ($row['Notification_Type'] == 7) {
	                                    if ($login_type == 2) {
	                                        $notifText = " Hi <b>{$row['member']} </b> welcome to <b>Openlinks </b> this notifications confirms that your entity account: <b>{$row['Manager_Created']}</b> has been successfully CREATED on our system. <br>";

	                                    }
	                                }

	                                    if ($row['Notification_Type'] == 50) {
		                                    if ($login_type == 2) {
		                                        $managerCreated = !empty($row['Manager_Created']) ? $row['Manager_Created'] : 'Project Manager';
		                                        $memberName = !empty($row['member']) ? $row['member'] : 'A new member';
		                                        $memberRole = !empty($row['Role']) ? $row['Role'] : 'Member';
		                                        $notifText = "Hi <b>{$managerCreated}</b>, a new member has been created for your entity.<br>
		                                            Member: <b>{$memberName}</b><br>
		                                            Thier Role: <b>{$memberRole}</b><br>";
		                                        $notifLink = "index.php?page=user_list";
		                                    }
		                                }

                                // TYPE 8
                                if ($row['Notification_Type'] == 8) {
                                    if ($login_type == 3) {
                                        $notifText = " Hi <b>{$row['member']} </b> this notifications confirms that you have been sucessfully Added to a team the details are as follows <br>
                                            Team: <b>{$row['team_name']}</b><br>
                                            Entity: <b>{$row['Manager_Created']}</b><br>
                                            Your Role: <b>{$row['Role']}</b><br>";
                                    }
                                }

                                // TYPE 9
                                if ($row['Notification_Type'] == 9) {
                                    if ($login_type == 2) {
                                        $notifText = " Hi <b>{$row['Manager_Created']} </b> this notification is to inform you that a team has been successfully CREATED and assigned to support your entity.<br>
                                            Team: <b>{$row['team_name']}</b><br>
                                            Entity: <b>{$row['Manager_Created']}</b><br>";
                                    }
                                }

                                // TYPE 10
                                if ($row['Notification_Type'] == 10) {
                                    if ($login_type == 3) {
                                        $notifText = " Hi <b>{$row['member']} </b> this notification is to inform you that have been Added to <b>{$row['team_name']}</b> and assigned to support your entity <br>
                                            {$row['Manager_Created']} <br/>";
                                    }
                                }

	                                // TYPE 11
	                                if ($row['Notification_Type'] == 11) {
	                                    if ($login_type == 2) {
	                                        $notifText = " Hi <b>{$row['Manager_Created']} </b> this notification is to inform you that a resource has been successfully Added to <b>{$row['team_name']}</b> Team to support your entity.<br>";
	                                    }
	                                }

	                                // TYPE 34 (MEMBER: TEAM SCHEDULED)
	                                if ($row['Notification_Type'] == 34) {
	                                    if ($login_type == 3) {
	                                        $teamName = !empty($row['team_name']) ? $row['team_name'] : 'your team';
	                                        $notifText = "This notification confirms that <b>{$teamName}</b> has been scheduled to work.";
	                                        $notifLink = "index.php?page=my_progress_calendar";
	                                    }
	                                }

	                                // TYPE 35 (PM: TEAM SCHEDULED)
	                                if ($row['Notification_Type'] == 35) {
	                                    if ($login_type == 2) {
	                                        $teamName = !empty($row['team_name']) ? $row['team_name'] : 'your team';
	                                        $notifText = "This notification confirms that <b>{$teamName}</b> has been scheduled to work.";
	                                        $notifLink = "index.php?page=my_teams_progress_calendar";
	                                    }
	                                }

	                                if ($row['Notification_Type'] == 466) {
	                                    if ($login_type == 2) {
	                                        $teamName = !empty($row['team_name']) ? $row['team_name'] : 'your team';
	                                        $jobName = !empty($row['Job_Name']) ? $row['Job_Name'] : 'this job';
	                                        $clientName = !empty($row['company_name']) ? $row['company_name'] : 'N/A';
	                                        $notifText = "This notification confirms that <b>{$teamName}</b> has been fully assigned for the following job.<br>
	                                            Job ID: <b>{$row['Job_ID']}</b><br>
	                                            Job Name: <b>{$jobName}</b><br>
	                                            Client: <b>{$clientName}</b>";
	                                        $notifLink = "index.php?page=Productivity_Pipeline";
	                                    }
	                                }

	                                if ($row['Notification_Type'] == 900) {
	                                    if ($login_type == 2) {
	                                        $clientName = !empty($row['orbited_client_name']) ? $row['orbited_client_name'] : 'N/A';
	                                        $notifText = "This notification confirms that a client has been orbited to your account.<br>
	                                            Client: <b>{$clientName}</b>";
	                                        $notifLink = "index.php?page=client_list";
	                                    }
	                                }

	                                // TYPE 887 (PM: REMINDER SET)
	                                if ($row['Notification_Type'] == 887) {
	                                    if ($login_type == 2) {
	                                        $teamName = !empty($row['team_name']) ? $row['team_name'] : 'your team';
	                                        $clientName = !empty($row['reminder_client_name']) ? $row['reminder_client_name'] : 'N/A';
	                                        $reminderTitle = !empty($row['reminder_name']) ? $row['reminder_name'] : 'Reminder';
	                                        $notifText = "This notification confirms that a reminder has been set.<br>
	                                            Reminder: <b>{$reminderTitle}</b><br>
	                                            Client: <b>{$clientName}</b><br>
	                                            Team: <b>{$teamName}</b>";
	                                        $notifLink = "index.php?page=reminders_list";
	                                    }
	                                }

	                                // TYPE 888 (PM/MEMBER: REMINDER ALERT)
	                                if ($row['Notification_Type'] == 888) {
	                                    $teamName = !empty($row['team_name']) ? $row['team_name'] : 'your team';
	                                    $clientName = !empty($row['reminder_client_name']) ? $row['reminder_client_name'] : 'N/A';
	                                    $reminderTitle = !empty($row['reminder_name']) ? $row['reminder_name'] : 'Reminder';
	                                    $notifText = "Reminder alert triggered.<br>
	                                        Reminder: <b>{$reminderTitle}</b><br>
	                                        Team: <b>{$teamName}</b><br>
	                                        Client: <b>{$clientName}</b>";
	                                    $notifLink = ($login_type == 2)
	                                        ? "index.php?page=reminders_list"
	                                        : "index.php?page=team_reminder";
	                                }

	                                // TYPE 111 (MEMBER: ORBITED)
	                                if ($row['Notification_Type'] == 111) {
	                                    if ($login_type == 3) {
	                                        $entityName = !empty($row['Manager_Created']) ? $row['Manager_Created'] : 'your entity';
	                                        $notifText = "This notification confirms that you have been orbited to support a new entity.<br>
	                                            Entity: <b>{$entityName}</b>";
	                                        $notifLink = "index.php?page=my_progress_calendar";
	                                    }
	                                }

	                                // TYPE 222 (PM: NEW ORBITED MEMBER)
	                                if ($row['Notification_Type'] == 222) {
	                                    if ($login_type == 2) {
	                                        $memberName = !empty($row['member']) ? $row['member'] : 'A member';
	                                        $notifText = "This notification confirms that you have a new orbited member.<br>
	                                            Member: <b>{$memberName}</b>";
	                                        $notifLink = "index.php?page=orbit_member";
	                                    }
	                                }
	                            ?>

                            <!-- Bubble Message -->
                            <div class="notif-bubble" id="notif_<?php echo $row['id']; ?>">
                                <!-- ✅ UPDATED: add data-ntype attribute (NO SQL change) -->
                                <a href="<?php echo $notifLink; ?>"
                                   class="notif-text"
                                   data-ntype="<?php echo (int)$row['Notification_Type']; ?>">
                                    <div>
                                        <?php echo $notifText; ?> <br>
                                        <small class="text-muted">
                                            <?php echo date("M d, Y h:i A", strtotime($row['date'])); ?>
                                        </small>
                                    </div>
                                </a>

                                <!-- Trash Icon Delete -->
                                <i class="fas fa-trash deleteNotifBtn"
                                   data-id="<?php echo $row['id']; ?>"
                                   data-type="<?php echo $login_type; ?>"></i>
                            </div>

                            <div class="dropdown-divider"></div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <span class="dropdown-item text-center text-muted">No notifications</span>
                    <?php endif; ?>
                </div>
            </li>

            <!-- FULL SCREEN -->
            <li class="nav-item d-none d-sm-block">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt" style="color:white;"></i>
                </a>
            </li>

            <!-- USER DROPDOWN -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <span class="fa fa-user" style="color:white;"></span>
                        <span class="d-none d-md-inline" style="color:white;">
                            <b>
                                &nbsp;<?php
                                    $firstName = ucwords($_SESSION['login_firstname']);
                                    echo strlen($firstName) > 10 ? substr($firstName, 0, 10).'...' : $firstName;
                                ?>
                            </b>
                        </span>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <!-- <a class="dropdown-item" id="manage_account">
                        <i class="fa fa-cog"></i> Manage Account
                    </a> -->

                    <!-- ✅ Logout via POST + CSRF -->
                    <form id="logoutForm" action="ajax.php?action=logout" method="POST" style="display:none;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    </form>

                    <a class="dropdown-item" href="#" id="logoutBtn">
                        <i class="fa fa-power-off"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </ul>
</nav>

<style>
/* ===== MODERN NOTIFICATION STYLES ===== */
.notif-box {
    width: 400px !important;
    max-height: 480px;
    overflow-y: auto;
    border-radius: 12px !important;
    padding: 0 !important;
    border: 1px solid #e0e6ed !important;
    box-shadow: 0 10px 40px rgba(3, 32, 51, 0.15) !important;
    background: #ffffff !important;
}

/* Notification header - Light color complementing #032033 */
.notif-box .dropdown-header {
    background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
    color: #032033;
    font-weight: 600;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
    font-size: 14px;
    letter-spacing: 0.3px;
    border-bottom: 1px solid #e0e6ed;
}

/* Divider styling */
.notif-box .dropdown-divider {
    margin: 0;
    border-color: #f0f7ff;
}

/* Empty state */
.dropdown-item.text-center {
    padding: 40px 20px;
    color: #8a94a6 !important;
    font-size: 14px;
    background: transparent !important;
}

/* ===== MODERN NOTIFICATION BUBBLE ===== */
.notif-bubble {
    background: #ffffff;
    padding: 16px 20px;
    margin: 0;
    border-bottom: 1px solid #f8fafc;
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

/* Notification indicator dot for unread */
.notif-bubble::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 20px;
    width: 8px;
    height: 8px;
    background: linear-gradient(135deg, #4a90e2 0%, #032033 100%);
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
    box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
}
.notif-bubble:hover::before { opacity: 1; }

/* Hover effect */
.notif-bubble:hover {
    background: #f8fafc;
    transform: translateX(4px);
    border-left: 3px solid #4a90e2;
}

/* Notification content container */
.notif-text {
    text-decoration: none;
    color: #2d3748;
    flex: 1;
    font-size: 13px;
    line-height: 1.5;
}
.notif-text b { color: #032033; font-weight: 600; }
.notif-text small.text-muted {
    color: #8a94a6 !important;
    font-size: 11px;
    margin-top: 4px;
    display: block;
}

/* Status badges for notification types */
.notif-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    background: rgba(3, 32, 51, 0.1);
    color: #032033;
}

/* Existing types */
.notif-badge.type-1 { /* Member Creation */
    background: linear-gradient(135deg, #e6fffa 0%, #b2f5ea 100%);
    color: #234e52;
    border: 1px solid #81e6d9;
}
.notif-badge.type-2 { /* Team Creation */
    background: linear-gradient(135deg, #faf5ff 0%, #e9d8fd 100%);
    color: #44337a;
    border: 1px solid #9f7aea;
}
.notif-badge.type-3 { /* Member Added to Team */
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
    color: #2a4365;
    border: 1px solid #63b3ed;
}
.notif-badge.type-4 { /* Assigned to Work */
    background: linear-gradient(135deg, #edf2f7 0%, #e2e8f0 100%);
    color: #1a202c;
    border: 1px solid #a0aec0;
}

/* ✅ Added types for your new labels */
.notif-badge.type-5 { /* Request for Done */
    background: linear-gradient(135deg, #fffaf0 0%, #feebc8 100%);
    color: #7b341e;
    border: 1px solid #f6ad55;
}
.notif-badge.type-6 { /* Approved */
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
    color: #22543d;
    border: 1px solid #68d391;
}
.notif-badge.type-7 { /* Job Completion */
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    color: #742a2a;
    border: 1px solid #fc8181;
}
.notif-badge.type-8 { /* Team Assigned */
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
    color: #22543d;
    border: 1px solid #68d391;
}
.notif-badge.type-14 { /* Fully Assigned */
    background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    color: #9a3412;
    border: 1px solid #fb923c;
}
.notif-badge.type-15 { /* Account Orbited */
    background: linear-gradient(135deg, #fffaf0 0%, #feebc8 100%);
    color: #9c4221;
    border: 1px solid #f6ad55;
}
.notif-badge.type-16 { /* Reminder Set */
    background: linear-gradient(135deg, #e8fff5 0%, #c6f6d5 100%);
    color: #1f5132;
    border: 1px solid #68d391;
}
.notif-badge.type-17 { /* Reminder Alert */
    background: linear-gradient(135deg, #fff7e6 0%, #fde68a 100%);
    color: #7c2d12;
    border: 1px solid #f59e0b;
}
.notif-badge.type-13 { /* Orbit Notification */
    background: linear-gradient(135deg, #eef2ff 0%, #c7d2fe 100%);
    color: #312e81;
    border: 1px solid #818cf8;
}

/* Delete button */
.deleteNotifBtn {
    color: #cbd5e0;
    cursor: pointer;
    font-size: 14px;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.2s ease;
    opacity: 0;
    transform: translateX(10px);
    background: transparent;
    border: none;
}
.notif-bubble:hover .deleteNotifBtn {
    opacity: 1;
    transform: translateX(0);
}
.deleteNotifBtn:hover {
    color: #f56565;
    background: #fed7d7;
}

/* Bell icon animation */
@keyframes bell-ring {
    0% { transform: rotate(0); }
    10% { transform: rotate(15deg); }
    20% { transform: rotate(-10deg); }
    30% { transform: rotate(10deg); }
    40% { transform: rotate(-5deg); }
    50% { transform: rotate(5deg); }
    60% { transform: rotate(0); }
    100% { transform: rotate(0); }
}
.fa-bell {
    position: relative;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
}
.fa-bell.has-notifications {
    animation: bell-ring 0.5s ease;
    background: rgba(255, 255, 255, 0.2);
}

/* Badge styling */
.navbar-badge {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 10px;
    padding: 2px 5px;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
    border: 2px solid #032033;
    font-weight: 700;
    box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
}

/* Scrollbar styling */
.notif-box::-webkit-scrollbar { width: 6px; }
.notif-box::-webkit-scrollbar-track { background: #f1f3f6; border-radius: 3px; }
.notif-box::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 3px; }
.notif-box::-webkit-scrollbar-thumb:hover { background: #a0aec0; }

/* ===== NAVBAR ENHANCEMENTS ===== */
.main-header.navbar {
    box-shadow: 0 2px 20px rgba(3, 32, 51, 0.2);
    background: linear-gradient(135deg, #032033 0%, #04324c 100%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.navbar-nav .nav-link {
    color: white !important;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 0 2px;
    padding: 8px 10px !important;
}
.navbar-nav .nav-link:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-1px);
}
.navbar-nav .nav-link i { color: white !important; }

/* User dropdown */
.navbar-nav .nav-item:last-child .nav-link {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 6px 12px !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.navbar-nav .nav-item:last-child .nav-link:hover { background: rgba(255, 255, 255, 0.2); }

/* Dropdown menu - Light theme to complement #032033 */
.dropdown-menu {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(3, 32, 51, 0.15);
    padding: 8px 0;
    margin-top: 8px;
    background: #ffffff;
    border: 1px solid #e0e6ed;
}
.dropdown-item {
    padding: 12px 20px;
    font-size: 14px;
    color: #2d3748;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 0 8px;
    width: calc(100% - 16px);
    background: transparent;
}
.dropdown-item:hover {
    background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
    color: #032033;
    transform: translateX(4px);
}
.dropdown-item i { width: 20px; margin-right: 10px; color: #4a90e2; }

/* Fullscreen icon animation */
.fa-expand-arrows-alt { transition: transform 0.3s ease; }
.nav-link[data-widget="fullscreen"]:hover .fa-expand-arrows-alt { transform: scale(1.2); }

/* Menu icon animation */
.fa-bars { transition: transform 0.3s ease; }
.nav-link[data-widget="pushmenu"]:hover .fa-bars { transform: rotate(90deg); }

/* Animation for notification appearance */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.notif-bubble { animation: fadeInUp 0.3s ease-out; }

/* Custom hover effect for notification link */
.notif-text:hover { text-decoration: none; }

/* ===================== */
/* RESPONSIVE BREAKPOINTS */
/* ===================== */

/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) {
    .notif-box { width: 400px !important; }
    .navbar-nav .nav-link { padding: 8px 16px !important; margin: 0 4px; }
}

/* Medium devices (tablets, 768px to 991px) */
@media (min-width: 768px) and (max-width: 991px) {
    .notif-box { width: 350px !important; max-height: 400px; }
    .navbar-nav .nav-link { padding: 8px 12px !important; margin: 0 2px; }
    .navbar-brand-text {
        font-size: 14px;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}

/* Small devices (landscape phones, 576px to 767px) */
@media (min-width: 576px) and (max-width: 767px) {
    .notif-box { width: 320px !important; max-height: 350px; right: 10px !important; left: auto !important; }
    .navbar-nav { flex-direction: row; }
    .navbar-nav .nav-item { margin-left: 5px; }
    .navbar-nav .nav-link { padding: 6px 8px !important; font-size: 14px; }
    .notif-bubble { padding: 12px 16px; }
    .notif-text { font-size: 12px; }
    .nav-item.d-none.d-sm-block { display: none !important; }
    .navbar-nav .nav-item:last-child .nav-link { padding: 6px 10px !important; }
}

/* Extra small devices (phones, less than 576px) */
@media (max-width: 575.98px) {
    .notif-box { width: 280px !important; max-height: 300px; right: 5px !important; left: auto !important; margin-top: 5px; }
    .navbar-nav .nav-link { padding: 5px 6px !important; font-size: 13px; margin: 0 1px; }
    .notif-bubble { padding: 10px 12px; flex-direction: column; gap: 8px; }
    .notif-text { font-size: 11px; line-height: 1.4; }
    .notif-text small.text-muted { font-size: 9px; }
    .notif-badge { font-size: 8px; padding: 1px 6px; }
    .nav-item.d-none.d-sm-block { display: none !important; }
    .fa-bell { padding: 6px; font-size: 14px; }
    .navbar-badge { font-size: 8px; padding: 1px 3px; min-width: 14px; height: 14px; border: 1px solid #032033; }
    .navbar-nav .nav-item:last-child .nav-link { padding: 4px 8px !important; border-radius: 8px; }
    .fa-user { font-size: 14px; }
    .navbar-brand-text {
        font-size: 12px;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .fa-bars { font-size: 16px; }
    .notif-box .dropdown-header { padding: 12px 16px; font-size: 12px; }
    .deleteNotifBtn { opacity: 1; transform: translateX(0); font-size: 12px; padding: 6px; align-self: flex-end; }
}

/* Very small devices (less than 375px) */
@media (max-width: 374.98px) {
    .notif-box { width: 250px !important; max-height: 280px; }
    .navbar-nav .nav-link { padding: 4px 5px !important; font-size: 12px; }
    .fa-bell, .fa-user, .fa-bars { font-size: 13px; }
    .notif-text { font-size: 10px; }
    .notif-bubble { padding: 8px 10px; }
    .navbar-nav .nav-item:last-child .nav-link span.d-none.d-md-inline { display: none !important; }
    .dropdown-item { padding: 10px 15px; font-size: 13px; }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .notif-bubble:hover { transform: none; border-left: none; background: #ffffff; }
    .notif-bubble:active { background: #f8fafc; transform: scale(0.98); }
    .deleteNotifBtn { opacity: 1; transform: translateX(0); min-width: 32px; min-height: 32px; }
    .navbar-nav .nav-link:active { background: rgba(255, 255, 255, 0.2); }
}

/* High DPI screens */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .navbar-nav .nav-link i { text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); }
    .notif-box { border: 0.5px solid #e0e6ed; }
}

/* Print styles */
@media print { .navbar { display: none !important; } }
</style>

<script>
$(document).ready(function() {

    
    // Add animation when there are notifications
    <?php if ($notif_count > 0): ?>
        $('.fa-bell').addClass('has-notifications');
        setTimeout(() => { $('.fa-bell').removeClass('has-notifications'); }, 500);
    <?php endif; ?>

    /* ✅ UPDATED BADGE LOGIC:
       - NO SQL change
       - Uses data-ntype (Notification_Type) not text guessing
       - Labels: Member Creation, Team Creation, Member Added to Team, Assigned to Work, Request for Done, Approved, Job Completion
    */
    $('.notif-text').each(function () {
        var $el = $(this);
        var text = $el.html();

        var ntype = parseInt($el.data('ntype'), 10) || 0;
        var badgeType = mapNotifToBadgeType(ntype);

        var badge = '<span class="notif-badge type-' + badgeType + '">' + getTypeLabel(badgeType) + '</span><br>';
        $el.html(badge + text);
    });

    function mapNotifToBadgeType(ntype) {
        // DB Notification_Type seen in your PHP:
        // 1 = Request DONE
        // 2 = Approved DONE
        // 3 = Job created (PM) / Activity assigned (Member)
        // 4 = Job Closed
        // 6 = Member account created
        // 7 = Entity created
        // 8 = Assigned to a team
        // 9 = Team created
        // 10 = Assigned to team + entity
        // 11 = Resource assigned to team
        // 50 = Member created for entity
        // 111 = Member orbited
        // 222 = PM has new orbited member
        // 887 = Reminder set
        // 888 = Reminder alert

        switch (ntype) {
            case 1:  return 5; // Request for Done
            case 2:  return 6; // Approved
            case 4:  return 7; // Job Completion

            case 6:  return 1;  // Member Creation
            case 50: return 1;  // Member Creation
            case 7:  return 12; // Entity Creation

            case 9:  return 2; // Team Creation

            case 8:  return 3; // Member Added to Team
            case 10: return 3; // Member Added to Team

            case 3:  return 4; // Assigned to Work
            case 11: return 3; // Assigned to Work

            case 12: return 12; // Entity Creation
            case 34: return 8;  // Team Assigned
            case 35: return 8;  // Team Assigned
            case 466:return 14; // Fully Assigned
            case 111:return 13; // Orbit Notification
            case 222:return 13; // Orbit Notification
            case 900:return 15; // Account Orbited
            case 887:return 16; // Reminder Set
            case 888:return 17; // Reminder Alert

            default: return 4; // fallback
        }
    }

    function getTypeLabel(type) {
        switch(type) {
            case 1: return 'Member Creation';
            case 2: return 'Team Creation';
            case 3: return 'Member Added to Team';
            case 4: return 'Job Creation';
            case 5: return 'Request for Done';
            case 6: return 'Approved';
            case 7: return 'Job Completion';
            case 8: return 'Team Assigned';
            case 12: return 'Entity Creation';
            case 13: return 'Orbit Notification';
            case 14: return 'Fully Assigned';
            case 15: return 'Account Orbited';
            case 16: return 'Reminder Set';
            case 17: return 'Reminder Alert';
            case 50: return 'Member Creation';
            default: return 'Info';
        }
    }

    // Handle touch events for mobile if ('ontouchstart' in window)
    if ('ontouchstart' in window) {
        $('.notif-bubble').on('touchstart', function() {
            $(this).addClass('touch-active');
        }).on('touchend', function() {
            $(this).removeClass('touch-active');
        });

        // Make delete button always visible on mobile
        if (window.innerWidth <= 576) {
            $('.deleteNotifBtn').css({ 'opacity': '1', 'transform': 'translateX(0)' });
        }
    }
});

// DELETE NOTIFICATION
$(".deleteNotifBtn").click(function(e){
    e.stopPropagation(); // Prevent link navigation
    var id = $(this).data("id");
    var type = $(this).data("type");

    $.ajax({
        url: "delete_notification.php",
        method: "POST",
        data: { id: id, type: type },
        success: function(){
            // Animation then remove
            $("#notif_" + id).fadeOut(300, function(){
                $(this).remove();

                // Check if there are no more notifications
                if ($('.notif-bubble').length === 0) {
                    $('.notif-box').prepend('<span class="dropdown-item text-center text-muted">No notifications</span>');
                }

                // Update badge count without full reload
                var currentCount = parseInt($('.navbar-badge').text());
                if (currentCount > 1) {
                    $('.navbar-badge').text(currentCount - 1);
                } else {
                    $('.navbar-badge').remove();
                }
            });
        }
    });
});

// Handle window resize for responsive adjustments
$(window).on('resize', function() {
    // Adjust delete button visibility based on screen size
    if (window.innerWidth <= 576) {
        $('.deleteNotifBtn').css({ 'opacity': '1', 'transform': 'translateX(0)' });
    } else {
        $('.deleteNotifBtn').css({ 'opacity': '', 'transform': '' });
    }
});
</script>

<script>
$(document).on('click', '#logoutBtn', function(e){
    e.preventDefault();
    // best: submit the hidden form so csrf_token is included automatically
    document.getElementById('logoutForm').submit();
});
</script>
