<?php
include('db_connect.php');
session_start();

if (isset($_SESSION['login_id']) && is_numeric($_SESSION['login_id'])) {
    $login_id = (int)$_SESSION['login_id'];

    // (Optional) Start of current week (Monday)
    $current_date = date('Y-m-d');
    $day_of_week  = date('N', strtotime($current_date));
    $monday_date  = date('Y-m-d', strtotime($current_date . ' -' . ($day_of_week - 1) . ' days'));

    // echo "<p>Start of the current week (Monday): $monday_date</p>";
}
?>

<div class="container">
  <div class="row">

    <!-- Team Progress Calendar (Year picker) -->
   <div class="col-md-6 mb-4">
  <div class="card shadow-sm p-3 border-primary">
    <h4 class="text-primary font-weight-bold mb-3">Team Progress Calendar</h4>

    <label for="year_calendar">Select Year:</label>
    <select id="year_calendar" class="form-control mb-3">
      <?php
        $startYear = 2000;
        $endYear = 2100;
        $currentYear = date('Y');

        for ($i = $startYear; $i <= $endYear; $i++) {
            // This marks the current year as the default selected option
            $selected = ($i == $currentYear) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
        }
      ?>
    </select>

    <div class="d-flex align-items-center">
      <a class="btn btn-secondary" id="view-link-calendar" href="calendar.php?year=<?php echo date('Y'); ?>">
        View
      </a>
    </div>
  </div>
</div>

    <!-- Form: Resources Report -->
    <div class="col-md-6 mb-4">
      <form method="POST" action="ajax.php?action=print_resources_report" class="card shadow-sm p-3 border-primary">
        <h4 class="text-primary font-weight-bold mb-3">Resources</h4>

        <label for="start_week_resources">Start:</label>
        <input type="date" id="start_week_resources" name="start_week" class="form-control mb-3" required>

        <label for="end_week_resources">End:</label>
        <input type="date" id="end_week_resources" name="end_week" class="form-control mb-3" required>

        <div class="d-flex align-items-center">
          <!--<button class="btn btn-primary mr-2" type="submit" name="print_resources_report">-->
          <!--  <i class="fa fa-print"></i> Print Resources Period-->
          <!--</button>-->

          <a class="btn btn-secondary" id="view-link-resources" href="#">View</a>
        </div>
      </form>
    </div>

    <!-- Form: Jobs Report -->
    <div class="col-md-6 mb-4">
      <form method="POST" action="ajax.php?action=print_report" class="card shadow-sm p-3 border-primary">
        <h4 class="text-primary font-weight-bold mb-3">Jobs</h4>

        <label for="start_week_jobs">Start:</label>
        <input type="date" id="start_week_jobs" name="start_week" class="form-control mb-3" required>

        <label for="end_week_jobs">End:</label>
        <input type="date" id="end_week_jobs" name="end_week" class="form-control mb-3" required>

        <div class="d-flex align-items-center">
          <button class="btn btn-primary mr-2" type="submit" name="print_report">
            <i class="fa fa-print"></i> Print Jobs Period
          </button>
          <a class="btn btn-secondary" id="view-link-jobs" href="#">View</a>
        </div>
      </form>
    </div>

    <!-- Form: Claims Report -->
    <div class="col-md-6 mb-4">
      <form method="POST" action="ajax.php?action=print_claims_report" class="card shadow-sm p-3 border-primary">
        <h4 class="text-primary font-weight-bold mb-3">Claims</h4>

        <label for="start_week_claims">Start:</label>
        <input type="date" id="start_week_claims" name="start_week" class="form-control mb-3" required>

        <label for="end_week_claims">End:</label>
        <input type="date" id="end_week_claims" name="end_week" class="form-control mb-3" required>

        <div class="d-flex align-items-center">
          <!--<button class="btn btn-primary mr-2" type="submit" name="print_claims_report">-->
          <!--  <i class="fa fa-print"></i> Print Claims Period-->
          <!--</button>-->
          <a class="btn btn-secondary" id="processed_claims" href="#">View</a>
        </div>
      </form>
    </div>

    <!-- Form: Jobs Responses Report -->
    <div class="col-md-6 mb-4">
      <form method="POST" action="ajax.php?action=print_jobs_responses" class="card shadow-sm p-3 border-primary">
        <h4 class="text-primary font-weight-bold mb-3">Jobs Responses</h4>

        <label for="start_week_responses">Start:</label>
        <input type="date" id="start_week_responses" name="start_week" class="form-control mb-3" required>

        <label for="end_week_responses">End:</label>
        <input type="date" id="end_week_responses" name="end_week" class="form-control mb-3" required>

        <div class="d-flex align-items-center">
          <!--<button class="btn btn-primary mr-2" type="submit" name="print_jobs_responses">-->
          <!--  <i class="fa fa-print"></i> Print All Jobs Responses-->
          <!--</button>-->
          <a class="btn btn-secondary" id="view-link-responses" href="#">View</a>
        </div>
      </form>
    </div>

    <!-- Form: Jobs Not Responded To -->
    <div class="col-md-6 mb-4">
      <form method="POST" action="ajax.php?action=print_jobs_not_responses" class="card shadow-sm p-3 border-primary">
        <h4 class="text-primary font-weight-bold mb-3">Jobs Not Responded To</h4>

        <label for="start_week_responses_not">Start:</label>
        <input type="date" id="start_week_responses_not" name="start_week" class="form-control mb-3" required>

        <label for="end_week_responses_not">End:</label>
        <input type="date" id="end_week_responses_not" name="end_week" class="form-control mb-3" required>

        <div class="d-flex align-items-center">
          <button class="btn btn-primary mr-2" type="submit" name="print_jobs_not_responses">
            <i class="fa fa-print"></i> Print All Jobs Responses
          </button>
          <a class="btn btn-secondary" id="view-link-responses_not" href="#">View</a>
        </div>
      </form>
    </div>

    <!-- Admins Print -->
    <!--<div class="col-md-6 mb-4">-->
    <!--  <form method="POST" action="ajax.php?action=Submit" class="card shadow-sm p-3 border-primary">-->
    <!--    <button class="btn btn-primary mx-3" name="print_admins">-->
    <!--      <i class="fa fa-print"></i> Print All Admins-->
    <!--    </button>-->
    <!--  </form>-->
    <!--</div>-->

    <!-- Jobs to SMMEs Print -->
    <!--<div class="col-md-6 mb-4">-->
    <!--  <form method="POST" action="ajax.php?action=Submit" class="card shadow-sm p-3 border-primary">-->
    <!--    <button class="btn btn-primary mx-3" name="print_jobs_to_smmes">-->
    <!--      <i class="fa fa-print"></i> Print All Jobs Sent to SMMEs-->
    <!--    </button>-->
    <!--  </form>-->
    <!--</div>-->

  </div>
</div>

<script>
  // Calendar year view link
  (function () {
    const yearInput = document.getElementById('year_calendar');
    const viewLink  = document.getElementById('view-link-calendar');

    function updateLink() {
      const y = yearInput.value.trim();
      viewLink.href = `index.php?page=my_teams_progress_calendar&y=${encodeURIComponent(y)}`;
    }

    yearInput.addEventListener('input', updateLink);
    updateLink();
  })();

  // Helper for date range links
  function wireRangeLink(linkId, startId, endId, pageName) {
    const link = document.getElementById(linkId);
    link.addEventListener('click', function (event) {
      const start = document.getElementById(startId).value;
      const end   = document.getElementById(endId).value;

      if (start && end) {
        link.href = `./index.php?page=${pageName}&start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;
      } else {
        event.preventDefault();
        alert('Please select both start and end dates.');
      }
    });
  }

  wireRangeLink('view-link-resources', 'start_week_resources', 'end_week_resources', 'filter_resources');
  wireRangeLink('view-link-jobs', 'start_week_jobs', 'end_week_jobs', 'filter_jobs');
  wireRangeLink('processed_claims', 'start_week_claims', 'end_week_claims', 'processed_claims');
  wireRangeLink('view-link-responses', 'start_week_responses', 'end_week_responses', 'filter_responses');
  wireRangeLink('view-link-responses_not', 'start_week_responses_not', 'end_week_responses_not', 'filter_not_responded');
</script>

<!-- Bootstrap CSS and JS dependencies -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
