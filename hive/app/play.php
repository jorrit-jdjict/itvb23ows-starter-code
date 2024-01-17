<?php
session_start(); // Start a session to manage game data.

// Include the GameBoard class
require_once 'util.php';
$gameBoard = new GameBoard();
$gameBoard->setPlayer(0);

// Include the GameDatabase class
require_once './database.php';
$gameDatabase = GameDatabase::getInstance();
$db = $gameDatabase->getDatabaseConnection();

$piece = $_POST['piece']; // Get the selected piece from the submitted form.
$to = $_POST['to']; // Get the destination position from the submitted form.

$player = $_SESSION['player']; // Get the current player from the session.
$board = $_SESSION['board']; // Get the game board from the session.
$hand = $_SESSION['hand'][$player]; // Get the player's hand from the session.

if (!$hand[$piece]) {
    $_SESSION['error'] = "Player does not have tile"; // Check if the player has the selected tile.
} elseif (isset($board[$to])) {
    $_SESSION['error'] = 'Board position is not empty'; // Check if the destination position is empty.
} elseif (count($board) && !$gameBoard->hasNeighBour($board, $to)) {
    $_SESSION['error'] = "board position has no neighbor"; // Check if the destination position has no neighboring tiles.
} elseif (array_sum($hand) < 11 && !$gameBoard->neighboursAreSameColor($player, $to, $board)) {
    $_SESSION['error'] = "Board position has opposing neighbor"; // Check if there are opposing color neighboring tiles.
} elseif ($piece != 'Q' && array_sum($hand) <= 8 && $hand['Q']) {
    $_SESSION['error'] = 'Must play queen bee'; // Check if the player must play the queen bee tile.
} else {
    $_SESSION['error'] = null;
    // Place the selected piece on the board and update the player's hand.
    $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
    $_SESSION['hand'][$player][$piece]--;
    $_SESSION['player'] = 1 - $_SESSION['player']; // Switch to the next player's turn.

    // Get the database connection
    $db = $gameDatabase->getDatabaseConnection();

    // Store the values in separate variables before binding.
    $game_id = $_SESSION['game_id'];
    $move_from = $piece;
    $move_to = $to;
    $last_move = $_SESSION['last_move'];
    $state = $gameDatabase->serializeGameState();

    $stmt = $db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state) VALUES (?, "play", ?, ?, ?, ?)');
    $stmt->bind_param('issis', $game_id, $move_from, $move_to, $last_move, $state);
    $stmt->execute();
    $_SESSION['last_move'] = $db->insert_id; // Store the last move ID in the session.
}

header('Location: ../index.php'); // Redirect to the main game page.
