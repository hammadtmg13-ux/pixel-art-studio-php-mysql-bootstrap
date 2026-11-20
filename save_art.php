<?php
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

$title = trim($_POST['title'] ?? 'Untitled Art');
$artist = trim($_POST['artist'] ?? 'Hammad Khan');
$gridSize = (int)($_POST['grid_size'] ?? 16);
$pixels = $_POST['pixels'] ?? '{}';

if (!in_array($gridSize, [16, 24, 32], true)) {
    $gridSize = 16;
}

$decoded = json_decode($pixels, true);
if (!is_array($decoded)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid pixel data']);
    exit;
}

if ($title === '') {
    $title = 'Untitled Art';
}

$stmt = $pdo->prepare('INSERT INTO artworks (title, artist, grid_size, pixels) VALUES (?, ?, ?, ?)');
$stmt->execute([$title, $artist, $gridSize, $pixels]);

echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
