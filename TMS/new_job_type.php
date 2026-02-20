<?php if(!isset($conn)){ include 'db_connect.php'; 


if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

} ?>



<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form action="" id="save-job-new">

            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        	<input type="hidden" name="creator_id" value="<?php echo isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '' ?>">
        	<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="control-label">Name</label>
                    <input type="text" class="form-control form-control-sm" name="job_type_name" value="<?php echo isset($job_type_name) ? $job_type_name : '' ?>">
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
    
        
        </form>
        </div>
        <div class="card-footer border-top border-info">
            <div class="d-flex w-100 justify-content-center align-items-center">
                <button class="btn btn-flat  bg-gradient-primary mx-2" form="save-job-new">Save</button>
                <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=job_type'">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#save-job-new').submit(function(e){
        e.preventDefault()
          start_load()
    
        $.ajax({
            url:'ajax.php?action=save_job_new',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                console.log('Response:', resp);
                if(resp == 1){
                    alert_toast('Data successfully saved',"success");
                    setTimeout(function(){
                        location.href = 'index.php?page=job_type'
                    },2000)
                }
                else {
                    console.log("something wrong here");
                }
            }
        })
    })
</script>