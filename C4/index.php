<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C4</title>
    <style>
        * {
            box-sizing: border-box;
        }

        textarea {
            display: block;
            width: 100%;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <form action="" method="post">
        <textarea name="jsoninp" rows="20" id="jsoninp">[
  {
    "title": "Team Meeting",
    "start": "2024-01-15 09:00:00",
    "end": "2024-01-15 10:00:00",
    "priority": 5
  },
  {
    "title": "Client Call",
    "start": "2024-01-15 09:30:00",
    "end": "2024-01-15 10:30:00",
    "priority": 8
  },
  {
    "title": "Code Review",
    "start": "2024-01-15 10:15:00",
    "end": "2024-01-15 11:00:00",
    "priority": 6
  },
  {
    "title": "Lunch Break",
    "start": "2024-01-15 12:00:00",
    "end": "2024-01-15 13:00:00",
    "priority": 3
  },
  {
    "title": "Project Demo",
    "start": "2024-01-15 14:00:00",
    "end": "2024-01-15 15:00:00",
    "priority": 9
  },
  {
    "title": "Training Session",
    "start": "2024-01-15 14:30:00",
    "end": "2024-01-15 16:00:00",
    "priority": 4
  },
  {
    "title": "Daily Standup",
    "start": "2024-01-15 08:30:00",
    "end": "2024-01-15 09:15:00",
    "priority": 7
  }
]</textarea>
        <button>Submit</button>
    </form>

    <?php

    $original = [];
    $final = [];
    $removed = [];

    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['jsoninp'])) {
        $inp = $_POST['jsoninp'];
        $json = json_decode($inp, true);
        $original = $json;
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p class=\"error\">Invalid JSON input: Syntax error</p>";
            exit;
        }

        usort($json, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        foreach ($json as $event) {
            $event_start = strtotime($event['start']);
            $event_end = strtotime($event['end']);

            $collision = false;
            $conflicts_with = null;
            foreach ($final as $scheduled) {
                $sch_start = strtotime($scheduled['start']);
                $sch_end = strtotime($scheduled['end']);

                if (($event_start < $sch_end) && ($event_end > $sch_start)) {
                    $collision = true;
                    $conflicts_with = $scheduled['title'];
                    break;
                }
            }

            if ($collision) {
                $removed[] = $event['title'] . " (priority " . $event['priority'] . ") - conflicts with " . $conflicts_with;
            } else {
                $final[] = $event;
            }
        }

        usort($final, function ($a, $b) {
            return strtotime($b['start']) <=> strtotime($a['start']);
        });
    } else {
        echo "<p class=\"error\">Please submit JSON input</p>";
    }
    ?>

    <h2>Original Events (<?= count($original) ?>)</h2>
    <?php foreach ($original as $event): ?>
        <p>
            <span><?= $event['title'] ?></span> |
            <span><?= $event['start'] ?></span> -
            <span><?= $event['end'] ?></span> |
            <span>Priority: <?= $event['priority'] ?></span>
        </p>
    <?php endforeach; ?>
    <h2>Final Schedule (<?= count($final) ?>)</h2>
    <?php foreach ($final as $event): ?>
        <p>
            <span><?= $event['title'] ?></span> |
            <span><?= $event['start'] ?></span> -
            <span><?= $event['end'] ?></span> |
            <span>Priority: <?= $event['priority'] ?></span>
        </p>
    <?php endforeach; ?>
    <h2>Removed Events</h2>
    <?php foreach ($removed as $event): ?>
        <p class="error">
            <?= $event ?>
        </p>
    <?php endforeach; ?>
    <h2>JSON Output</h2>
    <pre><?= json_encode($final, JSON_PRETTY_PRINT) ?></pre>

</body>

</html>