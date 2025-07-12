<?php
// Ensure access directory exists
$accessDir = 'data/access/';
if (!is_dir($accessDir)) {
    mkdir($accessDir, 0755, true);
    mkdir($accessDir . 'ips', 0755, true);
    mkdir($accessDir . 'queue', 0755, true);
}

// Create daily files with date format
$currentDate = date('Y.m.d');
$ipDir = $accessDir . 'ips/';
$queueDir = $accessDir . 'queue/';

// Create daily IP file
$ipDailyFile = $ipDir . $currentDate . '_ips.json';
if (!file_exists($ipDailyFile)) {
    file_put_contents($ipDailyFile, json_encode([]));
}

// Create daily queue file
$queueDailyFile = $queueDir . $currentDate . '_queue.json';
if (!file_exists($queueDailyFile)) {
    file_put_contents($queueDailyFile, json_encode([]));
}

// Get visitor IP
$visitorIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$sanitizedIP = preg_replace('/[^a-zA-Z0-9\.]/', '', $visitorIP);

// Load IP data from daily file
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

// Get current timestamp
$currentTime = time() * 1000; // milliseconds

// Process queue
$queueData = [];
if (file_exists($queueDailyFile)) {
    $queueData = json_decode(file_get_contents($queueDailyFile), true) ?: [];
}

// Clean up expired queue items
foreach ($queueData as $ip => $entry) {
    if ($currentTime - $entry['timestamp'] > 30000) { // 30 seconds expiration
        unset($queueData[$ip]);
    }
}

$queueCount = count($queueData);

// Check if we need to activate queue system
$queueActive = ($queueCount >= 5); // 5 or more in queue

// Pass data to JavaScript
echo "<script>";
echo "const accessData = " . json_encode([
        'ip' => $visitorIP,
        'hitokoto' => $ipData[$sanitizedIP]['hitokoto'],
        'card' => $ipData[$sanitizedIP]['card'],
        'queueCount' => $queueCount,
        'queueActive' => $queueActive,
        'currentTime' => $currentTime
    ]) . ";";
echo "</script>";

// Save updated IP data
file_put_contents($ipDailyFile, json_encode($ipData));
?>