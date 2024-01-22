<?php
session_start(); // Start a session to manage game data.

// Include the GameDatabase class
require_once './database.php';
$gameDatabase = GameDatabase::getInstance();
$db = $gameDatabase->getDatabaseConnection();

// Initialize an empty game board in the session.
$_SESSION['board'] = [];

// Initialize player hands
$_SESSION['hand'] = [
    0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], // Initialize player 0's hand with tile counts.
    1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]  // Initialize player 1's hand with tile counts.
];

// Set the current player to player 0.
$_SESSION['player'] = 0;

// Get the database connection using the GameDatabase instance
$db = $gameDatabase->getDatabaseConnection();

// Insert a new game record into the database.
$db->prepare('INSERT INTO games VALUES ()')->execute();

// Store the game ID in the session.
$_SESSION['game_id'] = $db->insert_id;

// Redirect to the main game page.
header('Location: ../index.php');
