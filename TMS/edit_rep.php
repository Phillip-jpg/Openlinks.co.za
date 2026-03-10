<?php
include 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$loginType = (int)($_SESSION['login_type'] ?? 0);
$loginId = (int)($_SESSION['login_id'] ?? 0);

if ($loginType !== 2 || $loginId <= 0) {
    die('Not authorised');
}


$secret = 'my_app_secret_key';

if (!isset($_GET['id'])) {
    die('Invalid request');
}

$decoded = base64_decode($_GET['id'], true);
if ($decoded === false) {
    die('Invalid ID');
}

list($id, $hash) = explode(':', $decoded, 2);

// validate signature
$expected = hash_hmac('sha256', $id, $secret);
if (!hash_equals($expected, $hash)) {
    die('Tampered ID');
}

$id = (int)$id;

$accessQry = $conn->query("
    SELECT
        SUM(CASE WHEN c.creator_id = {$loginId} THEN 1 ELSE 0 END) AS visible_links,
        SUM(CASE WHEN c.creator_id = {$loginId} AND COALESCE(c.orbiter_id, 0) > 0 THEN 1 ELSE 0 END) AS orbited_links
    FROM client_rep cr
    INNER JOIN yasccoza_openlink_market.client c
        ON c.CLIENT_ID = cr.CLIENT_ID
    WHERE cr.REP_ID = {$id}
");

if (!$accessQry) {
    die('Unable to verify rep access');
}

$accessRow = $accessQry->fetch_assoc();
$visibleLinks = (int)($accessRow['visible_links'] ?? 0);
$orbitedLinks = (int)($accessRow['orbited_links'] ?? 0);

if ($visibleLinks <= 0) {
    die('Rep not found');
}

if ($orbitedLinks > 0) {
    die('Orbited reps cannot be edited');
}

$qry = $conn->query("SELECT * FROM client_rep WHERE REP_ID = ".$id);
if (!$qry || $qry->num_rows === 0) {
    die('Rep not found');
}

$repRow = $qry->fetch_assoc();
foreach ($repRow as $k => $v) {
    $$k = $v;
}

$selectedClientIds = [];
$clientsQry = $conn->query("SELECT CLIENT_ID FROM client_rep WHERE REP_ID = ".$id);
if ($clientsQry) {
    while ($clientRow = $clientsQry->fetch_assoc()) {
        $clientId = (int)$clientRow['CLIENT_ID'];
        if ($clientId > 0) {
            $selectedClientIds[$clientId] = $clientId;
        }
    }
}
$selectedClientIds = array_values($selectedClientIds);
include 'new_client_rep.php';
?>
