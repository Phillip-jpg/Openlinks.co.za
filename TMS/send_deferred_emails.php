<?php
declare(strict_types=1);

require_once __DIR__ . '/send_email.php';
if (!function_exists('sendEmailNotification')) {
    exit(0);
}

$logFile = __DIR__ . DIRECTORY_SEPARATOR . 'email_worker.log';
$logLine = static function (string $line) use ($logFile): void {
    @file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $line . PHP_EOL, FILE_APPEND);
};

$queueFiles = [];
$argFile = $argv[1] ?? '';
if ($argFile !== '' && is_file($argFile)) {
    $queueFiles[] = $argFile;
}

$pattern = rtrim((string)sys_get_temp_dir(), DIRECTORY_SEPARATOR)
    . DIRECTORY_SEPARATOR
    . 'openlinks_email_queue_*.json';
$pending = glob($pattern) ?: [];
foreach ($pending as $f) {
    if (is_file($f) && !in_array($f, $queueFiles, true)) {
        $queueFiles[] = $f;
    }
}

if (empty($queueFiles)) {
    $logLine('No queue files found.');
    exit(0);
}

foreach ($queueFiles as $queueFile) {
    $logLine('Processing queue file: ' . $queueFile);
    $raw = @file_get_contents($queueFile);
    @unlink($queueFile);
    if ($raw === false || $raw === '') {
        $logLine('Skipped empty/unreadable queue file.');
        continue;
    }

    $jobs = json_decode($raw, true);
    if (!is_array($jobs) || empty($jobs)) {
        $logLine('Skipped invalid queue payload.');
        continue;
    }

    foreach ($jobs as $job) {
        $to = trim((string)($job['to'] ?? ''));
        $subject = (string)($job['subject'] ?? '');
        $message = (string)($job['message'] ?? '');

        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $logLine('Skipped invalid recipient: ' . $to);
            continue;
        }

        try {
            $ok = sendEmailNotification($to, $subject, $message);
            $logLine(($ok ? 'SENT' : 'FAILED') . ' to ' . $to . ' | subject: ' . $subject);
        } catch (Throwable $e) {
            $logLine('EXCEPTION to ' . $to . ' | ' . $e->getMessage());
        }
    }
}

exit(0);
