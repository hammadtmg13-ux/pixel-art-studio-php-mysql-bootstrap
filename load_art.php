<?php
require 'config.php';

header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM artworks WHERE id = ?');
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) {
    echo json_encode(['ok' => false, 'error' => 'Artwork not found']);
    exit;
}

echo json_encode([
    'ok' => true,
    'id' => (int)$art['id'],
    'title' => $art['title'],
    'artist' => $art['artist'],
    'grid_size' => (int)$art['grid_size'],
    'pixels' => json_decode($art['pixels'], true),
]);
