<?php
// --- Initialize Variables ---
$json_input = '';
$error_message = '';
$original_events = [];
$final_schedule = [];
$removed_events = [];
$json_output = '';

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_input = $_POST['json_input'] ?? '';

    if (empty($json_input)) {
        $error_message = 'Please submit JSON input.';
    } else {
        // --- 1. Decode JSON Input ---
        $events = json_decode($json_input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid JSON input: ' . json_last_error_msg();
        } else {
            // --- 2. Core Conflict Resolution Logic ---
            $original_events = $events;

            // Sort events by priority, descending (highest priority first)
            usort($events, function ($a, $b) {
                return $b['priority'] <=> $a['priority'];
            });

            foreach ($events as $event) {
                $has_conflict = false;
                $conflicting_with_title = '';

                $event_start_time = strtotime($event['start']);
                $event_end_time = strtotime($event['end']);

                // Check against the *already accepted* final schedule
                foreach ($final_schedule as $scheduled_event) {
                    $scheduled_start_time = strtotime($scheduled_event['start']);
                    $scheduled_end_time = strtotime($scheduled_event['end']);

                    // Overlap check: (StartA < EndB) and (EndA > StartB)
                    if (($event_start_time < $scheduled_end_time) && ($event_end_time > $scheduled_start_time)) {
                        $has_conflict = true;
                        $conflicting_with_title = $scheduled_event['title'];
                        break; // Found a conflict, no need to check further
                    }
                }

                // If no conflict, add to final schedule.
                // If conflict, add to removed list. (The higher priority item is already in)
                if ($has_conflict) {
                    $removed_events[] = [
                        'title' => $event['title'],
                        'priority' => $event['priority'],
                        'reason' => "conflicts with {$conflicting_with_title} (Priority {$scheduled_event['priority']})"
                    ];
                } else {
                    $final_schedule[] = $event;
                }
            }

            // --- 3. Sort Final Schedule by Start Time ---
            // The request requires the *final* list to be sorted by time.
            usort($final_schedule, function ($a, $b) {
                return strtotime($a['start']) <=> strtotime($b['start']);
            });

            // --- 4. Prepare JSON Output ---
            $json_output = json_encode($final_schedule, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
    }
}

/**
 * Helper function to format an event for display.
 */
function format_event($event)
{
    return sprintf(
        "%s: %s - %s | Priority: %s",
        htmlspecialchars($event['title']),
        htmlspecialchars(date('H:i', strtotime($event['start']))),
        htmlspecialchars(date('H:i', strtotime($event['end']))),
        htmlspecialchars($event['priority'])
    );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline Conflict Resolver</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 900px;
            margin: auto;
            background-color: #f9f9f9;
        }

        h2,
        h3 {
            color: #333;
        }

        textarea {
            width: 100%;
            min-height: 150px;
            font-family: monospace;
            font-size: 14px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .results {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            background-color: #f4f4f4;
            padding: 8px 12px;
            margin-bottom: 5px;
            border-radius: 3px;
        }

        pre {
            background-color: #2d2d2d;
            color: #f1f1f1;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>

<body>

    <h2>Timeline Conflict Resolver</h2>

    <form method="post" action="">
        <label for="json_input">Paste JSON here:</label>
        <textarea id="json_input" name="json_input"><?= htmlspecialchars($json_input) ?></textarea>
        <br>
        <input type="submit" value="Submit">
    </form>

    <?php if (!empty($error_message)): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <?php if (!empty($final_schedule) || !empty($removed_events)): ?>
        <div class="results">

            <h3>Original Events (<?= count($original_events) ?>)</h3>
            <ul>
                <?php foreach ($original_events as $event): ?>
                    <li><?= format_event($event) ?></li>
                <?php endforeach; ?>
            </ul>

            <hr>

            <h3>Final Schedule (<?= count($final_schedule) ?>)</h3>
            <ul>
                <?php foreach ($final_schedule as $event): ?>
                    <li style="background-color: #e0f2f1;"><?= format_event($event) ?></li>
                <?php endforeach; ?>
            </ul>

            <hr>

            <h3>Removed Events (<?= count($removed_events) ?>)</h3>
            <ul>
                <?php foreach ($removed_events as $removed): ?>
                    <li style="background-color: #ffebee;">
                        <?= htmlspecialchars($removed['title']) ?> (Priority <?= htmlspecialchars($removed['priority']) ?>) - <?= htmlspecialchars($removed['reason']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <hr>

            <h3>JSON Output</h3>
            <pre><code><?= htmlspecialchars($json_output) ?></code></pre>

        </div>
    <?php endif; ?>

</body>

</html>