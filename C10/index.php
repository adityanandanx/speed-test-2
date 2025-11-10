<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName = pathinfo($_FILES['files']['full_path'][0])['dirname'];
    $zipFile = $folderName . '.zip';
    $zip = new ZipArchive;
    $zip->open($zipFile, ZipArchive::CREATE);

    foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {
        $relPath = $_FILES['files']['full_path'][$i];
        $parts = explode('/', $relPath);
        array_shift($parts);
        $relPath = implode('/', $parts);
        if (str_starts_with($relPath, '.'))
            continue;

        if (is_file($tmp)) {
            $zip->addFile($tmp, $relPath);
        }
    }
    $zip->close();


    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename=\"$zipFile\"");
    header('Content-Length: ' . filesize($zipFile));
    readfile($zipFile);
    unlink($zipFile);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C10</title>
</head>

<body>
    <h1>Upload folder to compress</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" id="files" directory webkitdirectory multiple>
        <button>Compress</button>
    </form>
</body>

</html>