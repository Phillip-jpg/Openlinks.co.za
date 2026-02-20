<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('db_connect.php');

if (!isset($_SESSION['login_id']) || !is_numeric($_SESSION['login_id'])) {
    exit;
}

$login_id = (int) $_SESSION['login_id'];
$year     = (int) date('Y');

/* ------------------------------------------------
   FETCH WEEKLY COUNTS (PERSON VIEW) - MON to FRI
   - Generates all weeks for the year (so zeros show)
   - Counts Assigned/ Due/ Done only inside Mon-Fri window
------------------------------------------------ */
$sql = "
WITH RECURSIVE weeks AS (
  SELECT
    DATE_SUB(
      DATE(CONCAT($year, '-01-01')),
      INTERVAL WEEKDAY(DATE(CONCAT($year, '-01-01'))) DAY
    ) AS week_start
  UNION ALL
  SELECT DATE_ADD(week_start, INTERVAL 7 DAY)
  FROM weeks
  WHERE DATE_ADD(week_start, INTERVAL 7 DAY) <= DATE(CONCAT($year, '-12-31'))
)
SELECT
  YEARWEEK(w.week_start, 1) AS period,
  w.week_start,
  DATE_ADD(w.week_start, INTERVAL 4 DAY) AS week_end,
  MONTH(w.week_start) AS Month_Created,

  COALESCE(SUM(CASE
    WHEN ad.start_date >= w.week_start
     AND ad.start_date <  DATE_ADD(w.week_start, INTERVAL 5 DAY)  -- Mon–Fri
    THEN 1 ELSE 0 END), 0) AS Assigned,

  COALESCE(SUM(CASE
    WHEN pl.end_date >= w.week_start
     AND pl.end_date <  DATE_ADD(w.week_start, INTERVAL 5 DAY)    -- Mon–Fri
    THEN 1 ELSE 0 END), 0) AS Due,

  COALESCE(SUM(CASE
    WHEN ad.Done_Date >= w.week_start
     AND ad.Done_Date <  DATE_ADD(w.week_start, INTERVAL 5 DAY)   -- Mon–Fri
    THEN 1 ELSE 0 END), 0) AS Done

FROM weeks w
LEFT JOIN yasccoza_tms_db.assigned_duties ad
  ON ad.user_id = $login_id
LEFT JOIN yasccoza_tms_db.project_list pl
  ON pl.id = ad.project_id
GROUP BY w.week_start
ORDER BY w.week_start;
";

$res = $conn->query($sql);

$weeksByMonth = [];
while ($row = $res->fetch_assoc()) {
    $m = (int)$row['Month_Created'];

    $weeksByMonth[$m][] = [
        'period'   => (int)$row['period'],
        'start'    => $row['week_start'],
        'end'      => $row['week_end'],
        'Assigned' => (int)$row['Assigned'],
        'Due'      => (int)$row['Due'],
        'Done'     => (int)$row['Done'],
    ];
}

$months = [
    1=>"January",2=>"February",3=>"March",4=>"April",
    5=>"May",6=>"June",7=>"July",8=>"August",
    9=>"September",10=>"October",11=>"November",12=>"December"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Weekly Calendar</title>

<style>
body { background:#f4f6f9; font-family: Arial, sans-serif; }

/* MONTH BLOCK */
.month { width:100%; margin-bottom:40px; }

/* MONTH HEADER */
.month-header {
    background:#172D44;
    color:white;
    padding:15px;
    border-radius:8px;
}
.month-header h2 { margin:0; color:#0dc0ff; }

.month-stats { display:flex; gap:25px; margin-top:10px; }
.started { color:#4caf50; }
.due     { color:#f44336; }
.done    { color:#0dc0ff; }

/* WEEK GRID */
.weeks {
    margin-top:15px;
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap:15px;
}

/* WEEK TILE */
.week-tile {
    background:#1f3b5a;
    color:white;
    padding:12px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,.2);
}
.week-title {
    text-align:center;
    color:#0dc0ff;
    font-weight:bold;
    margin-bottom:8px;
}
.stat { display:flex; justify-content:space-between; margin:4px 0; }

.week-tile a { color:white; text-decoration:none; }

@media(max-width:992px){ .weeks { grid-template-columns: repeat(3, 1fr); } }
@media(max-width:600px){ .weeks { grid-template-columns: repeat(2, 1fr); } }
</style>
</head>

<body>

<h2 style="text-align:center;margin:20px 0;">
    My Weekly Progress – <?= $year ?>
</h2>

<?php for ($m = 1; $m <= 12; $m++): 
    $weeks = $weeksByMonth[$m] ?? [];
    $mAssigned = $mDue = $mDone = 0;

    foreach ($weeks as $w) {
        $mAssigned += $w['Assigned'];
        $mDue      += $w['Due'];
        $mDone     += $w['Done'];
    }
?>
<div class="month">
    <div class="month-header">
        <h2><?= $months[$m] ?></h2>
        <div class="month-stats">
            <div class="started">Started: <strong><?= $mAssigned ?></strong></div>
            <div class="due">Due: <strong><?= $mDue ?></strong></div>
            <div class="done">Done: <strong><?= $mDone ?></strong></div>
        </div>
    </div>

    <div class="weeks">
        <?php foreach ($weeks as $week): ?>
        <div class="week-tile">
            <div class="week-title">
                <?= $week['start'] ?> → <?= $week['end'] ?>
            </div>

            <div class="stat started"><span>Started</span><strong><?= $week['Assigned'] ?></strong></div>
            <div class="stat due"><span>Due</span><strong><?= $week['Due'] ?></strong></div>
            <div class="stat done"><span>Done</span><strong><?= $week['Done'] ?></strong></div>

            <hr style="border:1px solid rgba(255,255,255,.3)">

            <!-- Optional drill-down links (change page name if needed) -->
            <div style="text-align:center">
                <a href="index.php?page=my_progress_period&p=<?= $week['period'] ?>&w=1">Started</a> |
                <a href="index.php?page=my_progress_period&p=<?= $week['period'] ?>&w=2">Due</a> |
                <a href="index.php?page=my_progress_period&p=<?= $week['period'] ?>&w=3">Done</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endfor; ?>

</body>
</html>
