<?php

session_start(); // Start a session to store game data.

// include_once './util.php';

// // Include the databaseController class
// require_once './database.php';
// $databaseController = databaseController::getInstance();
// $db = $databaseController->getDatabaseConnection();


// $from = $_POST['from']; // Get the 'from' position from the submitted form.
// $to = $_POST['to']; // Get the 'to' position from the submitted form.

// $player = $_SESSION['player']; // Get the current player from the session.
// $board = $_SESSION['board']; // Get the game board from the session.
// $hand = $_SESSION['hand'][$player]; // Get the player's hand from the session.
// unset($_SESSION['error']); // Clear any previous error messages from the session.


header('Location: ../index.php'); // Redirect to the main game page.
