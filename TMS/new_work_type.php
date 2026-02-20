<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($conn)) {
  include 'db_connect.php';
}

if (isset($id) && $id !== '') {
  $id = (int) $id;

  $sql = "SELECT * FROM task_list WHERE id = $id";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $existing_file = !empty($row['file_path']) ? $row['file_path'] : "";
  } else {
    $existing_file = "";
  }
}
?>


    <div class="col-lg-12">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <form action="" id="save-task-new" enctype="multipart/form-data">
            <input type="hidden" name="creator_id" value="<?php echo $_SESSION['login_id'] ?>">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">Name</label>
                        <input type="text" class="form-control form-control-sm" name="task_name" value="<?php echo isset($task_name) ? $task_name : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">Customer benefits</label>
                        <input type="text" class="form-control form-control-sm" name="customer_benefits" value="<?php echo isset($customer_benefits ) ? $customer_benefits : '' ?>">
                    </div>
                </div>
                
            </div>
            
            <div class="row">
           
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">Number of resources</label>
                        <input type="number" class="form-control form-control-sm" name="resources" value="<?php echo isset($resources ) ? $resources : '' ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">Price: R</label>
                        <input type="number" class="form-control form-control-sm" name="price" value="<?php echo isset($price ) ? $price : '' ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Upload File</label>
                            <input type="file" class="form-control-file" name="file">
                        </div>
                        
                        <div class="mt-3">
    <?php if (!empty($existing_file)): ?>
        <p><strong>Existing File:</strong> <?php echo $existing_file; ?></p>
    <?php else: ?>
        <p ><strong >Existing File:</strong> <span style="color:red">No Existing file</span></p>
    <?php endif; ?>
</div>


                    </div>
                    <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">Target</label>
                        <input type="text" class="form-control form-control-sm" name="target" value="<?php echo isset($target) ? $target : '' ?>">
                    </div>
                </div>
                
        <div class="col-md-3">
                <label for="start_week_responses">Start Time:</label>
                <?php
  // Format time as HH:MM
  $formatted_start_time = isset($start_time) 
      ? date('H:i', strtotime($start_time)) 
      : '';
?>
<input type="time" id="start_week_responses" name="start_time" class="form-control mb-6" style="width:100px" value="<?php echo $formatted_start_time ?>">

                
        </div>
        <div class="col-md-3">
             <label for="end_week_responses">End Time:</label>
                <?php
  $formatted_end_time = isset($end_time)
      ? date('H:i', strtotime($end_time))
      : '';
?>
<input type="time" id="end_week_responses" name="end_time" class="form-control mb-6" style="width:100px" value="<?php echo $formatted_end_time ?>">

        </div>
            </div>
                 <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">Video Link</label>
                        <input type="text" class="form-control form-control-sm" name="video_link" value="<?php echo isset($video_link ) ? $video_link : '' ?>">
                    </div>
                </div>
                
                    
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="control-label">In-House/Originator</label>
                            <select name="typeofw" id="typeofw" class="custom-select custom-select-sm">
        						<option value="In-House" <?php echo isset($typeofw) && $typeofw == 'In-progress' ? 'selected' : '' ?>>In-House</option>
        						<option value="Originator" <?php echo isset($typeofw) && $typeofw == 'On-Hold' ? 'selected' : '' ?>>Originator</option>
        					</select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="" class="control-label">Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="summernote form-control">
                            <?php echo isset($description) ? $description : '' ?>
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="" class="control-label">Instructions</label>
                        <textarea name="instructions" id="" cols="30" rows="10" class="summernote form-control">
                            <?php echo isset($instructions) ? $instructions : '' ?>
                        </textarea>
                    </div>
                </div>
            </div>
            
            </form>
            </div>
            <div class="card-footer border-top border-info">
                <div class="d-flex w-100 justify-content-center align-items-center">
                    <button class="btn btn-flat  bg-gradient-primary mx-2" form="save-task-new">Save</button>
                    <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=work_type'">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#save-task-new').submit(function(e){
  e.preventDefault();
  start_load();

  $.ajax({
    url: 'ajax.php?action=save_task_new',
    data: new FormData(this),
    cache: false,
    contentType: false,
    processData: false,
    type: 'POST',

    success: function(resp, status, xhr){
      console.log("HTTP:", xhr.status);
      console.log("RAW RESPONSE:", resp);

      // Trim because sometimes there's whitespace/newlines
      const clean = String(resp).trim();

      if(clean === "1"){
        alert_toast('Data successfully saved',"success");
        setTimeout(function(){
          location.href = 'index.php?page=work_type';
        },2000);
      } else if (clean === "csrf") {
        alert_toast("CSRF blocked (token missing/mismatch). Refresh page and try again.", "error");
        end_load();
      } else if (clean === "unauthorized") {
        alert_toast("Session expired. Please login again.", "error");
        end_load();
      } else {
        alert_toast("Save failed: " + clean, "error");
        end_load();
      }
    },

    error: function(xhr){
      console.log("HTTP:", xhr.status);
      console.log("RESPONSE TEXT:", xhr.responseText);
      alert_toast("Request failed (" + xhr.status + "): " + String(xhr.responseText).trim(), "error");
      end_load();
    }
  });
});

    </script>