<?php

session_start(); // Start a session to store game data.

include_once './util.php';

$from = $_POST['from']; // Get the 'from' position from the submitted form.
$to = $_POST['to']; // Get the 'to' position from the submitted form.

$player = $_SESSION['player']; // Get the current player from the session.
$board = $_SESSION['board']; // Get the game board from the session.
$hand = $_SESSION['hand'][$player]; // Get the player's hand from the session.
unset($_SESSION['error']); // Clear any previous error messages from the session.

if (!isset($board[$from])) {
    $_SESSION['error'] = 'Board position is empty';
} elseif ($board[$from][count($board[$from]) - 1][0] != $player) {
    $_SESSION['error'] = "Tile is not owned by player";
} elseif ($hand['Q']) {
    $_SESSION['error'] = "Queen bee is not played";
} else {
    $tile = array_pop($board[$from]); // Remove a tile from the 'from' position.

    // Check if the move would split the hive.
    if (!hasNeighBour($to, $board)) {
        $_SESSION['error'] = "Move would split hive";
    } else {
        $all = array_keys($board);
        $queue = [array_shift($all)];

        // Check if the move would split the hive by iterating through neighboring positions.
        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach ($GLOBALS['OFFSETS'] as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $all)) {
                    $queue[] = "$p,$q";
                    $all = array_diff($all, ["$p,$q"]);
                }
            }
        }

        // If there are remaining positions in 'all', the move would split the hive.
        if ($all) {
            $_SESSION['error'] = "Move would split hive";
        } else {
            if ($from == $to) {
                $_SESSION['error'] = 'Tile must move';
            } elseif (isset($board[$to]) && $tile[1] != "B") {
                $_SESSION['error'] = 'Tile not empty';
            } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                if (!slide($board, $from, $to)) {
                    $_SESSION['error'] = 'Tile must slide';
                }
            }
        }
    }

    // Handle error cases by restoring the tile to its original position.
    if (isset($_SESSION['error'])) {
        if (isset($board[$from])) {
            array_push($board[$from], $tile);
        } else {
            $board[$from] = [$tile];
        }
    } else { // If no errors, proceed with the move.
        if (isset($board[$to])) {
            array_push($board[$to], $tile);
        } else {
            $board[$to] = [$tile];
        }
        $_SESSION['player'] = 1 - $_SESSION['player']; // Switch to the next player's turn.
        $db = include_once './database.php'; // Include the database connection.
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) 
        values (?, "move", ?, ?, ?, ?)');

        // Bind parameters for the database query and execute the query.
        $stmt->bind_param('issis', $_SESSION['game_id'], $from, $to, $_SESSION['last_move'], getState());
        $stmt->execute();
        $_SESSION['last_move'] = $db->insert_id; // Store the last move ID in the session.
    }
    $_SESSION['board'] = $board; // Update the game board in the session.
}

header('Location: ../index.php'); // Redirect to the main game page.
