<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Content Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }

        h1,
        h2,
        h3 {
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        .results {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }

        .result-file {
            margin-bottom: 15px;
        }

        .result-line {
            margin-left: 20px;
            font-family: monospace;
            background-color: #f4f4f4;
            padding: 5px;
            display: block;
            border-radius: 3px;
            margin-bottom: 5px;
        }

        .summary {
            font-weight: bold;
            color: #005a9c;
            margin-top: 15px;
        }

        .no-matches {
            color: #777;
        }
    </style>
</head>

<body>

    <h1>File Content Search</h1>

    <form action="ai.php" method="GET">
        <label for="keyword">Enter search keyword:</label>
        <input type="text" id="keyword" name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
        <input type="submit" value="Search All Files">
    </form>

    <?php
    // Check if a keyword was submitted
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
        $sourceDir = 'source/';

        // Find all .txt files in the source directory
        $files = glob($sourceDir . '*.txt');

        $results = [];
        $totalMatches = 0;
        $filesWithMatches = 0;

        echo '<div class="results">';
        echo '<h2>Search Results for "' . htmlspecialchars($keyword) . '":</h2>';

        if (empty($files)) {
            echo '<p class="no-matches">No .txt files found in the \'source\' directory.</p>';
        } else {
            // Loop through each file
            foreach ($files as $file) {
                $handle = fopen($file, 'r');

                if ($handle) {
                    $lineNumber = 1;
                    $fileMatches = [];
                    $filename = basename($file);

                    // Read file line by line
                    while (($line = fgets($handle)) !== false) {
                        // Perform case-insensitive search
                        if (stripos($line, $keyword) !== false) {
                            $fileMatches[] = [
                                'number' => $lineNumber,
                                'line' => htmlspecialchars(trim($line))
                            ];
                            $totalMatches++;
                        }
                        $lineNumber++;
                    }
                    fclose($handle);

                    // If matches were found in this file, store them
                    if (!empty($fileMatches)) {
                        $results[$filename] = $fileMatches;
                        $filesWithMatches++;
                    }
                }
            }

            // Display the results
            if (empty($results)) {
                echo '<p class="no-matches">No matches found for "' . htmlspecialchars($keyword) . '".</p>';
            } else {
                foreach ($results as $filename => $matches) {
                    echo '<div class="result-file">';
                    echo '<h3>File: ' . htmlspecialchars($filename) . '</h3>';
                    foreach ($matches as $match) {
                        echo '<span class="result-line">Line ' . $match['number'] . ': ' . $match['line'] . '</span>';
                    }
                    echo '</div>';
                }
                echo '<p class="summary">Summary: Found ' . $totalMatches . ' matching line(s) in ' . $filesWithMatches . ' file(s).</p>';
            }
        }
        echo '</div>';
    }
    ?>

</body>

</html>