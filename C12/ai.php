<?php
$cellSize = isset($_GET['cell_size']) ? (int)$_GET['cell_size'] : 50;

if ($cellSize <= 0) {
    $cellSize = 50;
}

$sourceFile = 'original.jpg';
$sourceImage = @imagecreatefromjpeg($sourceFile);

$width = imagesx($sourceImage);
$height = imagesy($sourceImage);

// --- 3. Create Mosaic Image ---

$mosaicImage = imagecreatetruecolor($width, $height);

// --- 4. Process the Mosaic ---

// Iterate over the image in a grid, stepping by $cellSize.
for ($y = 0; $y < $height; $y += $cellSize) {
    for ($x = 0; $x < $width; $x += $cellSize) {

        // Define the size of the current cell.
        // This handles edge cases where the image size isn't a perfect
        // multiple of $cellSize.
        $currentCellWidth = min($cellSize, $width - $x);
        $currentCellHeight = min($cellSize, $height - $y);

        // **OPTIMIZED: Get Average Color**
        // 1. Create a tiny 1x1 pixel temporary image.
        $avgTempImage = imagecreatetruecolor(1, 1);

        // 2. Copy the cell from the source image and resize it down to 1x1.
        // This process automatically averages all the pixels.
        imagecopyresampled(
            $avgTempImage,      // Destination image (1x1)
            $sourceImage,       // Source image
            0,
            0,               // Destination X, Y (0,0)
            $x,
            $y,             // Source X, Y
            1,
            1,               // Destination Width, Height (1,1)
            $currentCellWidth,  // Source Width
            $currentCellHeight  // Source Height
        );

        // 3. Get the color of the single pixel in our 1x1 image.
        $rgb = imagecolorat($avgTempImage, 0, 0);

        // 4. Allocate this average color in our main mosaic image.
        $avgColor = imagecolorallocate(
            $mosaicImage,
            ($rgb >> 16) & 0xFF,
            ($rgb >> 8) & 0xFF,
            $rgb & 0xFF
        );

        // 5. Draw the filled rectangle on the mosaic image.
        // We use $x + $currentCellWidth - 1 for the end coordinates.
        imagefilledrectangle(
            $mosaicImage,
            $x,
            $y,
            $x + $currentCellWidth - 1,
            $y + $currentCellHeight - 1,
            $avgColor
        );

        // 6. Free the memory of the temporary 1x1 image.
        imagedestroy($avgTempImage);
    }
}

// --- 5. Output and Cleanup ---

// Set the content-type header to tell the browser this is a JPEG image.
header('Content-Type: image/jpeg');

// Output the final mosaic image to the browser.
imagejpeg($mosaicImage);

// Free the memory associated with the main image resources.
imagedestroy($sourceImage);
imagedestroy($mosaicImage);
