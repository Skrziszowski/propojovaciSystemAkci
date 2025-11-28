<?php

require __DIR__ . '/config/settings.php';
require __DIR__ . '/app/Models/DatabaseModel.php';

// žádná session

$userId = filter_input(INPUT_GET, 'uid', FILTER_VALIDATE_INT);
if (!$userId) {
    http_response_code(400);
    exit;
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

ignore_user_abort(true);
set_time_limit(0);

while (ob_get_level() > 0) {
    ob_end_flush();
}

$db = new DatabaseModel();
$lastCount = -1;

while (!connection_aborted()) {
    $count = $db->getUnreadMessagesCountForUser($userId);

    if ($count !== $lastCount) {
        $lastCount = $count;

        $payload = json_encode([
            'type'  => 'messages',
            'count' => $count,
            'time'  => date('H:i:s'),
        ]);

        echo "data: {$payload}\n\n";
        @flush();
    }

    sleep(5);
}
