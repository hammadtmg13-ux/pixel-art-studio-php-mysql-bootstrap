// PixelCraft - interactive pixel drawing studio

const COLORS = [
    '#000000', '#ffffff', '#ff4757', '#ff6b81', '#ffa502',
    '#ffd93d', '#2ed573', '#1e90ff', '#70a1ff', '#6a11cb',
    '#eccc68', '#a4b0be'
];

let gridSize = 16;
let pixels = {};
let currentColor = '#ff4757';
let currentTool = 'brush';
let isDrawing = false;

const canvas = document.getElementById('canvas');
const palette = document.getElementById('palette');
const customColor = document.getElementById('customColor');
const saveMessage = document.getElementById('saveMessage');

function buildCanvas() {
    canvas.innerHTML = '';
    canvas.style.gridTemplateColumns = 'repeat(' + gridSize + ', 1fr)';
    for (let i = 0; i < gridSize * gridSize; i++) {
        const cell = document.createElement('div');
        cell.dataset.index = i;
        if (pixels[i]) {
            cell.style.background = pixels[i];
        }
        canvas.appendChild(cell);
    }
}

function buildPalette() {
    palette.innerHTML = '';
    COLORS.forEach(function (color) {
        const swatch = document.createElement('button');
        swatch.type = 'button';
        swatch.className = 'swatch' + (color === currentColor ? ' active' : '');
        swatch.style.background = color;
        swatch.title = color;
        swatch.addEventListener('click', function () {
            currentColor = color;
            currentTool = 'brush';
            syncTools();
            buildPalette();
        });
        palette.appendChild(swatch);
    });
}

function paintCell(cell) {
    if (!cell) return;
    const index = parseInt(cell.dataset.index, 10);
    if (currentTool === 'eraser') {
        delete pixels[index];
        cell.style.background = '#fff';
    } else if (currentTool === 'fill') {
        floodFill(index, pixels[index] || null, currentColor);
        renderPixels();
    } else {
        pixels[index] = currentColor;
        cell.style.background = currentColor;
    }
}

function floodFill(start, target, replacement) {
    if (target === replacement) return;
    const stack = [start];
    const seen = new Set();
    while (stack.length) {
        const i = stack.pop();
        if (seen.has(i)) continue;
        seen.add(i);
        if ((pixels[i] || null) !== target) continue;
        pixels[i] = replacement;
        const row = Math.floor(i / gridSize);
        const col = i % gridSize;
        if (col > 0) stack.push(i - 1);
        if (col < gridSize - 1) stack.push(i + 1);
        if (row > 0) stack.push(i - gridSize);
        if (row < gridSize - 1) stack.push(i + gridSize);
    }
}

function renderPixels() {
    canvas.querySelectorAll('div').forEach(function (cell) {
        const index = parseInt(cell.dataset.index, 10);
        cell.style.background = pixels[index] || '#fff';
    });
}

canvas.addEventListener('mousedown', function (e) {
    isDrawing = true;
    paintCell(e.target.closest('div'));
});

canvas.addEventListener('mouseover', function (e) {
    if (isDrawing) {
        paintCell(e.target.closest('div'));
    }
});

document.addEventListener('mouseup', function () {
    isDrawing = false;
});

canvas.addEventListener('touchstart', function (e) {
    e.preventDefault();
    isDrawing = true;
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    paintCell(el && el.closest('div'));
}, { passive: false });

canvas.addEventListener('touchmove', function (e) {
    e.preventDefault();
    const touch = e.touches[0];
    const el = document.elementFromPoint(touch.clientX, touch.clientY);
    paintCell(el && el.closest('div'));
}, { passive: false });

canvas.addEventListener('touchend', function () {
    isDrawing = false;
});

function syncTools() {
    document.querySelectorAll('.tool-btn').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.tool === currentTool);
    });
}

document.querySelectorAll('.tool-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        currentTool = btn.dataset.tool;
        syncTools();
    });
});

document.getElementById('clearBtn').addEventListener('click', function () {
    if (confirm('Clear the whole canvas?')) {
        pixels = {};
        renderPixels();
    }
});

document.getElementById('gridSize').addEventListener('change', function (e) {
    gridSize = parseInt(e.target.value, 10);
    pixels = {};
    buildCanvas();
});

customColor.addEventListener('input', function (e) {
    currentColor = e.target.value;
    currentTool = 'brush';
    syncTools();
    buildPalette();
});

document.getElementById('saveBtn').addEventListener('click', function () {
    const title = document.getElementById('artTitle').value.trim() || 'Untitled Art';
    const artist = document.getElementById('artArtist').value.trim() || 'Hammad Khan';
    fetch('save_art.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'title=' + encodeURIComponent(title)
            + '&artist=' + encodeURIComponent(artist)
            + '&grid_size=' + gridSize
            + '&pixels=' + encodeURIComponent(JSON.stringify(pixels))
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.ok) {
            saveMessage.textContent = 'Saved! Reload the page to see it in the gallery.';
        } else {
            saveMessage.textContent = 'Could not save. Please try again.';
        }
    })
    .catch(function () {
        saveMessage.textContent = 'Could not save. Please try again.';
    });
});

document.querySelectorAll('.like-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        fetch('like_art.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + btn.dataset.id
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.ok) {
                btn.querySelector('span').textContent = data.likes;
            }
        });
    });
});

document.querySelectorAll('.load-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        fetch('load_art.php?id=' + btn.dataset.id)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.ok) return;
                gridSize = data.grid_size;
                document.getElementById('gridSize').value = String(gridSize);
                pixels = data.pixels || {};
                document.getElementById('artTitle').value = data.title;
                buildCanvas();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
    });
});

function confirmDelete() {
    return confirm('Delete this artwork?');
}

buildPalette();
buildCanvas();
