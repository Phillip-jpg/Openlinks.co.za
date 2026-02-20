<?php if(!isset($conn)){ include 'db_connect.php'; } ?>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title text-center" style="padding-left:220px">Generate and Save Project Link</h5>
        </div>
        <div class="card-body">
            <form id="manage-project">
                <input type="hidden" name="Admin_ID" value="<?php echo htmlspecialchars($_SESSION['login_id'], ENT_QUOTES, 'UTF-8'); ?>">
                <p class="text-danger text-center">Copy the link before submitting the information!</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="generated-link" class="control-label">Generated Link</label>
                            <input type="text" id="generated-link" class="form-control form-control-sm" name="Link" value="<?php echo isset($link) ?>" readonly required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company-name" class="control-label">Company Name</label>
                            <input type="text" id="company-name" class="form-control form-control-sm" name="Smme_name" value="<?php echo isset($company) ?>" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer border-top border-info text-center">
            <button class="btn btn-success mx-2" type="button" onclick="generateLink()">Generate</button>
            <button class="btn btn-secondary mx-2" form="manage-project">Save</button>
        </div>
    </div>
    <div id="link-container"></div>
</div>

<script>
    function generateLink() {
        const baseUrl = "https://openlinks.co.za/TIMS/SMME/login.php#signup";
        const inputField = document.getElementById('generated-link');
        inputField.value = baseUrl;

        // Display a toast or alert for user feedback
        alert_toast('Link Generated Successfully', 'success');
    }

    $('#manage-project').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_link',
            data: new FormData(this),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp){
                console.log('Response:', resp);
                if(resp == 1){
                    alert_toast('Data successfully saved', "success");
                    setTimeout(function(){
                        location.href = 'index.php?page=generate_invite';
                    }, 2000);
                } else {
                    alert_toast('An error occurred', "danger");
                    console.log("Error:", resp);
                }
            },
            error: function(xhr, status, error) {
                alert_toast('An error occurred', "danger");
                console.log("Error:", error);
            }
        });
    });
</script>

<!-- Additional Styling -->
<style>
    .container {
        max-width: 700px;
    }
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        text-align: center;
        padding: 15px 0;
        font-size: 18px;
        font-weight: bold;
    }
    .form-group label {
        font-weight: bold;
        color: #495057;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
    }
    .btn {
        padding: 10px 20px;
        font-size: 14px;
    }
</style>
