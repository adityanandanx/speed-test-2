<?php
if (isset($_GET["keyword"])) {
    $keyword = $_GET['keyword'] ?? "";

    $files = glob('source/*.txt');
    $result = [];
    $summary = [
        'lines' => 0,
        'files' => 0,
    ];
    foreach ($files as $path) {
        $handle = fopen($path, 'r');
        if ($handle) {
            $lineNumber = 1;
            while (($line = fgets($handle))) {
                if (stripos($line, $keyword) !== false) {
                    $result[basename($path)][] = [
                        'n' => $lineNumber,
                        'line' => $line
                    ];
                    $summary['lines']++;
                }
                $lineNumber++;
            }
        }
    }
    $summary['files'] = count($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C3</title>
</head>

<body>
    <h1>File Content Search</h1>
    <form action="index.php" method="get">
        <input type="text" name="keyword" id="keyword" value="<?= $keyword ?>">
        <button>Search all files</button>
    </form>

    <h2>Search Results for "<?= $keyword ?>"</h2>
    <?php foreach ($result as $filename => $value): ?>
        <div>
            <h3>
                File: <?= $filename ?>
            </h3>
            <?php foreach ($value as $v): ?>
                <div>
                    <span style="font-weight: bold;">
                        Line: <?= $v['n'] ?>:
                    </span>
                    <?= $v['line'] ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <p><b>Summary: </b> Found <?= $summary['lines'] ?> matching line(s) in <?= $summary['files'] ?> file(s).</p>
</body>

</html>