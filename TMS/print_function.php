<?php 

function getUserType($type) {
    include('db_connect.php');
    switch ($type) {
        case 1:
            return 'Super Admin';
        case 2:
            return 'Project Manager';
        case 3:
            return 'General Admin';
        default:
            return '';
    }
}

function getAssignedWorkTypes($taskIdsString) {
    include('db_connect.php');
    $taskIdsArray = array_map('intval', explode(',', $taskIdsString));
    $qry2 = $conn->query("SELECT task_name,id FROM task_list WHERE id IN (" . implode(',', $taskIdsArray) . ")");
    $taskNames = array();
    while ($row2 = $qry2->fetch_assoc()) {
        $taskNames[] = $row2['task_name'];
    }
    return implode(', ', $taskNames);
}