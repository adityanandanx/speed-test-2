<?php
define("DATA_FILE", 'data.txt');


function findCodeForUrl($url): string|null
{
    $handle = fopen(DATA_FILE, 'r');
    if ($handle !== false) {
        while (($line = fgets($handle)) !== false) {
            $u = preg_split('/ ~ /', $line);
            if ($u !== false && isset($u[0]) && $u[0] === $url) {
                fclose($handle);
                return $u[1];
            }
        }
    }
    fclose($handle);
    return null;
}
function findUrlForCode($code): string|null
{
    $handle = fopen(DATA_FILE, 'r');
    if ($handle !== false) {
        while (($line = fgets($handle)) !== false) {
            $u = preg_split('/ ~ /', $line);
            if ($u !== false && isset($u[1]) && $u[1] == (string)$code) {
                fclose($handle);
                return $u[0];
            }
        }
    }
    fclose($handle);
    return null;
}
function writeCodeForUrl($url)
{
    $code = createCode();
    file_put_contents(DATA_FILE, "{$url} ~ {$code}" . PHP_EOL, FILE_APPEND | LOCK_EX);
    return $code;
}


function createCode()
{
    return random_int(100000, 999999 + 1);
}


if (isset($_GET['id'])) {
    $code = $_GET['id'];
    $url = findUrlForCode($code);
    if ($url !== null) {
        header("Location: {$url}");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C9</title>
</head>

<body>
    <form action="" method="post">
        <input type="text" name="url" placeholder="Input url" id="url" value="<?= $_POST['url'] ?>">
        <button>Create</button>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['url'])) {
        $url = $_POST['url'];
        if (empty($url)) {
            echo "Invalid url";
        } else {
            $code = findCodeForUrl($url);
            if ($code !== null) {
                echo "Already exists: <br>{$url}: {$code}";
                echo "<a href='?id={$code}'>Link</a>";
            } else {
                $code = writeCodeForUrl($url);
                echo "Created: <br>" . $url . ": " . $code;
                echo "<br><a href='?id={$code}'>Link</a>";
            }
        }
    }

    ?>


</body>

</html>