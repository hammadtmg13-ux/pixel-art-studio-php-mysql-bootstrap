<?php
require 'config.php';

header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Invalid id']);
    exit;
}

$stmt = $pdo->prepare('UPDATE artworks SET likes = likes + 1 WHERE id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('SELECT likes FROM artworks WHERE id = ?');
$stmt->execute([$id]);
$likes = (int)$stmt->fetchColumn();

echo json_encode(['ok' => true, 'likes' => $likes]);
