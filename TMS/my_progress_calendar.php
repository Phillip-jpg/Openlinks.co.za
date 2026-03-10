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
:root {
    --bg-1: #f4f8ff;
    --bg-2: #eaf3ff;
    --surface: #ffffff;
    --line: #d8e6f7;
    --ink: #0f2238;
    --muted: #5f7288;
    --navy-1: #0f2a44;
    --navy-2: #1b4467;
    --cyan-1: #19a5da;
    --cyan-2: #5bd2f5;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    background:
        radial-gradient(circle at 12% 10%, rgba(25, 165, 218, 0.12), transparent 34%),
        radial-gradient(circle at 88% 0%, rgba(15, 42, 68, 0.08), transparent 38%),
        linear-gradient(180deg, var(--bg-1), var(--bg-2));
    color: var(--ink);
    font-family: "Segoe UI", "Trebuchet MS", sans-serif;
}

.calendar-shell {
    margin: 0 auto;
    max-width: 1320px;
    padding: 1.2rem 1rem 1.6rem;
}

.page-title {
    color: var(--ink);
    font-size: 1.55rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    margin: 0 0 1.05rem;
    text-align: center;
}

.month {
    margin-bottom: 1.5rem;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(15, 34, 56, 0.08);
    overflow: hidden;
}

.month-header {
    background: linear-gradient(125deg, var(--navy-1) 0%, var(--navy-2) 55%, #0d5d8e 100%);
    color: #eaf6ff;
    padding: 0.9rem 1rem 0.82rem;
}

.month-header h2 {
    margin: 0;
    color: #ffffff;
    font-size: 1.16rem;
    font-weight: 700;
}

.month-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-top: 0.58rem;
}

.month-stats > div {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 999px;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 0.24rem 0.66rem;
}

.started { color: #b9f3d7; }
.due     { color: #ffe3b0; }
.done    { color: #bceeff; }

.weeks {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.88rem;
    padding: 0.95rem;
}

.week-tile {
    background: linear-gradient(145deg, #143452 0%, #20567f 58%, #2379ae 100%);
    border: 1px solid rgba(91, 210, 245, 0.28);
    border-radius: 14px;
    color: #f4fbff;
    padding: 0.72rem 0.74rem;
    box-shadow: 0 8px 20px rgba(15, 34, 56, 0.2);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.week-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(15, 34, 56, 0.26);
}

.week-title {
    text-align: center;
    color: #d9f4ff;
    font-size: 0.9rem;
    font-weight: 700;
    line-height: 1.35;
    margin-bottom: 0.56rem;
}

.stat {
    align-items: center;
    display: flex;
    font-size: 0.88rem;
    justify-content: space-between;
    margin: 0.28rem 0;
}

.stat strong {
    font-size: 0.95rem;
}

.tile-divider {
    border: 0;
    border-top: 1px solid rgba(217, 244, 255, 0.32);
    margin: 0.58rem 0 0.5rem;
}

.week-links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.32rem;
    justify-content: center;
}

.week-links a {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.24);
    border-radius: 999px;
    color: #f5fcff;
    font-size: 0.79rem;
    font-weight: 600;
    padding: 0.2rem 0.54rem;
    text-decoration: none;
}

.week-links a:hover {
    background: rgba(91, 210, 245, 0.28);
    color: #ffffff;
}

@media (max-width: 1200px) {
    .weeks { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (max-width: 860px) {
    .weeks { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 560px) {
    .calendar-shell { padding: 0.9rem 0.72rem 1.2rem; }
    .page-title { font-size: 1.28rem; }
    .weeks { grid-template-columns: 1fr; gap: 0.72rem; padding: 0.72rem; }
    .month-header { padding: 0.76rem 0.74rem; }
}
</style>
</head>

<body>
<div class="calendar-shell">

<h2 class="page-title">
    My Weekly Progress - <?= $year ?>
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
                <?= $week['start'] ?> to <?= $week['end'] ?>
            </div>

            <div class="stat started"><span>Started</span><strong><?= $week['Assigned'] ?></strong></div>
            <div class="stat due"><span>Due</span><strong><?= $week['Due'] ?></strong></div>
            <div class="stat done"><span>Done</span><strong><?= $week['Done'] ?></strong></div>

            <hr class="tile-divider">

            <!-- Optional drill-down links (change page name if needed) -->
            <div class="week-links">
                <a href="index.php?page=my_progress_period&p=<?= $week['period'] ?>&w=1">Started</a>
                <a href="index.php?page=my_progress_period&p=<?= $week['period'] ?>&w=2">Due</a>
                <a href="index.php?page=my_progress_period&p=<?= $week['period'] ?>&w=3">Done</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endfor; ?>

</div>
</body>
</html>
