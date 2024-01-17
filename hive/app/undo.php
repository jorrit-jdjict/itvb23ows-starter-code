<?php
session_start(); // Start a session to manage game data.

$db = include_once './database.php'; // Include the database connection.

if ($_SESSION['last_move'] > 0) {
    // Prepare a database query to retrieve the move information based on the last move ID.
    $stmt = $db->prepare('SELECT * FROM moves WHERE id = ' . $_SESSION['last_move']);

    $stmt->execute(); // Execute the database query.

    $result = $stmt->get_result()->fetch_array(); // Get the result of the query and fetch it as an array.

    $_SESSION['last_move'] = $result[5]; // Update the last move ID in the session.

    setState($result[6]); // Set the game state based on the retrieved state from the database.

}

header('Location: ../index.php'); // Redirect to the main game page.
