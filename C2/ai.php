<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Category Sum Parser</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 2em;
            line-height: 1.6;
        }

        form {
            margin-bottom: 2em;
            padding: 1em;
            background: #f4f4f4;
            border: 1px solid #ddd;
        }

        .results {
            font-family: monospace, monospace;
            background: #333;
            color: #fff;
            padding: 1em;
            border-radius: 5px;
        }

        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>

    <h2>CSV File Parser (Level 2)</h2>
    <p>Select a CSV file to upload (first column = category, second column = value).</p>

    <form action="ai.php" method="post" enctype="multipart/form-data">
        <label for="csvFile">Choose File:</label>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
        <button type="submit" name="submit">Upload and Parse</button>
    </form>

    <div class="results">
        <?php
        /**
         * 2. PHP Processing Logic
         * This code only runs if the form was submitted.
         */

        // Check if the form was submitted and a file was uploaded without errors
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {

            echo "Processing file: " . htmlspecialchars($_FILES["csvFile"]["name"]) . "<br><br>";

            // Get the temporary path where the file was uploaded
            $csvFile = $_FILES["csvFile"]["tmp_name"];

            // An associative array to store the sum for each category
            $categorySums = [];

            // Open the uploaded file for reading
            // 'fopen' is the standard function for opening files
            if (($handle = fopen($csvFile, "r")) !== FALSE) {

                // Get and discard the header row (Category,Value)
                fgetcsv($handle);

                // Loop through the remaining rows in the CSV
                // 'fgetcsv' reads one line from the file handle and parses it as CSV
                while (($data = fgetcsv($handle)) !== FALSE) {

                    // Ensure we have at least 2 columns
                    if (count($data) >= 2) {
                        $category = trim($data[0]);
                        $value = (float)(trim($data[1])); // Convert to float

                        // Initialize the category sum if it doesn't exist
                        if (!isset($categorySums[$category])) {
                            $categorySums[$category] = 0;
                        }

                        // Add the value to the total for this category
                        $categorySums[$category] += $value;
                    }
                }
                // Close the file handle
                fclose($handle);

                // 4. Output the aggregated results
                echo "Results:<br>";
                if (empty($categorySums)) {
                    echo "No data found or processed.";
                } else {
                    foreach ($categorySums as $category => $sum) {
                        echo htmlspecialchars($category) . ": " . number_format($sum, 2, '.', '') . "<br>";
                    }
                }
            } else {
                echo "Error: Could not open the uploaded file.";
            }
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Handle potential upload errors
            echo "Error: No file selected or there was an upload error. Please try again.";
        } else {
            // Default message when the page is first loaded
            echo "Please select a CSV file and click 'Upload and Parse'.";
        }
        ?>
    </div>

</body>

</html>