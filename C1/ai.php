<?php

// ---
// 1. CONFIGURATION
// ---

/**
 * The path to the file where messages are stored.
 * Make sure this file is writable by the web server.
 */
define('DATA_FILE', 'data.txt');

/**
 * How many messages to show on each page.
 */
define('MESSAGES_PER_PAGE', 5);


// ---
// 2. HANDLE FORM SUBMISSION (POST Request)
// ---

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data and trim whitespace
    $name = trim($_POST['name'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation: only save if both fields are non-empty
    if (!empty($name) && !empty($message)) {

        // Create the new entry as an associative array
        $entry = [
            'name'    => $name,
            'message' => $message,
            'date'    => date('Y-m-d H:i:s') // Add a server-side timestamp
        ];

        // Convert the array to a JSON string
        $jsonEntry = json_encode($entry);

        // Append the JSON string to the data file
        // FILE_APPEND: Adds to the end of the file instead of overwriting
        // LOCK_EX: Prevents other processes from writing to the file at the same time
        // PHP_EOL: Adds a newline character to separate entries
        file_put_contents(DATA_FILE, $jsonEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    //
    // Redirect after POST (Post-Redirect-Get Pattern)
    // This prevents the user from re-submitting the form if they refresh the page.
    //
    header('Location: ai.php');
    exit;
}


// ---
// 3. READ AND PREPARE MESSAGES (GET Request)
// ---

$allMessages = [];
if (file_exists(DATA_FILE)) {
    // Read the file into an array, one line per element
    // FILE_IGNORE_NEW_LINES: Removes the newline character from each line
    // FILE_SKIP_EMPTY_LINES: Skips any empty lines
    $lines = file(DATA_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Decode the JSON string back into an associative array
        $decoded = json_decode($line, true);

        // Add to our messages array if decoding was successful
        if ($decoded) {
            $allMessages[] = $decoded;
        }
    }

    // Show newest messages first (as required)
    $allMessages = array_reverse($allMessages);
}


// ---
// 4. PAGINATION LOGIC
// ---

// Get the total number of messages
$totalMessages = count($allMessages);

// Calculate the total number of pages needed
// If 0 messages, we still want 1 page.
$totalPages = $totalMessages > 0 ? ceil($totalMessages / MESSAGES_PER_PAGE) : 1;

// Get the current page number from the URL query string (e.g., ai.php?page=2)
// Default to 1 if not set or invalid
$currentPage = (int)($_GET['page'] ?? 1);

// Clamp the current page to be within the valid range (1 to $totalPages)
$currentPage = max(1, min($totalPages, $currentPage));

// Calculate the starting ai (offset) for the messages on the current page
$offset = ($currentPage - 1) * MESSAGES_PER_PAGE;

// Slice the array to get only the messages for the current page
$messagesForPage = array_slice($allMessages, $offset, MESSAGES_PER_PAGE);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        h1,
        h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        form div {
            display: flex;
            flex-direction: column;
        }

        form label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        form input[type="text"],
        form textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        form textarea {
            min-height: 100px;
            resize: vertical;
        }

        form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            align-self: flex-start;
            transition: background-color 0.2s;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .messages-list {
            margin-bottom: 30px;
        }

        .message {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #fafafa;
        }

        .message strong {
            display: block;
            font-size: 1.1em;
            color: #0056b3;
        }

        .message .date {
            display: block;
            font-size: 0.9em;
            color: #777;
            margin-bottom: 10px;
        }

        .message p {
            margin: 0;
            /* Preserve line breaks entered by the user */
            white-space: pre-wrap;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 1.1em;
        }

        .pagination a,
        .pagination span {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a {
            color: #007bff;
            background-color: #f0f0f0;
        }

        .pagination a:hover {
            background-color: #e0e0e0;
        }

        .pagination span.current {
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Guestbook</h1>

        <!-- The form submits to ai.php (itself) using the POST method -->
        <form action="ai.php" method="POST">
            <div>
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="message">Your Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>

        <h2>Messages</h2>

        <div class="messages-list">
            <?php if (empty($messagesForPage)): ?>

                <p>No messages yet. Be the first!</p>

            <?php else: ?>

                <?php foreach ($messagesForPage as $message): ?>
                    <div class="message">
                        <!-- 
                            We use htmlspecialchars() on ALL user output.
                            This is critical to prevent XSS (Cross-Site Scripting) attacks.
                            It converts characters like < and > to &lt; and &gt;.
                        -->
                        <strong><?= htmlspecialchars($message['name']) ?></strong>
                        <span class="date"><?= htmlspecialchars($message['date']) ?></span>

                        <!-- 
                            nl2br() converts newlines (\n) from the textarea
                            into HTML <br> tags so they are displayed.
                        -->
                        <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>

        <!-- Pagination Navigation -->
        <div class="pagination">

            <!-- "Previous" Link -->
            <?php if ($currentPage > 1): ?>
                <a href="ai.php?page=<?= $currentPage - 1 ?>">« Prev</a>
            <?php endif; ?>

            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $currentPage): ?>
                    <!-- Current page is a span, not a link -->
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <!-- Other pages are links -->
                    <a href="ai.php?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- "Next" Link -->
            <?php if ($currentPage < $totalPages): ?>
                <a href="ai.php?page=<?= $currentPage + 1 ?>">Next »</a>
            <?php endif; ?>

        </div>

    </div>

</body>

</html>