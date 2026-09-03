<?php
/**
 * Lean Cast — Broadcast State API
 * Returns the current data/broadcast.json state for the broadcast screen.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$dataFile = __DIR__ . '/../data/broadcast.json';

if (!is_file($dataFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Broadcast state file not found']);
    exit;
}

$data = json_decode(file_get_contents($dataFile), true);
if (!is_array($data)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Broadcast state is invalid']);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $data,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
