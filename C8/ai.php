<?php
// Initialize the output variable
$ascii_output = "";
$error_message = "";

// Define constraints
const MAX_WIDTH = 200;
const MAX_HEIGHT = 80;

// Check if the form was submitted and a file was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["imageInput"])) {

    // Check for upload errors
    if ($_FILES["imageInput"]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["imageInput"]["tmp_name"];

        // Get image properties
        // Suppress errors from getimagesize for unsupported types
        $image_info = @getimagesize($tmp_name);

        if ($image_info) {
            list($original_width, $original_height, $image_type) = $image_info;

            // Create an image resource from the uploaded file
            $source_image = null;
            switch ($image_type) {
                case IMAGETYPE_JPEG:
                    $source_image = @imagecreatefromjpeg($tmp_name);
                    break;
                case IMAGETYPE_PNG:
                    $source_image = @imagecreatefrompng($tmp_name);
                    break;
                case IMAGETYPE_GIF:
                    $source_image = @imagecreatefromgif($tmp_name);
                    break;
                default:
                    $error_message = "Unsupported image type. Please use JPEG, PNG, or GIF.";
            }

            if ($source_image) {
                // --- 1. Calculate new dimensions ---
                $ratio = $original_width / $original_height;
                $width = $original_width;
                $height = $original_height;

                if ($width > MAX_WIDTH) {
                    $width = MAX_WIDTH;
                    $height = floor($width / $ratio);
                }

                if ($height > MAX_HEIGHT) {
                    $height = MAX_HEIGHT;
                    $width = floor($height * $ratio);
                }

                // Ensure dimensions are at least 1px
                $width = max(1, (int)$width);
                $height = max(1, (int)$height);

                // --- 2. Create a new, scaled-down image in memory ---
                $scaled_image = imagecreatetruecolor($width, $height);

                // Copy and resize the original image to the scaled-down canvas
                imagecopyresampled(
                    $scaled_image,  // Destination image
                    $source_image,   // Source image
                    0,
                    0,            // Destination x, y
                    0,
                    0,            // Source x, y
                    $width,
                    $height, // Destination width, height
                    $original_width,
                    $original_height // Source width, height
                );

                // --- 3. Loop through pixels and build ASCII string ---
                $ascii_art = "";
                for ($y = 0; $y < $height; $y++) {
                    for ($x = 0; $x < $width; $x++) {

                        // Get the color of the pixel
                        $rgb = @imagecolorat($scaled_image, $x, $y);

                        // Extract R, G, B values
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;

                        // 5. Convert to grayscale using weighted average
                        $brightness = (0.299 * $r + 0.587 * $g + 0.114 * $b);

                        // 6. Map brightness to '@' or '.'
                        if ($brightness < 128) {
                            $ascii_art .= '@';
                        } else {
                            $ascii_art .= '.';
                        }
                    }
                    // Add a newline at the end of each row
                    $ascii_art .= "\n";
                }

                $ascii_output = $ascii_art;

                // --- 4. Clean up resources ---
                imagedestroy($source_image);
                imagedestroy($scaled_image);
            } elseif (!$error_message) {
                $error_message = "Could not process the image. It might be corrupt.";
            }
        } else {
            $error_message = "Invalid file. Not recognized as an image.";
        }
    } elseif ($_FILES["imageInput"]["error"] != UPLOAD_ERR_NO_FILE) {
        // Display an error if something else went wrong
        $error_message = "Error during file upload. Code: " . $_FILES["imageInput"]["error"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCII Art Converter (PHP)</title>

    <!-- CSS styles are embedded in a <style> tag -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: grid;
            place-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            box-sizing: border-box;
        }

        main {
            width: 100%;
            max-width: 800px;
            background: #fff;
            border-radius: 8px;
            padding: 1rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h1,
        h2 {
            text-align: center;
            color: #222;
        }

        .controls {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }

        @media (min-width: 600px) {
            .controls {
                flex-direction: row;
            }
        }

        input[type="file"] {
            flex-grow: 1;
            width: 100%;
        }

        button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
        }

        @media (min-width: 600px) {
            button {
                width: auto;
            }
        }

        button:hover {
            background-color: #0056b3;
        }

        /* --- Critical ASCII Output Styling --- */
        #output {
            font-family: "Courier New", Courier, monospace;
            font-size: 8px;
            line-height: 0.8;
            letter-spacing: 0.5px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 1rem;
            overflow-x: auto;
            white-space: pre;
            word-wrap: normal;
        }

        .error {
            color: red;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- 
      This <form> submits the file to index.php (itself).
      - method="POST" is required to send data.
      - enctype="multipart/form-data" is required for file uploads.
    -->
    <form method="POST" enctype="multipart/form-data">
        <main>
            <h1>ASCII Art Converter (PHP)</h1>
            <div class="controls">
                <!-- 
                  The input needs a "name" attribute ("imageInput")
                  so PHP can find it in the $_FILES array.
                  "required" ensures the form can't be submitted empty.
                -->
                <input type="file" id="imageInput" name="imageInput" accept="image/*" required>

                <!-- This is now a submit button -->
                <button type="submit">Convert</button>
            </div>

            <h2>Result</h2>

            <?php
            // Display an error message if one occurred
            if (!empty($error_message)) {
                echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
            }
            ?>

            <!-- 
              The <pre> tag prints the $ascii_output variable from our PHP script.
              htmlspecialchars() is a security measure to prevent XSS attacks.
            -->
            <pre id="output"><?php echo htmlspecialchars($ascii_output); ?></pre>
        </main>
    </form>

    <!-- No JavaScript is included or needed -->
</body>

</html>