<?php
session_start(); // Start a session to manage game data.

$_SESSION['board'] = []; // Initialize an empty game board in the session.
$_SESSION['hand'] = [
    0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], // Initialize player 0's hand with tile counts.
    1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]  // Initialize player 1's hand with tile counts.
];
$_SESSION['player'] = 0; // Set the current player to player 0.

$db = include_once './database.php'; // Include the database connection.
$db->prepare('INSERT INTO games VALUES ()')->execute(); // Insert a new game record into the database.
$_SESSION['game_id'] = $db->insert_id; // Store the game ID in the session.

header('Location: ../index.php'); // Redirect to the main game page.
