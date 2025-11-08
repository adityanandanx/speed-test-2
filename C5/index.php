<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = $_POST['words'];
  $words = preg_split('/(\R)|(\,)/', $input);
  $words = array_map(function ($word) {
    return trim($word);
  }, $words);
  $words = array_filter($words, function ($word) {
    return strlen($word) >= 1;
  });
  $crossword = generateCrossword($words);
}

function generateCrossword($words)
{
  $crossword = array_fill(0, 15, array_fill(0, 15, null));
  usort($words, function ($a, $b) {
    return strlen($b) <=> strlen($a);
  });

  $mid = floor(15 / 2);
  $word = $words[0]; // biggest word
  $len = strlen($word);
  $splitword = str_split($word);
  $start = $mid - floor($len / 2);

  array_splice($crossword[$mid], $start, $len, $splitword);

  return $crossword;
}

echo '<br>';
echo (json_encode($crossword));
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>C5</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: sans-serif;
    }

    textarea {
      width: 100%;
      display: block;
      margin: 1rem 0;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(15, min-content);
      grid-template-rows: repeat(15, min-content);
    }

    .cell {
      width: 2rem;
      height: 2rem;
      border: 1px solid black;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      text-transform: uppercase;
    }

    .debug {
      position: absolute;
      opacity: 0.5;
      user-select: none;
      font-size: 12px;
      left: 0;
      top: 0;
    }
  </style>
</head>

<body>
  <h1>Crossword Generator</h1>
  <form action="" method="post">
    <textarea name="words" id="words" rows="10"><?= isset($words) ? implode(', ', $words) : 'instant
mentioned
automatic
healthcare
viewing
maintained
increasing
majority
connected' ?></textarea>
    <button>Generate</button>
  </form>

  <?php if (isset($crossword)): ?>
    <h2>Grid</h2>
    <div class="grid">
      <?php for ($y = 0; $y < 15; $y++): ?>
        <?php for ($x = 0; $x < 15; $x++): ?>
          <div class="cell">
            <span class="debug"><?= $x . "," . $y ?></span>
            <?= $crossword[$y][$x] ?>
          </div>
        <?php endfor; ?>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</body>

</html>