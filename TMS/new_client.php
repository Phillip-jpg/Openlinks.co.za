<?php

 if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="ajax.php?action=save_client" method="post" id="manage_client">
                <input type="hidden" name="id" value="<?php echo isset($CLIENT_ID) ? $CLIENT_ID : '' ?>">
              	<input type="hidden" name="creator_id" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
              	<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">
                <div class="row">
                    <div class="col-md-6 border-right">
                    <div class="form-group">
							<label for="" class="control-label">Offices</label>
							<select name="office_id" id="offices" class="custom-select custom-select-sm">
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
                        <div class="form-group">
                            <label for="" class="control-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control form-control-sm" required value="<?php echo isset($company_name) ? $company_name : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">City</label>
                            <input type="text" name="city" class="form-control form-control-sm" required value="<?php echo isset($city) ? $city : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Province</label>
                            <input type="text" name="province" class="form-control form-control-sm" required value="<?php echo isset($province) ? $province : '' ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                    <div class="form-group" id="industryDropdownDiv">
						<label for="" class="control-label">Industries</label>
						<select name="industry_id" id="industries" class="custom-select custom-select-sm">
							<?php
							$officeId = $_GET['officeId'];
							$stmt = $conn->prepare("SELECT TITLE_ID, title FROM yasccoza_openlink_association_db.industry_title WHERE INDUSTRY_ID = ?");
							$stmt->bind_param("i", $officeId); // Assuming INDUSTRY_ID is an integer, use "i" for integers

							if ($stmt->execute()) {
								$result = $stmt->get_result();
								while ($row = $result->fetch_assoc()) {
									// Add each industry as an option to the response array
									echo "<option value='" . $row['TITLE_ID'] . "'>" . $row['title'] . "</option>";
								}
							}
							?>
						</select>
					</div>
                      
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($Email) ? $Email : '' ?>">
                            <small id="msg"></small>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Contact</label>
                            <input type="number" class="form-control form-control-sm" name="contact" required value="<?php echo isset($Contact) ? $Contact : '' ?>">
                            <small id="msg"></small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2" type="submit">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=client_list'">Cancel</button>
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
    // Function to load industries based on the selected office
    function loadIndustries() {
        var officeId = document.getElementById('offices').value;
        var industriesDropdown = document.getElementById('industries');

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
    document.getElementById('offices').addEventListener('change', loadIndustries);

    // Initial load of industries based on the selected office (if applicable)
    loadIndustries(); // You can decide whether to keep this initial load or load only on user selection
</script>
<script>
    $('#manage_client').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=save_client',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast('Data successfully saved.', "success");
                    setTimeout(function() {
                        location.replace('index.php?page=client_list');
                    }, 750);
                } else if (resp == 2) {
                    $('#msg').html("<div class='alert alert-danger'>Email already exists.</div>");
                    $('[name="email"]').addClass("border-danger");
                }
                console.log(resp);
            }
        });
    });
</script>
