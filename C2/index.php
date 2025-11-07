<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C2</title>
</head>

<body>
    <h1>CSV File Parser</h1>
    <p>Select CSV file (first column = category, second column = value):</p>
    <form action="index.php" enctype="multipart/form-data" method="post">
        <input type="file" name="csv" id="csv">
        <button>Upload and parse</button>
    </form>

    <h2>Results</h2>
    <div>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
            $path = $_FILES['csv']['name'];

            if (($handle = fopen($path, 'r')) !== false) {
                fgetcsv($handle);
                $totals = [];
                while (($data = fgetcsv($handle)) !== false) {
                    $category = $data[0];
                    $value = floatval($data[1]);
                    if (!isset($totals[$category])) {
                        $totals[$category] = 0;
                    }
                    $totals[$category] += $value;
                }

                foreach ($totals as $category => $value) {
                    echo $category . ": " . number_format($value, 2, '.') . "<br><br>";
                }
            }
        }
        ?>
    </div>
</body>

</html>