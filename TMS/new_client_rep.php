<?php

 if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="ajax.php?action=save_rep" method="post" id="manage_rep">
                <input type="hidden" name="id" value="<?php echo isset($REP_ID) ? $REP_ID  : '' ?>">
                <input type="hidden" name="USER_CREATED" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label for="" class="control-label">Rep Name</label>
                            <input type="text" name="REP_NAME" class="form-control form-control-sm" required value="<?php echo isset($REP_NAME) ? $REP_NAME : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Email</label>
                            <input type="email" name="REP_EMAIL" class="form-control form-control-sm" required value="<?php echo isset($REP_EMAIL) ? $REP_EMAIL : '' ?>">
                        </div>
                      
                        <div class="form-group">
                            <label for="" class="control-label">Contact</label>
                            <input type="number" name="REP_CONTACT" class="form-control form-control-sm" required value="<?php echo isset($REP_CONTACT) ? $REP_CONTACT : '' ?>">
                        </div>
                    </div>
	                    <div class="col-md-6">
	                        <div class="form-group">
	                            <label for="" class="control-label">Client</label>
	                            <select class="form-control form-control-sm select2" name="CLIENT_ID[]" id="CLIENT_ID" multiple="multiple" required>
	                                <?php
	                                $selectedClientIdsForForm = [];
	                                if (isset($selectedClientIds) && is_array($selectedClientIds)) {
	                                    foreach ($selectedClientIds as $selectedClientId) {
	                                        $selectedClientId = (int)$selectedClientId;
	                                        if ($selectedClientId > 0) {
	                                            $selectedClientIdsForForm[$selectedClientId] = $selectedClientId;
	                                        }
	                                    }
	                                } elseif (isset($CLIENT_ID)) {
	                                    $singleClientId = (int)$CLIENT_ID;
	                                    if ($singleClientId > 0) {
	                                        $selectedClientIdsForForm[$singleClientId] = $singleClientId;
	                                    }
	                                }
	                                $selectedClientIdsForForm = array_values($selectedClientIdsForForm);
	                                $selectedClientLookup = array_flip($selectedClientIdsForForm);

	                                $loginType = (int)($_SESSION['login_type'] ?? 0);
	                                $loginId = (int)($_SESSION['login_id'] ?? 0);

	                                if ($loginType === 1) {
	                                    $clientSql = "SELECT CLIENT_ID, company_name FROM yasccoza_openlink_market.client ORDER BY company_name ASC";
	                                } else {
	                                    $clientSql = "SELECT CLIENT_ID, company_name FROM yasccoza_openlink_market.client WHERE (creator_id = $loginId AND orbiter_id = 0)";
	                                    if (!empty($selectedClientIdsForForm)) {
	                                        $clientSql .= " OR CLIENT_ID IN (" . implode(',', array_map('intval', $selectedClientIdsForForm)) . ")";
	                                    }
	                                    $clientSql .= " ORDER BY company_name ASC";
	                                }

	                                $client = $conn->query($clientSql);
	                                if ($client) {
	                                    while ($row = $client->fetch_assoc()):
	                                        $clientId = (int)$row['CLIENT_ID'];
	                                        $isSelectedClient = isset($selectedClientLookup[$clientId]) ? 'selected' : '';
	                                ?>
	                                <option value="<?php echo $clientId; ?>" <?php echo $isSelectedClient; ?>>
	                                    <?php echo ucwords($row['company_name']) . ' (' . $clientId . ')'; ?>
	                                </option>
	                                <?php
	                                    endwhile;
	                                }
	                                ?>
	                            </select>
	                            <small class="form-text text-muted">Select one or more clients.</small>
	                        </div>
	                        <br>
	                        <div class="form-group">
	                            <label for="" class="control-label">Role in Company </label>
	                            <input type="text" name="ROLE" class="form-control form-control-sm" required value="<?php echo isset($ROLE) ? $ROLE : '' ?>">
	                        </div>
	                    </div>
	                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2" type="submit">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=List_client_rep'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100% 100%;
    }
</style>

<script>
    $('#manage_rep').submit(function(e) {
        e.preventDefault();
        const $emailField = $('[name="REP_EMAIL"]');
        const $clientField = $('[name="CLIENT_ID[]"]');
        const selectedClientIds = $clientField.val() || [];

        $emailField.removeClass('border-danger');
        $clientField.removeClass('border-danger');

        if (!Array.isArray(selectedClientIds) || selectedClientIds.length === 0) {
            $clientField.addClass('border-danger');
            alert_toast('Please select a client before saving.', "warning");
            return;
        }

        $.ajax({
            url: 'ajax.php?action=save_rep',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                resp = String(resp).trim();
                if (resp === '1') {
                    alert_toast('Data successfully saved.', "success");
                    setTimeout(function() {
                        location.replace('index.php?page=List_client_rep');
                    }, 750);
                } else if (resp === '2') {
                    $emailField.addClass("border-danger");
                    alert_toast('Email already exists.', "danger");
                } else if (resp === '3') {
                    $clientField.addClass("border-danger");
                    alert_toast('Please select a client before saving.', "warning");
                } else if (resp === '4') {
                    alert_toast('Orbited reps cannot be edited.', "warning");
                } else {
                    alert_toast('Unable to save client rep.', "danger");
                }
                console.log(resp);
            }
        });
    });
</script>
