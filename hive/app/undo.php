<?php
session_start(); // Start a session to manage game data.

$db = include_once './database.php'; // Include the database connection.

if ($_SESSION['last_move'] > 0) {
    // Prepare a database query to retrieve the last move's information based on the current game and last move ID.
    $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ? AND id = ?');
    $stmt->bind_param('ii', $_SESSION['game_id'], $_SESSION['last_move']);
    $stmt->execute(); // Execute the database query.

    $result = $stmt->get_result()->fetch_array(); // Get the result of the query and fetch it as an array.

    if ($result) {
        // Restore the previous game state from the retrieved state in the database.
        setState($result[6]);

        // Update the last move ID in the session to the previous move.
        $_SESSION['last_move'] = $result[4];
    }
}

header('Location: ../index.php'); // Redirect to the main game page.
