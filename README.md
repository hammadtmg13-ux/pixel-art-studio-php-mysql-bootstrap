# PixelCraft - Interactive Pixel Art Studio

An interactive pixel art studio built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Draw pixel by pixel on a colourful canvas, save your artwork to the gallery, and like other artists' work.

## Features
- Interactive pixel canvas: click and drag to draw, with touch support
- 12 colour palette plus a custom colour picker
- Brush, eraser and flood fill tools
- Grid sizes 16 x 16, 24 x 24 and 32 x 32
- Clear the canvas with one click
- Save artwork with a title and artist name to MySQL
- Community gallery with mini previews of every saved artwork
- Like artworks and load any artwork back into the canvas to edit it
- Delete artworks you no longer want
- Stats: artworks saved, total likes, artists and pixels drawn today

## Tech stack
- HTML and CSS
- JavaScript
- Bootstrap 5
- PHP with PDO
- MySQL

## Setup
1. Create the database and tables using `database.sql`:
```sql
mysql -u root -p < database.sql
```
2. Update the database details in `config.php` if needed.
3. Copy the project folder to your web server, for example `htdocs` for XAMPP.
4. Open `http://localhost/pixel-art-studio-php-mysql/` in your browser.

## Database
Database name: `pixelcraft`

Table: `artworks`
- id: INT, auto increment, primary key
- title: VARCHAR(255)
- artist: VARCHAR(255)
- grid_size: INT, 16, 24 or 32
- pixels: TEXT, JSON of cell index to hex colour
- likes: INT
- created_at: TIMESTAMP, default current timestamp

## Project structure
- `index.php` - main page, drawing studio, stats and gallery
- `config.php` - MySQL connection with PDO
- `save_art.php` - saves a finished artwork to the database
- `delete_art.php` - deletes an artwork
- `like_art.php` - adds a like to an artwork
- `load_art.php` - returns an artwork's pixels as JSON
- `database.sql` - database and table setup with sample artworks
- `assets/css/style.css` - custom styles
- `assets/js/script.js` - canvas drawing, palette, tools and gallery actions

## Author
Built by Hammad Khan
