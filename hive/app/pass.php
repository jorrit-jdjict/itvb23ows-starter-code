<?php

session_start(); // Start a session to store game data.

$db = include_once './database.php'; // Include the database connection.

// Prepare a database query to record a "pass" move in the moves table.
$stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) 
values (?, "pass", null, null, ?, ?)');

// Bind parameters for the database query.
$stmt->bind_param('iis', $_SESSION['game_id'], $_SESSION['last_move'], getState());

// Execute the database query to record the "pass" move.
$stmt->execute();

$_SESSION['last_move'] = $db->insert_id; // Store the last move ID in the session.
$_SESSION['player'] = 1 - $_SESSION['player']; // Switch to the next player's turn.

header('Location: ../index.php'); // Redirect to the main game page.
