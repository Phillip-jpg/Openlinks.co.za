<?php
if (!isset($conn)) {
    include 'db_connect.php';
}
?>
<style>
.work-schedule-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 22px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: #ffffff;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}

.work-schedule-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top left, rgba(255,255,255,0.35), transparent 60%);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.work-schedule-btn:hover::before { opacity: 1; }
.work-schedule-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(37, 99, 235, 0.45);
}
.work-schedule-btn:active { transform: translateY(0); }

.work-schedule-btn .icon { font-size: 18px; }
.work-schedule-btn .arrow { transition: transform 0.25s ease; }
.work-schedule-btn:hover .arrow { transform: translateX(4px); }

/* Select2 invalid border */
.select2-container.is-invalid .select2-selection {
    border: 1px solid #dc3545 !important;
}
</style>

<a href="./index.php?page=schedule_teams_lvl3" class="work-schedule-btn">
   <span class="icon">📅</span>
   <span class="text">Work Resource Schedule</span>
   <span class="arrow">→</span>
</a>

<br><br>

<div class="card card-outline card-primary">
    <div class="card-body">
        <p>Create New Team</p>
        <p style="color:red; font-size:17px">
            Please Note :You Only Create new Teams in this Section Scroll to the Bottom Table and select Action if you want to Make Edits (eg. Adding a Member to an existing Team)
        </p>

        <!-- Form for Managing Team Schedule -->
        <form id="manage-schedule" action="./index.php?page=save_schedule" method="POST" novalidate>
            <div class="row">
                <!-- Team Name -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Team Name</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            name="team_name"
                            value=""
                            required
                        >
                    </div>
                </div>

                <!-- Team Members Onboarding -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Team Members Onboarding</label>
                        <select
                            class="form-control form-control-sm select2"
                            multiple="multiple"
                            name="user_ids[]"
                            id="user_ids"
                            required
                        >
                            <?php
                            if ($_SESSION['login_type'] == 2) {
                                $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 AND creator_id = {$_SESSION['login_id']} OR id={$_SESSION['login_id']}  ORDER BY name ASC;");
                            } else {
                                $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 OR type =2 ORDER BY name ASC");
                            }

                            while ($row = $employees->fetch_assoc()):
                                $isDefaultEntityMember = ((int)$_SESSION['login_type'] === 2 && (int)$row['id'] === (int)$_SESSION['login_id']);
                            ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $isDefaultEntityMember ? 'selected' : ''; ?>>
                                    <?php echo ucwords($row['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Work Types -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Add Work Type(s)</label>
                        <select
                            class="form-control form-control-sm select2"
                            multiple="multiple"
                            name="worktype_ids[]"
                            id="worktype_ids"
                            required
                        >
                            <?php
                            if ($_SESSION['login_type'] == 2) {
                                $worktypes = $conn->query("SELECT DISTINCT tl.*
                                    FROM task_list tl
                                    LEFT JOIN members_and_worktypes mw ON tl.id = mw.work_type_id
                                    WHERE mw.member_id = {$_SESSION['login_id']}
                                    OR tl.creator_id = {$_SESSION['login_id']}");
                            } else {
                                $worktypes = $conn->query("SELECT * FROM task_list ORDER BY task_name ASC");
                            }

                            while ($row = $worktypes->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['id']; ?>">
                                    <?php echo ucwords($row['task_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Admin in Charge -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Admin in Charge of Team</label>
                        <select class="form-control form-control-sm select2" name="manager_id" required>
                            <option value=""></option>
                            <?php
                            if ($_SESSION['login_type'] == 2) {
                                $managers = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 2 AND id={$_SESSION['login_id']}");
                            } else {
                                $managers = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 2 ORDER BY name ASC");
                            }

                            while ($row = $managers->fetch_assoc()):
                                $isSelectedManager = ((int)$_SESSION['login_type'] === 2 && (int)$row['id'] === (int)$_SESSION['login_id']);
                            ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $isSelectedManager ? 'selected' : ''; ?>>
                                    <?php echo ucwords($row['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <!-- Operational Leader -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Operational Leader</label>
                        <select class="form-control form-control-sm select2" required name="op_ids">
                            <option value=""></option>
                            <?php
                            if ($_SESSION['login_type'] == 2) {
                                $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 AND creator_id = {$_SESSION['login_id']} OR id={$_SESSION['login_id']}  ORDER BY name ASC;");
                            } else {
                                $employees = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 OR type =2 ORDER BY name ASC");
                            }

                            while ($row = $employees->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['id']; ?>">
                                    <?php echo ucwords($row['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-footer border-top border-info">
                <div class="d-flex w-100 justify-content-center align-items-center">
                    <button id="btn-save" class="btn btn-flat bg-gradient-primary mx-2" type="submit">
                        Save
                    </button>

                    <button class="btn btn-flat bg-gradient-secondary mx-2" type="button"
                            onclick="location.href='index.php?page=schedule_teams_lvl2'">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="col-lg-12">
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title">Teams</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered table-condensed" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                </colgroup>
                <thead style="background-color:#032033 !important; color:white">
                    <tr>
                        <th>Team_ID</th>
                        <th>Name of Team</th>
                        <th>Date Created</th>
                        <th>PM</th>
                        <th>Number of Members</th>
                        <th>Number of Worktypes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($_SESSION['login_type'] !== 1) {
                        $work_qry = $conn->query("
                            SELECT
                                ts.*,
                                COUNT(DISTINCT ts.team_members) AS member_count,
                                COUNT(DISTINCT ts.worktype_ids) AS work_type_count,
                                CONCAT(u.firstname, ' ', u.lastname) AS PM
                            FROM team_schedule ts
                            JOIN users u ON u.id = ts.pm_manager
                            WHERE ts.pm_manager = {$_SESSION['login_id']}
                            GROUP BY ts.team_id;
                        ");
                    }

                    if ($_SESSION['login_type'] == 1) {
                        $work_qry = $conn->query("
                            SELECT
                                ts.*,
                                COUNT(DISTINCT ts.team_members) AS member_count,
                                COUNT(DISTINCT ts.worktype_ids) AS work_type_count,
                                CONCAT(u.firstname, ' ', u.lastname) AS PM
                            FROM team_schedule ts
                            JOIN users u ON u.id = ts.pm_manager
                            GROUP BY ts.team_id;
                        ");
                    }

                    while ($row = $work_qry->fetch_assoc()):
                    ?>
                        <tr>
                            <td><p><?php echo ucwords($row['team_id']) ?></p></td>
                            <td><p><?php echo ucwords($row['team_name']) ?></p></td>
                            <td><p><?php echo ucwords($row['Date_created']) ?></p></td>
                            <td><p><?php echo ucwords($row['PM']) ?></p></td>
                            <td><p><?php echo ucwords($row['member_count']) ?></p></td>
                            <td><p><?php echo ucwords($row['work_type_count']) ?></p></td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item view_project"
                                       href="./index.php?page=team&team_id=<?php echo $row['team_id'] ?>"
                                       data-id="<?php echo $row['team_id'] ?>">
                                        View/Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function () {
    function markInvalid(el, isInvalid) {
        el.classList.toggle('is-invalid', isInvalid);
    }

    function getSelect2Container(selectEl) {
        return selectEl.parentElement.querySelector('.select2-container');
    }

    function validateForm(form) {
        let firstInvalid = null;

        const teamName = form.querySelector('input[name="team_name"]');
        if (teamName) {
            const invalid = teamName.value.trim() === '';
            markInvalid(teamName, invalid);
            if (invalid && !firstInvalid) firstInvalid = teamName;
        }

        const members = form.querySelector('select[name="user_ids[]"]');
        if (members) {
            const invalid = members.selectedOptions.length === 0;
            markInvalid(members, invalid);

            const s2 = getSelect2Container(members);
            if (s2) s2.classList.toggle('is-invalid', invalid);
            if (invalid && !firstInvalid) firstInvalid = s2 || members;
        }

        const worktypes = form.querySelector('select[name="worktype_ids[]"]');
        if (worktypes) {
            const invalid = worktypes.selectedOptions.length === 0;
            markInvalid(worktypes, invalid);

            const s2 = getSelect2Container(worktypes);
            if (s2) s2.classList.toggle('is-invalid', invalid);
            if (invalid && !firstInvalid) firstInvalid = s2 || worktypes;
        }

        const manager = form.querySelector('select[name="manager_id"]');
        if (manager) {
            const invalid = (manager.value || '').trim() === '';
            markInvalid(manager, invalid);

            const s2 = getSelect2Container(manager);
            if (s2) s2.classList.toggle('is-invalid', invalid);
            if (invalid && !firstInvalid) firstInvalid = s2 || manager;
        }

        const opLeader = form.querySelector('select[name="op_ids"]');
        if (opLeader) {
            const invalid = (opLeader.value || '').trim() === '';
            markInvalid(opLeader, invalid);

            const s2 = getSelect2Container(opLeader);
            if (s2) s2.classList.toggle('is-invalid', invalid);
            if (invalid && !firstInvalid) firstInvalid = s2 || opLeader;
        }

        return { ok: !firstInvalid, firstInvalid };
    }

    function disableButton(btn) {
        btn.disabled = true;
        btn.innerText = 'Saving...';
    }

    document.addEventListener('DOMContentLoaded', function () {
        $('#list').dataTable();

        $('.delete_task').click(function () {
            _conf("Are you sure to delete this work type?", "delete_task", [$(this).attr('data-id')]);
        });

        $('.new_productivity').click(function () {
            uni_modal(
                "<i class='fa fa-plus'></i> New Progress for: " + $(this).attr('data-task'),
                "manage_progress.php?tid=" + $(this).attr('data-tid'),
                'large'
            );
        });

        const form = document.getElementById('manage-schedule');
        const btn = document.getElementById('btn-save');
        const membersSelect = document.getElementById('user_ids');
        const managerSelect = form ? form.querySelector('select[name="manager_id"]') : null;

        if (!form || !btn) return;

        function ensureManagerInMembers() {
            if (!membersSelect || !managerSelect) return;

            const managerId = String(managerSelect.value || '').trim();
            if (!managerId) return;

            let managerOption = null;
            for (let i = 0; i < membersSelect.options.length; i++) {
                if (String(membersSelect.options[i].value) === managerId) {
                    managerOption = membersSelect.options[i];
                    break;
                }
            }

            if (!managerOption) return;

            if (!managerOption.selected) {
                managerOption.selected = true;
                $(membersSelect).trigger('change.select2');
            }
        }

        // Revalidate on select changes (Select2 triggers change)
        $(form).find('select').on('change', function () {
            validateForm(form);
        });

        if (managerSelect) {
            $(managerSelect).on('change', function () {
                ensureManagerInMembers();
            });
        }

        if (membersSelect) {
            $(membersSelect).on('change', function () {
                ensureManagerInMembers();
            });
        }

        ensureManagerInMembers();

        form.addEventListener('submit', function (e) {
            const result = validateForm(form);

            if (!result.ok) {
                e.preventDefault();
                result.firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });

                if (typeof alert_toast === 'function') {
                    alert_toast("Please fill in all required fields before saving.", "warning");
                } else {
                    alert("Please fill in all required fields before saving.");
                }
                return;
            }

            e.preventDefault();
            disableButton(btn);

            $.ajax({
                url: 'save_schedule.php',
                method: 'POST',
                data: $(form).serialize(),
                timeout: 45000,
                success: function (resp) {
                    const cleanResp = String(resp || '').trim();
                    if (cleanResp === 'OK' || cleanResp.indexOf('Action completed successfully') !== -1) {
                        if (typeof alert_toast === 'function') {
                            alert_toast('Team saved successfully', 'success');
                        }
                        setTimeout(function () {
                            window.location.href = 'index.php?page=schedule_teams_lvl2';
                        }, 900);
                        return;
                    }

                    btn.disabled = false;
                    btn.innerText = 'Save';
                    if (typeof alert_toast === 'function') {
                        alert_toast('Save failed: ' + String(resp).trim(), 'danger');
                    }
                },
                error: function (xhr, status) {
                    btn.disabled = false;
                    btn.innerText = 'Save';
                    if (status === 'timeout') {
                        if (typeof alert_toast === 'function') {
                            alert_toast('Save timed out. Refreshing to verify status...', 'warning');
                        }
                        setTimeout(function () {
                            window.location.href = 'index.php?page=schedule_teams_lvl2';
                        }, 800);
                        return;
                    }
                    if (typeof alert_toast === 'function') {
                        alert_toast('Request failed: ' + xhr.status, 'danger');
                    }
                }
            });
        });
    });

    function delete_task($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_task',
            method: 'POST',
            data: { id: $id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
})();
</script>
