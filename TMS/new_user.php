<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$isEditMode = !empty($id);
$recordCreatorId = isset($creator_id) ? (int)$creator_id : 0;
$currentUserType = isset($type) ? (int)$type : 0;
$currentOrbiterId = isset($row['orbiter_id']) ? (int)$row['orbiter_id'] : (isset($orbiter_id) ? (int)$orbiter_id : 0);
$isEntityLogin = ((int)($_SESSION['login_type'] ?? 0) === 2);
$worktypeOnlyEdit = (
    $isEntityLogin
    && $isEditMode
    && $currentUserType === 3
    && $currentOrbiterId !== 0
    && $recordCreatorId === (int)($_SESSION['login_id'] ?? 0)
);
$fieldReadonly = $worktypeOnlyEdit ? 'readonly' : '';
$fieldDisabled = $worktypeOnlyEdit ? 'disabled' : '';

?>
<div class="col-lg-12">
	<div class="card">
		<div class="card-body">
			<form action="save_member_worktype.php" id="manage_user">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<input type="hidden" name="creator_id" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
				<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

				<div class="row">
						<?php if ($worktypeOnlyEdit): ?>
						<div class="col-12">
							<div class="alert alert-info">
								This orbited member can only have work types updated by this entity.
							</div>
						</div>
						<?php endif; ?>
					<div class="col-md-6 border-right">
						<?php if ($currentOrbiterId === 0): ?>
					<div class="form-group">
							<label for="" class="control-label">Offices</label>
							<select name="OFFICE_ID" id="offices" class="custom-select custom-select-sm">
							<option value="0">SELECT NONE</option>
								<option value="1">Office of Life Sciences</option>
								<option value="2">Office of Energy and Transportation</option>
								<option value="3">Office of Real Estate and Construction</option>
								<option value="4">Office of Manufacturing</option>
								<option value="5">Office of Technology</option>
								<option value="6">Office of Trade and Services</option>
								<option value="7">Office of Finance</option>
								<option value="8">Office of Structured Finance</option>
								<option value="9">Office of International Corporate Finance</option>
								
							</select>
						</div>
						<?php endif; ?>
						
						<div class="form-group">
							<label for="" class="control-label">First Name/Entity</label>
							<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>" <?php echo $fieldReadonly; ?>>
						</div>
						<div class="form-group">
							<label for="" class="control-label">Last Name/Function</label>
							<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>" <?php echo $fieldReadonly; ?>>
						</div>
						<?php if($_SESSION['login_type'] == 1): ?>
						<div class="form-group">
							<label for="" class="control-label">User Role</label>
							<select name="type" id="type" class="custom-select custom-select-sm" <?php echo $fieldDisabled; ?>>
								<!-- <option value="3" <?php echo isset($type) && $type == 3 ? 'selected' : '' ?>>Team Member</option> -->
								<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Entity</option>
								<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Super Admin</option>
								<option value="4" <?php echo isset($type) && $type == 4 ? 'selected' : '' ?>>Admin Assistant</option>
							</select>
						</div>
						<?php else: ?>
							<input type="hidden" name="type" value="3">
						<?php endif; ?>
						<div class="form-group">
							<label for="" class="control-label">Avatar</label>
							<div class="custom-file">
		                      <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))" <?php echo $fieldDisabled; ?>>
		                      <label class="custom-file-label" for="customFile">Choose file</label>
		                    </div>
						</div>
						<div class="form-group d-flex justify-content-center align-items-center">
							<img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar :'' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
						</div>
					</div>
					
					<div class="col-md-6">
					<div class="form-group" id="industryDropdownDiv">
						<label for="" class="control-label">Industries</label>
						<select name="industry_id" id="industries" class="custom-select custom-select-sm" <?php echo $fieldDisabled; ?>>
						
						</select>
					</div>

						<div class="form-group">
							<label class="control-label">Email</label>
							<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>" <?php echo $fieldReadonly; ?>>
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Phone</label>
							<input type="number" class="form-control form-control-sm" name="number" required value="<?php echo isset($number) ? $number : '' ?>" <?php echo $fieldReadonly; ?>>
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Password</label>
							<input type="password" class="form-control form-control-sm" name="password" <?php echo (!$worktypeOnlyEdit && !isset($id)) ? "required" : '' ?> <?php echo $fieldDisabled; ?>>
							<small><i><?php echo isset($id) ? "Leave this blank if you dont want to change you password":'' ?></i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">Confirm Password</label>
							<input type="password" class="form-control form-control-sm" name="cpass" <?php echo (!$worktypeOnlyEdit && !isset($id)) ? 'required' : '' ?> <?php echo $fieldDisabled; ?>>
							<small id="pass_match" data-status=''></small>
						</div>
						<?php
						$isType2Login = ((int)($_SESSION['login_type'] ?? 0) === 2);
						$isEditingEntityRecord = (!empty($id) && (int)($type ?? 0) === 2);
						if ($isType2Login && !$isEditingEntityRecord):
						?>
						<div class="form-group">
							<label for="" class="control-label">Add Work Type</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="task_ids[]">
								<option></option>
								<?php
								$taskList = $conn->query("
									SELECT DISTINCT tl.*
									FROM task_list tl
									WHERE tl.creator_id = {$_SESSION['login_id']}
									ORDER BY tl.task_name ASC
								");
								$selectedTasks = [];
								if (!empty($id)) {
									$memberId = (int)$id;
									$creatorId = (int)$_SESSION['login_id'];
									$selectedTaskQuery = $conn->query("
										SELECT DISTINCT work_type_id
										FROM members_and_worktypes
										WHERE member_id = {$memberId}
										  AND creator_id = {$creatorId}
									");
									if ($selectedTaskQuery) {
										while ($selectedTaskRow = $selectedTaskQuery->fetch_assoc()) {
											$selectedTasks[] = (int)$selectedTaskRow['work_type_id'];
										}
									}
								}
								if (empty($selectedTasks)) {
									$selectedTasks = isset($task_ids) ? array_map('intval', explode(',', (string)$task_ids)) : [];
								}
								while ($row = $taskList->fetch_assoc()):
									$taskId = (int)$row['id'];
								?>
									<option value="<?php echo $taskId; ?>" <?php echo in_array($taskId, $selectedTasks, true) ? 'selected' : ''; ?>>
										<?php echo ucwords($row['task_name']); ?>
									</option>
								<?php endwhile; ?>
							</select>
						</div>
						<?php endif; ?>
					
			
</div>
</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Save</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=user_list'">Back</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>

<script>
    // Function to load industries based on the selected office
    function loadIndustries() {
        var officesDropdown = document.getElementById('offices');
        var industriesDropdown = document.getElementById('industries');
        if (!officesDropdown || !industriesDropdown) {
            return;
        }
        var officeId = officesDropdown.value;

        // Clear the industries dropdown while loading data
        //industriesDropdown.innerHTML = '<option value="">Loading...</option>';

        // Make an AJAX request to your PHP script
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                // Update the industries dropdown with the response from PHP
                industriesDropdown.innerHTML = xhttp.responseText;
            }
        };

        // Send the request to your PHP script with the selected office ID
        xhttp.open('GET', 'index.php?page=test&officeId=' + officeId, true);
        xhttp.send();
    }

    // Attach an event listener to the offices dropdown to trigger loading of industries
    var officesDropdown = document.getElementById('offices');
    if (officesDropdown) {
        officesDropdown.addEventListener('change', loadIndustries);
        loadIndustries();
    }
</script>


<script>
	$('[name="password"],[name="cpass"]').keyup(function(){
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if(cpass == '' ||pass == ''){
			$('#pass_match').attr('data-status','')
		}else{
			if(cpass == pass){
				$('#pass_match').attr('data-status','1').html('<i class="text-success">Password Matched.</i>')
			}else{
				$('#pass_match').attr('data-status','2').html('<i class="text-danger">Password does not match.</i>')
			}
		}
	})
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage_user').submit(function(e){
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if($('[name="password"]').val() != '' && $('[name="cpass"]').val() != ''){
			if($('#pass_match').attr('data-status') != 1){
				if($("[name='password']").val() !=''){
					$('[name="password"],[name="cpass"]').addClass("border-danger")
					end_load()
					return false;
				}
			}
		}
		$.ajax({
			url:'ajax.php?action=save_user',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
                  if(resp == 1){
                    alert_toast('Data successfully saved.',"success");
                    setTimeout(function(){
                      location.replace('index.php?page=user_list')
                    },750)
                  }else if(resp == 2){
                    $('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
                    $('[name="email"]').addClass("border-danger")
                    end_load()
                  }else if(resp.trim() == 'csrf'){
                    $('#msg').html("<div class='alert alert-danger'>Session expired. Refresh the page and try again.</div>");
                    end_load();
                  }else if(resp.trim() == 'unauthorized'){
                    $('#msg').html("<div class='alert alert-danger'>You are not logged in.</div>");
                    end_load();
                  }else{
                    $('#msg').html("<div class='alert alert-danger'>Failed to save. Response: "+resp+"</div>");
                    end_load();
                  }
                  console.log(resp);
                }

		})
	});

</script>
