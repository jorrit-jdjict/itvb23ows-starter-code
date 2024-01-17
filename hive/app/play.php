<?php
session_start(); // Start a session to manage game data.

include_once './util.php'; // Include utility functions.

$piece = $_POST['piece']; // Get the selected piece from the submitted form.
$to = $_POST['to']; // Get the destination position from the submitted form.

$player = $_SESSION['player']; // Get the current player from the session.
$board = $_SESSION['board']; // Get the game board from the session.
$hand = $_SESSION['hand'][$player]; // Get the player's hand from the session.

if (!$hand[$piece]) {
    $_SESSION['error'] = "Player does not have tile"; // Check if the player has the selected tile.
} elseif (isset($board[$to])) {
    $_SESSION['error'] = 'Board position is not empty'; // Check if the destination position is empty.
} elseif (count($board) && !hasNeighBour($to, $board)) {
    $_SESSION['error'] = "board position has no neighbour"; // Check if the destination position has no neighboring tiles.
} elseif (array_sum($hand) < 11 && !neighboursAreSameColor($player, $to, $board)) {
    $_SESSION['error'] = "Board position has opposing neighbour"; // Check if there are opposing color neighboring tiles.
} elseif (array_sum($hand) <= 8 && $hand['Q']) {
    $_SESSION['error'] = 'Must play queen bee'; // Check if the player must play the queen bee tile.
} else {
    // Place the selected piece on the board and update the player's hand.
    $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
    $_SESSION['hand'][$player][$piece]--;
    $_SESSION['player'] = 1 - $_SESSION['player']; // Switch to the next player's turn.
    $db = include_once './database.php'; // Include the database connection.
    $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) 
    values (?, "play", ?, ?, ?, ?)');
    $stmt->bind_param('issis', $_SESSION['game_id'], $piece, $to, $_SESSION['last_move'], getState());
    $stmt->execute();
    $_SESSION['last_move'] = $db->insert_id; // Store the last move ID in the session.
}

header('Location: ../index.php'); // Redirect to the main game page.
