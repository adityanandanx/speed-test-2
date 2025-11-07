<?php

define("DATA_FILE", './data.txt');
define("PERPAGE", 5);

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $name = trim($_POST['name'] ?? "");
    $message = trim($_POST['message'] ?? "");

    if (!empty($name) && !empty($message)) {
        $row = [
            'name' => $name,
            'message' => $message,
            'date' => date('Y-m-d H:i:s'),
        ];
    }

    file_put_contents(DATA_FILE, json_encode($row) . PHP_EOL, FILE_APPEND | LOCK_EX);

    header('Location: index.php');
    exit;
}

$all = [];

if (file_exists(DATA_FILE)) {
    $lines = file(DATA_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $all[] = json_decode($line, true);
    }

    $all = array_reverse($all);
}

$total = count($all);
$totalPages = $total === 0 ? 1 : ceil($total / PERPAGE);

$page = $_GET['page'] ?? 0;
$offset = $page * PERPAGE;
$currentPage = array_slice($all, $offset, PERPAGE);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C1</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: aliceblue;
            font-family: sans-serif;
        }

        .msg {
            padding: 1rem;
            background-color: white;
            margin: 0.5rem 0;
        }

        .name {
            font-weight: bold;
        }

        .date {
            font-style: italic;
        }

        input,
        textarea {
            display: block;
            width: 100%;
            margin: 0.5rem 0;

        }

        button {
            display: block;
        }

        .pagination {
            display: flex;
            gap: 1rem;

        }

        a {
            text-decoration: none;
            color: blue;
        }

        a.active {
            color: black;
        }
    </style>
</head>

<body>
    <h1>Guestbook</h1>
    <form action="index.php" method="post">
        <input type="text" name="name" id="name" placeholder="Name">
        <textarea rows="4" name="message" id="message" placeholder="Message..."></textarea>
        <button>Submit</button>
    </form>
    <h2>Messages</h2>
    <?php foreach ($currentPage as $msg): ?>
        <div class="msg">
            <span class="name"><?= htmlspecialchars($msg['name']) ?></span>
            <span class="date"><?= htmlspecialchars($msg['date']) ?></span>
            <p class="message"><?= htmlspecialchars($msg['message']) ?></p>
        </div>
    <?php endforeach; ?>

    <div class="pagination">

        <?php if ($page > 0): ?>
            <a href="index.php?page=<?= $page - 1 ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 0; $i < $totalPages; $i++): ?>
            <a class="<?= (int)$page === $i ? 'active' : '' ?>" href="index.php?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages - 1): ?>
            <a href="index.php?page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
    </div>
</body>

</html>