<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$loginId = isset($_SESSION['login_id']) ? (int)$_SESSION['login_id'] : 0;
$rows = [];
$errorMessage = '';
$infoMessage = '';

if ($loginId <= 0) {
    $errorMessage = 'Session expired. Please login again.';
} else {
    $tableExists = false;
    $tableCheck = $conn->query("SHOW TABLES LIKE 'reminder_interval'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $tableExists = true;
    }

    if (!$tableExists) {
        $infoMessage = 'No interval reminders have been generated yet.';
    } else {
        $hasTeamId = false;
        $hasResponsible = false;

        $teamColCheck = $conn->query("SHOW COLUMNS FROM reminder_interval LIKE 'team_id'");
        if ($teamColCheck && $teamColCheck->num_rows > 0) {
            $hasTeamId = true;
        }

        $responsibleColCheck = $conn->query("SHOW COLUMNS FROM reminder_interval LIKE 'responsible'");
        if ($responsibleColCheck && $responsibleColCheck->num_rows > 0) {
            $hasResponsible = true;
        }

        $selectTeamId = $hasTeamId ? 'ri.team_id' : 'r.team';
        $selectResponsible = $hasResponsible ? 'ri.responsible' : '0';
        $teamJoinCondition = $hasTeamId ? 'tms.team_id = ri.team_id' : 'tms.team_id = r.team';
        $userJoinCondition = $hasResponsible ? 'u.id = ri.responsible' : '1 = 0';

        $sql = "
            SELECT DISTINCT
                ri.id,
                ri.parent_reminder_id,
                {$selectTeamId} AS team_id,
                {$selectResponsible} AS responsible,
                ri.interval_date,
                ri.trigger_time,
                ri.scheduled_for,
                ri.status AS interval_status,
                ri.sent_at,
                ri.error_message,
                ri.created_at,
                ri.updated_at,
                r.reminder_name,
                r.meeting_day,
                r.meeting_time,
                r.online_meeting,
                r.meeting_link,
                r.who AS parent_who,
                r.status AS parent_status,
                wt.task_name AS work_type_name,
                c.company_name AS account_name,
                c.CLIENT_ID AS account_code,
                cr.REP_NAME AS rep_name,
                CONCAT(COALESCE(u.firstname, ''), ' ', COALESCE(u.lastname, '')) AS responsible_name,
                tms.team_name
            FROM reminder_interval ri
            INNER JOIN reminders r
                ON r.id = ri.parent_reminder_id
            INNER JOIN (
                SELECT team_id, MAX(team_name) AS team_name
                FROM team_schedule
                WHERE team_members = ?
                GROUP BY team_id
            ) tms
                ON {$teamJoinCondition}
            LEFT JOIN users u
                ON {$userJoinCondition}
            LEFT JOIN task_list wt
                ON wt.id = r.work_type
            LEFT JOIN yasccoza_openlink_market.client c
                ON c.CLIENT_ID = r.account
            LEFT JOIN client_rep cr
                ON cr.REP_ID = r.account_rep
            ORDER BY ri.interval_date DESC, ri.trigger_time DESC, ri.id DESC
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errorMessage = 'Unable to load team reminders.';
        } else {
            $stmt->bind_param('i', $loginId);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();

            if (empty($rows)) {
                $infoMessage = 'No reminders found for your team membership.';
            }
        }
    }
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header bg-info text-white">
            <b>My Team Reminder Intervals</b>
        </div>
        <div class="card-body">
            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-danger mb-3"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <?php if ($infoMessage !== '' && $errorMessage === ''): ?>
                <div class="alert alert-info mb-3"><?php echo htmlspecialchars($infoMessage); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-condensed table-sm" id="team-reminder-list" style="width:100%">
                    <thead style="background-color:#0a3d62 !important; color:#fff">
                        <tr>
                            <th>Reminder ID</th>
                            <th>Responsible</th>
                            <th>Team Name</th>
                            <th>Client / Account</th>
                            <th>Rep</th>
                            <th>Work Type</th>
                            <th>Day</th>
                            <th>Meeting Time</th>
                            <th>Meeting Platform</th>
                            <th>Meeting Link</th>
                            <th>Date Sent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                $dateCreated = !empty($row['created_at']) ? date('d/m/Y H:i', strtotime((string)$row['created_at'])) : '';
                                 $meetingTime = !empty($row['meeting_time']) ? date('H:i', strtotime((string)$row['meeting_time'])) : '';
                                $meetingDay = !empty($row['meeting_day']) ? ucfirst((string)$row['meeting_day']) : '';
                                $meetingLink = trim((string)($row['meeting_link'] ?? ''));
                                $intervalId = (int)($row['id'] ?? 0);
                                $responsibleId = (int)($row['responsible'] ?? 0);
                                $isTaken = $responsibleId > 0;
                                $responsibilityStampRaw = $isTaken ? (string)($row['updated_at'] ?? '') : '';
                                if ($responsibilityStampRaw === '' && $isTaken) {
                                    $responsibilityStampRaw = (string)($row['created_at'] ?? '');
                                }
                                $responsibilityStamp = '';
                                if ($responsibilityStampRaw !== '') {
                                    $stampTime = strtotime($responsibilityStampRaw);
                                    $responsibilityStamp = $stampTime !== false
                                        ? date('Y-m-d H:i', $stampTime)
                                        : $responsibilityStampRaw;
                                }
                                ?>
                                <tr>
                                    <td><?php echo $intervalId; ?></td>
                                    <td>
                                        <?php if (!empty(trim((string)($row['responsible_name'] ?? '')))): ?>
                                            <?php echo htmlspecialchars(trim((string)($row['responsible_name'] ?? ''))); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                        <?php if ($isTaken && $responsibilityStamp !== ''): ?>
                                            <br><small class="text-muted">Taken: <?php echo htmlspecialchars($responsibilityStamp); ?></small>
                                        <?php endif; ?>
                                        <br>
                                        <?php if (!$isTaken): ?>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary mt-1 take-responsibility"
                                                data-interval-id="<?php echo $intervalId; ?>">
                                                Take Responsibility
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-secondary mt-1" disabled>Already Taken</button>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars((string)($row['team_name'] ?? '')); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars((string)($row['account_name'] ?? '')); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars((string)($row['rep_name'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars((string)($row['work_type_name'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($meetingDay); ?></td>
                                    <td><?php echo htmlspecialchars($meetingTime); ?></td>
                                    <td><?php echo htmlspecialchars((string)($row['online_meeting'] ?? '')); ?></td>
                                    <td>
                                        <?php if ($meetingLink !== ''): ?>
                                            <a href="<?php echo htmlspecialchars($meetingLink); ?>" target="_blank" rel="noopener noreferrer">Open Link</a>
                                        <?php endif; ?>
                                    </td>
                                     <td><?php echo htmlspecialchars($dateCreated); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
    overflow-y: visible;
}
</style>

<script>
$(document).ready(function () {
    $('#team-reminder-list').DataTable({
        autoWidth: false
    });

    $(document).on('click', '.take-responsibility', function () {
        var btn = $(this);
        var intervalId = parseInt(btn.attr('data-interval-id'), 10);
        if (!intervalId) {
            return;
        }

        if (!confirm('Take responsibility for this reminder interval?')) {
            return;
        }

        start_load();
        $.ajax({
            url: 'take_reminder_responsibility.php',
            method: 'POST',
            data: {
                interval_id: intervalId,
                csrf_token: '<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>'
            },
            success: function (resp) {
                resp = String(resp).trim();
                if (resp === '1') {
                    alert_toast('Responsibility assigned to you', 'success');
                    setTimeout(function () { location.reload(); }, 700);
                } else if (resp === 'claimed') {
                    end_load();
                    alert_toast('This interval is already claimed by another member', 'warning');
                    setTimeout(function () { location.reload(); }, 900);
                } else if (resp === 'already_mine') {
                    end_load();
                    alert_toast('You are already responsible for this interval', 'info');
                    setTimeout(function () { location.reload(); }, 700);
                } else if (resp === 'unauthorized') {
                    end_load();
                    alert_toast('You are not allowed to claim this interval', 'danger');
                } else if (resp === 'csrf') {
                    end_load();
                    alert_toast('Session expired. Refresh and try again.', 'warning');
                } else {
                    end_load();
                    alert_toast('Unable to claim: ' + resp, 'danger');
                }
            },
            error: function () {
                end_load();
                alert_toast('Request failed', 'danger');
            }
        });
    });
});
</script>
