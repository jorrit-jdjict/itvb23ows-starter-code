<?php

session_start(); // Start a session to store game data.

// Include the GameDatabase class
require_once './database.php';
$gameDatabase = GameDatabase::getInstance();
$db = $gameDatabase->getDatabaseConnection();

// Prepare a database query to record a "pass" move in the moves table.
$stmt = $db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) 
VALUES (?, "pass", null, null, ?, ?)');

// Create variables for the parameters
$gameId = $_SESSION['game_id'];
$lastMove = $_SESSION['last_move'];
$serializedGameState = $gameDatabase->serializeGameState();

// Bind parameters for the database query.
$stmt->bind_param('iis', $gameId, $lastMove, $serializedGameState);

// Execute the database query to record the "pass" move.
$stmt->execute();

$_SESSION['last_move'] = $db->insert_id; // Store the last move ID in the session.
$_SESSION['player'] = 1 - $_SESSION['player']; // Switch to the next player's turn.

header('Location: ../index.php'); // Redirect to the main game page.
