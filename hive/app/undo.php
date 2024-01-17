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

        $_SESSION['last_move'] = $previousMoveId;

        // Restore the previous game state from the retrieved state in the database.
        setState($result[6]);
    }
} else {
    $_SESSION['board'] = []; // Initialize an empty game board in the session.
    $_SESSION['hand'] = [
        0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], // Initialize player 0's hand with tile counts.
        1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]  // Initialize player 1's hand with tile counts.
    ];
    $_SESSION['player'] = 0; // Set the current player to player 0.

    $db->prepare('INSERT INTO games VALUES ()')->execute(); // Insert a new game record into the database.
    $_SESSION['game_id'] = $db->insert_id; // Store the game ID in the session.

}

header('Location: ../index.php'); // Redirect to the main game page.
