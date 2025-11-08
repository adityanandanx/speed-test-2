<?php

// Initialize $crossword to null for the HTML check
$crossword = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['words'];
    // Split by newlines (any type) or commas
    $words = preg_split('/(\R)|(\,)/', $input);
    // Clean up whitespace
    $words = array_map(function ($word) {
        return trim(strtoupper($word)); // Also convert to uppercase
    }, $words);
    // Remove any empty entries
    $words = array_filter($words, function ($word) {
        return strlen($word) >= 1;
    });
    // Remove duplicate words
    $words = array_unique($words);

    $crossword = generateCrossword($words);
}

/**
 * Main generator function.
 */
function generateCrossword($words)
{
    $gridSize = 15;
    $grid = array_fill(0, $gridSize, array_fill(0, $gridSize, null));
    $placedWords = [];

    // Sort words by length, longest first. This is a good heuristic.
    usort($words, function ($a, $b) {
        return strlen($b) <=> strlen($a);
    });

    // 1. Anchor the first (longest) word
    $word = array_shift($words);
    $len = strlen($word);
    $mid = floor($gridSize / 2);
    $direction = (rand(0, 1) === 0) ? 'horizontal' : 'vertical';

    if ($direction === 'horizontal') {
        $x = $mid - floor($len / 2);
        $y = $mid;
    } else {
        $x = $mid;
        $y = $mid - floor($len / 2);
    }

    // Place the first word
    placeWordOnGrid($grid, $word, $x, $y, $direction);
    $placedWords[] = ['word' => $word, 'x' => $x, 'y' => $y, 'direction' => $direction];

    // 2. Try to place the remaining words
    // Shuffle to add randomness to which words get placed first if lengths are equal
    shuffle($words);

    foreach ($words as $wordToPlace) {
        $bestPlacement = findBestPlacement($grid, $placedWords, $wordToPlace);

        if ($bestPlacement) {
            placeWordOnGrid($grid, $wordToPlace, $bestPlacement['x'], $bestPlacement['y'], $bestPlacement['direction']);
            // Add this new word to the list of placed words for future intersections
            $placedWords[] = $bestPlacement;
        }
    }

    return $grid;
}

/**
 * Finds all valid placements for a new word and picks one randomly.
 */
function findBestPlacement($grid, $placedWords, $wordToPlace)
{
    $possiblePlacements = [];
    $lenToPlace = strlen($wordToPlace);

    // Check against every word already on the grid
    foreach ($placedWords as $placed) {
        $lenPlaced = strlen($placed['word']);

        // Check every letter of the new word
        for ($i = 0; $i < $lenToPlace; $i++) {
            // Against every letter of the placed word
            for ($j = 0; $j < $lenPlaced; $j++) {

                // Check for an intersection
                if ($wordToPlace[$i] === $placed['word'][$j]) {
                    // Determine new direction (must be perpendicular)
                    $newDirection = ($placed['direction'] === 'horizontal') ? 'vertical' : 'horizontal';

                    // Calculate new (x, y) start position
                    if ($newDirection === 'horizontal') {
                        $newX = $placed['x'] - $i;
                        $newY = $placed['y'] + $j;
                    } else { // vertical
                        $newX = $placed['x'] + $j;
                        $newY = $placed['y'] - $i;
                    }

                    // Check if this placement is valid
                    if (canPlaceWord($grid, $wordToPlace, $newX, $newY, $newDirection)) {
                        $possiblePlacements[] = [
                            'word' => $wordToPlace,
                            'x' => $newX,
                            'y' => $newY,
                            'direction' => $newDirection
                        ];
                    }
                }
            }
        }
    }

    if (empty($possiblePlacements)) {
        return null; // No valid placement found
    }

    // Pick one of the valid placements at random
    return $possiblePlacements[array_rand($possiblePlacements)];
}

/**
 * Checks if a word can be placed at a specific location without conflicts.
 */
function canPlaceWord($grid, $word, $x, $y, $direction)
{
    $gridSize = 15;
    $len = strlen($word);

    // 1. Check if word is fully within bounds
    if ($direction === 'horizontal') {
        if ($x < 0 || ($x + $len) > $gridSize || $y < 0 || $y >= $gridSize) {
            return false;
        }
    } else { // vertical
        if ($y < 0 || ($y + $len) > $gridSize || $x < 0 || $x >= $gridSize) {
            return false;
        }
    }

    // 2. Check for letter conflicts and adjacent ("touching") words
    for ($i = 0; $i < $len; $i++) {
        $cx = ($direction === 'horizontal') ? $x + $i : $x;
        $cy = ($direction === 'horizontal') ? $y : $y + $i;

        $existing = $grid[$cy][$cx];
        $new = $word[$i];

        // If there's a letter, it must match
        if ($existing !== null && $existing !== $new) {
            return false; // Conflicting letter
        }

        // Check for "touching" cells *unless* it's the intersection
        if ($existing === null) {
            if ($direction === 'horizontal') {
                // Check cell above
                if ($cy > 0 && $grid[$cy - 1][$cx] !== null) return false;
                // Check cell below
                if ($cy < $gridSize - 1 && $grid[$cy + 1][$cx] !== null) return false;
            } else { // vertical
                // Check cell to the left
                if ($cx > 0 && $grid[$cy][$cx - 1] !== null) return false;
                // Check cell to the right
                if ($cx < $gridSize - 1 && $grid[$cy][$cx + 1] !== null) return false;
            }
        }
    }

    // 3. Check for letters immediately before or after the word
    if ($direction === 'horizontal') {
        // Before
        if ($x > 0 && $grid[$y][$x - 1] !== null) return false;
        // After
        if (($x + $len) < $gridSize && $grid[$y][$x + $len] !== null) return false;
    } else { // vertical
        // Before
        if ($y > 0 && $grid[$y - 1][$x] !== null) return false;
        // After
        if (($y + $len) < $gridSize && $grid[$y + $len][$x] !== null) return false;
    }

    return true; // All checks passed
}

/**
 * Helper to place the word's letters onto the grid.
 * (Note: $grid is passed by reference)
 */
function placeWordOnGrid(&$grid, $word, $x, $y, $direction)
{
    $len = strlen($word);
    $letters = str_split($word);

    for ($i = 0; $i < $len; $i++) {
        if ($direction === 'horizontal') {
            $grid[$y][$x + $i] = $letters[$i];
        } else { // vertical
            $grid[$y + $i][$x] = $letters[$i];
        }
    }
}

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
            font-weight: bold;
        }

        /* Style empty cells differently */
        .cell:not(.filled) {
            background-color: #eee;
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
        <textarea name="words" id="words" rows="10"><?= isset($_POST['words']) ? htmlspecialchars($_POST['words']) : 'instant
mentioned
automatic
healthcare
viewing
maintained
increasing
majority
connected
program
logic' ?></textarea>
        <button>Generate</button>
    </form>

    <?php if (isset($crossword)): ?>
        <h2>Grid</h2>
        <div class="grid">
            <?php for ($y = 0; $y < 15; $y++): ?>
                <?php for ($x = 0; $x < 15; $x++): ?>
                    <?php $letter = $crossword[$y][$x]; ?>
                    <div class="cell <?= $letter !== null ? 'filled' : '' ?>">
                        <span class="debug"><?= $x . "," . $y ?></span>
                        <?= $letter ?>
                    </div>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</body>

</html>