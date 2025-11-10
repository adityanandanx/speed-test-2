<?php
$source = imagecreatefromjpeg('original.jpg');
list($width, $height) = getimagesize('original.jpg');

$dest = imagecreatetruecolor($width, $height);
$cell_size = (int)($_GET['cell_size'] ?? 50);
if ($cell_size <= 0) $cell_size = 50;

for ($y = 0; $y < $height; $y += $cell_size) {
    for ($x = 0; $x < $width; $x += $cell_size) {

        $currentCellWidth = min($cell_size, $width - $x);
        $currentCellHeight = min($cell_size, $height - $y);

        $pixel = imagecreatetruecolor(1, 1);

        imagecopyresized(
            $pixel,             // Destination image
            $source,            // Source image
            0,
            0,               // Destination X, Y (Start at top-left of $pixel)
            $x,
            $y,             // Source X, Y (Get from main image)
            1,
            1,             // Destination Width, Height
            $currentCellWidth,  // Source Width
            $currentCellHeight  // Source Height
        );

        imagecopyresized(
            $dest,              // Destination image (our final mosaic)
            $pixel,             // Source image (the 10x10)
            $x,
            $y,             // Destination X, Y
            0,
            0,               // Source X, Y
            $currentCellWidth,  // Destination Width
            $currentCellHeight, // Destination Height
            1,
            1              // Source Width, Height
        );

        imagedestroy($pixel);
    }
}

header('Content-Type: image/jpeg');
imagejpeg($dest);
imagedestroy($dest);
imagedestroy($source);
