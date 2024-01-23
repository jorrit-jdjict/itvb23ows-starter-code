<?php
session_start(); // Start a session to manage game data.

// Include the databaseController class
require_once './database.php';
$databaseController = databaseController::getInstance();
$db = $databaseController->getDatabaseConnection();



// Get the database connection using the databaseController instance
$db = $databaseController->getDatabaseConnection();

// Insert a new game record into the database.
$db->prepare('INSERT INTO games VALUES ()')->execute();

// Store the game ID in the session.
$_SESSION['game_id'] = $db->insert_id;

// Redirect to the main game page.
header('Location: ../index.php');
