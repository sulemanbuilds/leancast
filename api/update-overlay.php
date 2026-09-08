<?php
/**
 * Lean Cast — Broadcast State Update API Based Working
 * Receives the current control state and safely persists it to data/broadcast.json.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'POST request required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
    exit;
}

$dataFile = __DIR__ . '/../data/broadcast.json';
$existing = [];

if (is_file($dataFile)) {
    $existing = json_decode(file_get_contents($dataFile), true);
    if (!is_array($existing)) {
        $existing = [];
    }
}

$activeOverlay = isset($input['active_overlay']) && is_string($input['active_overlay'])
    ? trim($input['active_overlay'])
    : ($existing['active_overlay'] ?? 'main');

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $activeOverlay)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid overlay identifier']);
    exit;
}

$overlay = $input['overlays'][$activeOverlay] ?? ($existing['overlays'][$activeOverlay] ?? null);
if (!is_array($overlay)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Overlay data is missing']);
    exit;
}

$clean = [
    'lower_third' => [
        'enabled' => !empty($overlay['lower_third']['enabled']),
        'name' => trim((string)($overlay['lower_third']['name'] ?? '')),
        'role' => trim((string)($overlay['lower_third']['role'] ?? '')),
        'location' => trim((string)($overlay['lower_third']['location'] ?? '')),
    ],
    'breaking_news' => [
        'enabled' => !empty($overlay['breaking_news']['enabled']),
        'headline' => trim((string)($overlay['breaking_news']['headline'] ?? '')),
    ],
    'ticker' => [
        'enabled' => !empty($overlay['ticker']['enabled']),
        'label' => trim((string)($overlay['ticker']['label'] ?? 'HYDERABAD')),
        'text' => trim((string)($overlay['ticker']['text'] ?? '')),
    ],
];

$allOverlays = isset($existing['overlays']) && is_array($existing['overlays'])
    ? $existing['overlays']
    : [];
$allOverlays[$activeOverlay] = $clean;

/* -----------------------------------------------------------
   Broadcast design (new)
   A whitelist keeps this safe to use directly as a lookup key
   on the frontend. If the request doesn't include a design
   choice (e.g. the operator only edited overlay text), the
   previously persisted design is kept as-is — saving overlay
   content should never silently reset the active design.
   ----------------------------------------------------------- */
$validDesigns = ['zaviya', 'fanoos', 'sitara'];
$requestedDesign = isset($input['active_design']) && is_string($input['active_design'])
    ? trim($input['active_design'])
    : null;
$activeDesign = in_array($requestedDesign, $validDesigns, true)
    ? $requestedDesign
    : ($existing['active_design'] ?? 'zaviya');

$output = [
    'active_overlay' => $activeOverlay,
    'active_design' => $activeDesign,
    'overlays' => $allOverlays,
    'updated_at' => time(),
];

$json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not encode broadcast state']);
    exit;
}

$tempFile = $dataFile . '.tmp';
if (file_put_contents($tempFile, $json . PHP_EOL, LOCK_EX) === false || !rename($tempFile, $dataFile)) {
    @unlink($tempFile);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save broadcast state']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Broadcast state updated',
    'data' => $output,
]);