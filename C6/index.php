<?php
function findAnagrams()
{
    $groups = [];
    $handle = fopen("dictionary.txt", 'r');
    if ($handle === false) die("Invalid");

    while (($line = fgets($handle)) !== false) {
        $word = trim($line);
        $key = str_split($word);
        sort($key);
        $key = implode('', $key);
        $groups[$key][] = $word;
    }
    fclose($handle);

    return array_filter($groups, function ($grp) {
        return count($grp) > 1;
    });
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groups = findAnagrams();
    $word = $_POST['word'];
    $key = str_split($word);
    sort($key);
    $key = implode('', $key);
    if (array_key_exists($key, $groups)) {
        $results = array_filter($groups[$key], fn($val) => $val != $word);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C6</title>
</head>

<body>
    <h1>Anagram Finder</h1>
    <form action="" method="post">
        <input type="text" name="word" id="word" value="<?= $_POST['word'] ?? "" ?>">
        <button>Find Anagrams</button>
    </form>
    <h2>Results for "<?= $_POST['word'] ?>"</h2>
    <?php if (!empty($results)): ?>
        <ul>
            <?php foreach ($results as $r): ?>
                <li><?= htmlspecialchars($r) ?></li>
            <?php endforeach ?>
        </ul>
    <?php else: ?>
        <p>No anagrams found.</p>
    <?php endif; ?>
</body>

</html>