<?php
session_start(); // Start a session to manage game data.

$db = include_once './database.php'; // Include the database connection.

$lastMoveID = $_SESSION['last_move'];
// echo $lastMoveID;
if ($lastMoveID != 0) {
    // Prepare a database query to retrieve the last move's information based on the current game and last move ID.
    $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ? ');
    $stmt->bind_param('i', $_SESSION['game_id']);
    $stmt->execute(); // Execute the database query.

    $result = $stmt->get_result()->fetch_array(); // Get the result of the query and fetch it as an array.

    // var_dump($result);
    if ($result) {
        $stmt = $db->prepare('DELETE FROM moves WHERE game_id = ? AND id = ?');
        $stmt->bind_param('ii', $_SESSION['game_id'], $lastMoveID);
        $stmt->execute(); // Execute the database query.


        $stmt = $db->prepare('SELECT * FROM moves WHERE id = ? ');
        $stmt->bind_param('i', $lastMoveID);
        $stmt->execute(); // Execute the database query.

        $previousMoveId = $stmt->get_result()->fetch_array();
        var_dump($previousMoveId);

        $_SESSION['last_move'] = $previousMoveId;

        // Restore the previous game state from the retrieved state in the database.
        setState($result[6]);

        // set last move to session
    }
} else {
}

header('Location: ../index.php'); // Redirect to the main game page.
