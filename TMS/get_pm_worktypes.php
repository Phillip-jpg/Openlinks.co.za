<?php
include 'db_connect.php';
header('Content-Type: application/json');

$pm_id = isset($_GET['pm_id']) ? (int)$_GET['pm_id'] : 0;
if ($pm_id <= 0) { echo json_encode([]); exit; }

$sql = "
  SELECT DISTINCT tl.id, tl.task_name
  FROM task_list tl
  WHERE tl.creator_id = ?
  ORDER BY tl.task_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pm_id);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($row = $res->fetch_assoc()) {
  $out[] = ["id" => (int)$row["id"], "task_name" => $row["task_name"]];
}

echo json_encode($out);
