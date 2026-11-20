<?php
require 'config.php';

$totalArtworks = (int)$pdo->query('SELECT COUNT(*) FROM artworks')->fetchColumn();
$totalLikes = (int)$pdo->query('SELECT COALESCE(SUM(likes), 0) FROM artworks')->fetchColumn();
$totalArtists = (int)$pdo->query('SELECT COUNT(DISTINCT artist) FROM artworks')->fetchColumn();
$pixelsToday = 1240;

$artworks = $pdo->query('SELECT * FROM artworks ORDER BY likes DESC, created_at DESC')->fetchAll();

function render_mini_grid($pixelsJson, $size) {
    $pixels = json_decode($pixelsJson, true);
    if (!is_array($pixels)) {
        $pixels = [];
    }
    $html = '<div class="mini-grid" style="grid-template-columns: repeat(' . (int)$size . ', 1fr);">';
    for ($i = 0; $i < $size * $size; $i++) {
        $color = $pixels[(string)$i] ?? 'transparent';
        $html .= '<div style="background:' . htmlspecialchars($color) . '"></div>';
    }
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PixelCraft - Interactive Pixel Art Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <header class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="logo">PC</div>
            <div>
                <h1 class="h4 mb-0">PixelCraft</h1>
                <small class="text-muted">Interactive Pixel Art Studio</small>
            </div>
        </div>
        <div class="user-chip">
            <span class="avatar avatar-sm">HK</span>
            <span>Hammad Khan</span>
        </div>
    </header>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $totalArtworks ?></div>
                <div class="stat-label">Artworks saved</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $totalLikes ?></div>
                <div class="stat-label">Total likes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $totalArtists ?></div>
                <div class="stat-label">Artists</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= number_format($pixelsToday) ?></div>
                <div class="stat-label">Pixels drawn today</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card studio-card p-4 shadow-sm">
                <h2 class="h5 mb-3">Draw your pixel art</h2>
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <div class="palette" id="palette"></div>
                    <input type="color" id="customColor" value="#ff4757" title="Pick any color">
                </div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" class="btn btn-primary tool-btn active" data-tool="brush">Brush</button>
                    <button type="button" class="btn btn-outline-secondary tool-btn" data-tool="eraser">Eraser</button>
                    <button type="button" class="btn btn-outline-secondary tool-btn" data-tool="fill">Fill</button>
                    <button type="button" class="btn btn-outline-danger" id="clearBtn">Clear</button>
                    <select id="gridSize" class="form-select w-auto">
                        <option value="16" selected>16 x 16 grid</option>
                        <option value="24">24 x 24 grid</option>
                        <option value="32">32 x 32 grid</option>
                    </select>
                </div>
                <div id="canvas" class="pixel-canvas"></div>
                <p class="text-muted small mt-2 mb-4">Tip: click and drag to draw. Pick a color, then paint pixel by pixel.</p>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" id="artTitle" class="form-control" placeholder="Name your artwork..." maxlength="80">
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="artArtist" class="form-control" value="Hammad Khan" maxlength="80">
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="saveBtn" class="btn btn-success w-100">Save artwork</button>
                    </div>
                </div>
                <p id="saveMessage" class="mt-2 mb-0 small text-success"></p>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4 shadow-sm">
                <h2 class="h5 mb-3">Community gallery</h2>
                <?php if (empty($artworks)): ?>
                    <p class="text-muted mb-0">No artworks yet. Be the first to draw one.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($artworks as $a): ?>
                        <div class="col-6">
                            <div class="gallery-item">
                                <?= render_mini_grid($a['pixels'], (int)$a['grid_size']) ?>
                                <div class="mt-2">
                                    <b><?= htmlspecialchars($a['title']) ?></b>
                                    <small class="text-muted d-block">by <?= htmlspecialchars($a['artist']) ?></small>
                                    <div class="d-flex gap-1 mt-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger like-btn" data-id="<?= (int)$a['id'] ?>">♥ <span><?= (int)$a['likes'] ?></span></button>
                                        <button type="button" class="btn btn-sm btn-outline-primary load-btn" data-id="<?= (int)$a['id'] ?>">Load</button>
                                        <a href="delete_art.php?id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirmDelete()">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted mt-4 small">Built by Hammad Khan with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP</footer>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>
