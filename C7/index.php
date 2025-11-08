<?php
$menu = "";
$tree = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $menu = $_POST['menu'];
    $lines = preg_split('/\R/', $menu);
    foreach ($lines as $line) {
        $words = preg_split('/ > /', $line);
        addtotree($tree, $words);
    }
    // echo json_encode($tree);
}

function addtotree(&$tree, &$allvalues, $i = 0)
{
    if ($i >= count($allvalues)) {
        return;
    }
    $value = $allvalues[$i];
    if (!isset($tree[$value])) {
        $tree[$value] = [];
    }
    addtotree($tree[$value], $allvalues, $i + 1);
}


function renderTree($tree)
{
    $keys = array_keys($tree);
    if (empty($keys)) {
        return;
    }
    echo '<ul>';
    foreach ($keys as $k) {
        echo "<li>{$k}</li>";
        renderTree($tree[$k]);
    }
    echo '</ul>';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C7</title>
    <style>
        * {
            box-sizing: border-box;
        }

        textarea {
            display: block;
            width: 100%;
            margin: 1rem 0;
        }

        li {
            list-style: disc;
        }
    </style>
</head>

<body>
    <h1>Hierarchical Menu Builder</h1>
    <form action="" method="post">
        <textarea name="menu" id="menu" rows="10"><?= $menu ? $menu : "Food
Food > Fruits
Food > Fruits > Apple
Food > Fruits > Banana
Food > Vegetables
Food > Vegetables > Carrot
Food > Vegetables > Broccoli
Drinks
Drinks > Water
Drinks > Coffee
Drinks > Tea" ?></textarea>
        <button>Build Menu</button>
    </form>

    <h2>Menu Preview</h2>

    <?php
    renderTree($tree);
    ?>
</body>

</html>