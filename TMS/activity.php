<?php
/* ===========================================
   FILE: manage_progress.php
   =========================================== */
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $qry = $conn->query("SELECT * FROM user_productivity WHERE id = $id");
  if ($qry && $qry->num_rows) {
    $row = $qry->fetch_assoc();
    foreach ($row as $k => $v) {
      $$k = $v;
    }
  }
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="container-fluid">
  <form action="" id="manage-progress">
    <input type="hidden" name="id" value="<?php echo isset($id) ? (int)$id : ''; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">

    <?php if (isset($_GET['tid'])): ?>
      <input type="hidden" name="task_id" value="<?php echo (int)$_GET['tid']; ?>">
    <?php endif; ?>

    <div class="col-lg-12">
      <div class="row">
        <div class="col-md-5">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control form-control-sm" name="name"
                   value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES) : ''; ?>" required>
          </div>

          <div class="form-group">
            <label>Duration (In days)</label>
            <input type="number" class="form-control form-control-sm" name="duration"
                   value="<?php echo isset($duration) ? (int)$duration : ''; ?>" required>
          </div>

          <div class="form-group">
            <label>Rate :R</label>
            <input type="number" class="form-control form-control-sm" name="rate"
                   value="<?php echo isset($rate) ? (int)$rate : ''; ?>" required>
          </div>

          <div class="form-group">
            <label>Resources for the Activity</label>
            <input type="number" class="form-control form-control-sm" name="resources"
                   value="1" min="1" max="1" readonly required>
            <p style="color:red">Please Note: The number of resources for the activity should not exceed resources for the work type</p>
          </div>
        </div>

        <div class="col-md-7">
          <div class="form-group">
            <label>Comment/Progress Description</label>
            <textarea name="comment" cols="30" rows="10" class="summernote form-control" required><?php
              echo isset($comment) ? htmlspecialchars($comment, ENT_QUOTES) : '';
            ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <?php if (!isset($_GET['tid'])): ?>
      <div class="card-footer border-top border-info">
        <div class="d-flex w-100 justify-content-center align-items-center">
          <button class="btn btn-flat bg-gradient-primary mx-2" type="submit">Save</button>
          <button class="btn btn-flat bg-gradient-secondary mx-2" type="button"
                  onclick="location.href='index.php?page=view_work_type&id=<?php echo isset($task_id) ? (int)$task_id : 0; ?>'">
            Cancel
          </button>
        </div>
      </div>
    <?php endif; ?>
  </form>
</div>

<script>
$(document).ready(function () {
  $('.summernote').summernote({
    height: 200,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
      ['fontname', ['fontname']],
      ['fontsize', ['fontsize']],
      ['color', ['color']],
      ['para', ['ol', 'ul', 'paragraph', 'height']],
      ['table', ['table']],
      ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
    ]
  });

  $('.select2').select2({
    placeholder: "Please select here",
    width: "100%"
  });

  $('#manage-progress').on('submit', function (e) {
    e.preventDefault();
    start_load?.();

    $.ajax({
      url: 'ajax.php?action=save_progress',
      method: 'POST',
      data: new FormData(this),
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'text',

      success: function (resp) {
        if (resp === '1') {
          alert_toast('Data successfully save', 'success');

          var urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('page') === 'view_work_type') {
            var id = getUrlParameter('id');
            location.href = 'index.php?page=view_work_type&id=' + encodeURIComponent(id);
            return;
          }

          if (urlParams.get('page') === 'edit_activity') {
            location.href = 'index.php?page=view_work_type&id=<?php echo isset($task_id) ? (int)$task_id : 0; ?>';
            return;
          }
          return;
        }

        if (resp === 'csrf') {
          alert_toast('403 CSRF failed. Refresh the page and try again.', 'danger');
          return;
        }

        alert_toast('Save failed: ' + resp, 'danger');
      },

      error: function (xhr) {
        alert_toast('HTTP ' + xhr.status + ': ' + (xhr.responseText || ''), 'danger');
        console.log('HTTP', xhr.status, 'Body:', xhr.responseText);
      },

      complete: function () {
        end_load?.();
      }
    });
  });

  function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
  }
});
</script>


<?php
/* ===========================================
   FILE: admin_class.php (save_progress)
   =========================================== */
function save_progress(){
  extract($_POST);
  $data = "";
  foreach($_POST as $k => $v){
    if(!in_array($k, array('id','csrf_token')) && !is_numeric($k)){
      if($k == 'comment')
        $v = htmlentities(str_replace("'","&#x2019;",$v));
      $data .= empty($data) ? " $k='$v' " : ", $k='$v' ";
    }
  }

  if(empty($id)){
    $save = $this->db->query("INSERT INTO user_productivity set $data");
  }else{
    $save = $this->db->query("UPDATE user_productivity set $data where id = $id");
  }
  if($save){
    return 1;
  }
}
?>
