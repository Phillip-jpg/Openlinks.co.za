<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

header('Content-Type: text/plain; charset=UTF-8');

if (empty($_SESSION['login_id'])) {
    http_response_code(401);
    echo 'unauthorized';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo 'invalid_method';
    exit;
}

$csrf = (string)($_POST['csrf_token'] ?? '');
if (
    empty($_SESSION['csrf_token']) ||
    $csrf === '' ||
    !hash_equals($_SESSION['csrf_token'], $csrf)
) {
    http_response_code(403);
    echo 'csrf';
    exit;
}

$intervalId = (int)($_POST['interval_id'] ?? 0);
$loginId = (int)$_SESSION['login_id'];

if ($intervalId <= 0) {
    http_response_code(422);
    echo 'invalid_input';
    exit;
}

function ensureIntervalResponsibleColumn(mysqli $conn): void
{
    $hasTeamId = false;
    $teamCheck = $conn->query("SHOW COLUMNS FROM reminder_interval LIKE 'team_id'");
    if ($teamCheck && $teamCheck->num_rows > 0) {
        $hasTeamId = true;
    }
    if (!$hasTeamId) {
        if (!$conn->query("ALTER TABLE reminder_interval ADD COLUMN team_id INT(11) NOT NULL DEFAULT 0 AFTER parent_reminder_id")) {
            throw new RuntimeException('Failed to add team_id column.');
        }
    }

    $hasResponsible = false;
    $responsibleCheck = $conn->query("SHOW COLUMNS FROM reminder_interval LIKE 'responsible'");
    if ($responsibleCheck && $responsibleCheck->num_rows > 0) {
        $hasResponsible = true;
    }
    if (!$hasResponsible) {
        if (!$conn->query("ALTER TABLE reminder_interval ADD COLUMN responsible INT(11) NOT NULL DEFAULT 0 AFTER team_id")) {
            throw new RuntimeException('Failed to add responsible column.');
        }
    }
}

try {
    ensureIntervalResponsibleColumn($conn);

    $read = $conn->prepare("
        SELECT
            ri.id,
            ri.responsible,
            CASE
                WHEN ri.team_id > 0 THEN ri.team_id
                ELSE r.team
            END AS effective_team_id,
            r.who AS parent_who
        FROM reminder_interval ri
        INNER JOIN reminders r
            ON r.id = ri.parent_reminder_id
        WHERE ri.id = ?
        LIMIT 1
    ");
    if (!$read) {
        throw new RuntimeException('prepare_failed');
    }
    $read->bind_param('i', $intervalId);
    $read->execute();
    $current = $read->get_result()->fetch_assoc();
    $read->close();

    if (!$current) {
        http_response_code(404);
        echo 'not_found';
        exit;
    }

    $effectiveTeamId = (int)($current['effective_team_id'] ?? 0);
    $currentResponsible = (int)($current['responsible'] ?? 0);
    $parentWho = (int)($current['parent_who'] ?? 0);

    if ($effectiveTeamId <= 0) {
        http_response_code(422);
        echo 'invalid_team';
        exit;
    }

    $memberCheck = $conn->prepare("
        SELECT 1
        FROM team_schedule
        WHERE team_id = ?
          AND team_members = ?
        LIMIT 1
    ");
    if (!$memberCheck) {
        throw new RuntimeException('prepare_failed');
    }
    $memberCheck->bind_param('ii', $effectiveTeamId, $loginId);
    $memberCheck->execute();
    $isMember = (bool)$memberCheck->get_result()->fetch_assoc();
    $memberCheck->close();

    if (!$isMember) {
        http_response_code(401);
        echo 'unauthorized';
        exit;
    }

    if ($currentResponsible === $loginId) {
        echo 'already_mine';
        exit;
    }

    // Claim is allowed when not assigned yet (0) or still on default parent owner.
    if (!($currentResponsible <= 0 || $currentResponsible === $parentWho)) {
        echo 'claimed';
        exit;
    }

    $update = $conn->prepare("
        UPDATE reminder_interval
        SET responsible = ?
        WHERE id = ?
          AND (responsible <= 0 OR responsible = ?)
    ");
    if (!$update) {
        throw new RuntimeException('prepare_failed');
    }
    $update->bind_param('iii', $loginId, $intervalId, $parentWho);
    $update->execute();
    $affected = $update->affected_rows;
    $update->close();

    if ($affected > 0) {
        echo '1';
        exit;
    }

    $recheck = $conn->prepare("SELECT responsible FROM reminder_interval WHERE id = ? LIMIT 1");
    if (!$recheck) {
        throw new RuntimeException('prepare_failed');
    }
    $recheck->bind_param('i', $intervalId);
    $recheck->execute();
    $row = $recheck->get_result()->fetch_assoc();
    $recheck->close();
    $finalResponsible = (int)($row['responsible'] ?? 0);

    if ($finalResponsible === $loginId) {
        echo 'already_mine';
    } else {
        echo 'claimed';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo 'claim_failed';
}

