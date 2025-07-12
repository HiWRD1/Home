<?php
// Ensure access directory exists
$accessDir = 'data/access/';
if (!is_dir($accessDir)) {
    mkdir($accessDir, 0755, true);
    mkdir($accessDir . 'ips', 0755, true);
    mkdir($accessDir . 'queue', 0755, true);
}

// Get current date for file naming
$currentDate = date('Y.m.d');
$ipDailyFile = $accessDir . 'ips/' . $currentDate . '_ips.json';
$queueDailyFile = $accessDir . 'queue/' . $currentDate . '_queue.json';

// Create files if they don't exist
if (!file_exists($ipDailyFile)) {
    file_put_contents($ipDailyFile, json_encode([]));
}
if (!file_exists($queueDailyFile)) {
    file_put_contents($queueDailyFile, json_encode([]));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$ip = $data['ip'] ?? '';
$type = $data['type'] ?? '';
$timestamp = $data['timestamp'] ?? 0;

if (empty($ip) || empty($type)) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

// Sanitize IP
$sanitizedIP = preg_replace('/[^a-zA-Z0-9\.]/', '', $ip);

// Load IP data
$ipData = [];
if (file_exists($ipDailyFile)) {
    $ipData = json_decode(file_get_contents($ipDailyFile), true) ?: [];
}

// Default data for IP
if (!isset($ipData[$sanitizedIP])) {
    $ipData[$sanitizedIP] = [
        'hitokoto' => [
            'last_click' => 0,
            'count' => 0,
            'banned_until' => 0
        ],
        'card' => [
            'last_click' => 0,
            'count' => 0,
            'banned_until' => 0
        ]
    ];
}

// Update data
$accessType = &$ipData[$sanitizedIP][$type];
$currentTime = time() * 1000; // milliseconds

// Reset count if more than 1 minute since first click
if ($currentTime - $accessType['last_click'] > 60000) {
    $accessType['count'] = 0;
}

// Update count and timestamp
$accessType['count']++;
$accessType['last_click'] = $currentTime;

// Check if should be banned
$limit = ($type === 'hitokoto') ? 10 : 5;
if ($accessType['count'] >= $limit) {
    $accessType['banned_until'] = $currentTime + 600000; // 10 minutes
    $accessType['count'] = 0; // reset count after ban
}

// Save updated IP data
file_put_contents($ipDailyFile, json_encode($ipData));

// Update queue data if needed
$queueData = [];
if (file_exists($queueDailyFile)) {
    $queueData = json_decode(file_get_contents($queueDailyFile), true) ?: [];
}

// Add timestamp to queue entry
$queueData[$sanitizedIP] = [
    'timestamp' => $currentTime,
    'type' => $type
];

// Save updated queue data
file_put_contents($queueDailyFile, json_encode($queueData));

// Create log entry with timestamp
$logEntry = "[" . date('H:i:s') . "] " . json_encode([
        'ip' => $ip,
        'type' => $type,
        'action' => 'recorded'
    ]);

// Append to daily log file
$logFile = $accessDir . $currentDate . '_access.log';
file_put_contents($logFile, $logEntry . PHP_EOL, FILE_APPEND);

// Return success
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
exit;