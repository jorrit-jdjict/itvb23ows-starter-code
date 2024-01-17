<?php

session_start(); // Data store for session

// Include the GameDatabase class
require_once './app/database.php';

// Include the GameBoard class
require_once './app/util.php';
$gameBoard = new GameBoard();
$gameBoard->setPlayer(0);

require_once './vendor/autoload.php';

// When board is not set in session, restart the game
if (!isset($_SESSION['board'])) {
    header('Location: app/restart.php');
    exit(0);
}

// Create an instance of the GameDatabase class
$gameDatabase = GameDatabase::getInstance();

// Get the database connection
$db = $gameDatabase->getDatabaseConnection();





// Sessions for board, player and hand, acting as a storage.
$board = $_SESSION['board'];
$player = $_SESSION['player'];
$hand = $_SESSION['hand'];

// All possible move destinations
$to = [];

// Generate possible move destinations based on predefined offsets.
foreach ($gameBoard->getOffsets() as $pq) {
    foreach (array_keys($board) as $pos) {
        $pq2 = explode(',', $pos);
        $to[] = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
    }
}

// Remove duplicate move destinations.
$to = array_unique($to);
if (!count($to)) {
    $to[] = '0,0'; // If no move destinations are available, set a default.
}

?>

<!DOCTYPE html>
<html lang="en-GB" xml:lang="en-GB">

<head>
    <title>Hive</title>
    <style>
        div.board {
            width: 60%;
            height: 100%;
            min-height: 500px;
            float: left;
            overflow: scroll;
            position: relative;
        }

        div.board div.tile {
            position: absolute;
        }

        div.tile {
            display: inline-block;
            width: 4em;
            height: 4em;
            border: 1px solid black;
            box-sizing: border-box;
            font-size: 50%;
            padding: 2px;
        }

        div.tile span {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 200%;
        }

        div.player0 {
            color: black;
            background: white;
        }

        div.player1 {
            color: white;
            background: black
        }

        div.stacked {
            border-width: 3px;
            border-color: red;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="board">
        <?php
        $min_p = 1000;
        $min_q = 1000;

        // What does P and Q mean?
        // Find the minimum p and q values on the game board. 
        foreach ($board as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p) {
                $min_p = $pq[0];
            }
            if ($pq[1] < $min_q) {
                $min_q = $pq[1];
            }
        }

        // Generate HTML code for displaying the game board.
        foreach (array_filter($board) as $pos => $tile) {
            $pq = explode(',', $pos);
            $pq[0];
            $pq[1];
            $h = count($tile);
            // Generate HTML for each tile on the board.
            echo '<div class="tile player';
            echo $tile[$h - 1][0];
            if ($h > 1) {
                echo ' stacked';
            }
            echo '" style="left: ';
            echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
            echo 'em; top: ';
            echo ($pq[1] - $min_q) * 4;
            echo "em;\">($pq[0],$pq[1])<span>";
            echo $tile[$h - 1][1];
            echo '</span></div>';
        }
        ?>
    </div>
    <div class="hand">
        White:
        <?php
        foreach ($hand[0] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player0"><span>' . $tile . "</span></div> ";
            }
        }
        ?>
    </div>
    <div class="hand">
        Black:
        <?php
        foreach ($hand[1] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player1"><span>' . $tile . "</span></div> ";
            }
        }
        ?>
    </div>
    <div class="turn">
        Turn: <?php if ($player == 0) {
                    echo "White";
                } else {
                    echo "Black";
                } ?>
    </div>
    <form method="post" action="app/play.php">
        <select name="piece">
            <?php
            foreach ($hand[$player] as $tile => $ct) {
                if ($ct > 0) {
                    echo '<option value="' . htmlspecialchars($tile) . '">' . htmlspecialchars($tile) . '</option>';
                }
            }
            ?>
        </select>
        <select name="to">
            <?php
            foreach ($to as $pos) {
                echo "<option value=\"$pos\">$pos</option>";
            }
            ?>
        </select>
        <input type="submit" value="Play">
    </form>
    <form method="post" action="app/move.php">
        <select name="from">
            <?php
            foreach (array_keys($board) as $pos) {
                echo "<option value=\"$pos\">$pos</option>";
            }
            ?>
        </select>
        <select name="to">
            <?php
            foreach ($to as $pos) {
                echo "<option value=\"$pos\">$pos</option>";
            }
            ?>
        </select>
        <input type="submit" value="Move">
    </form>
    <form method="post" action="app/pass.php">
        <input type="submit" value="Pass">
    </form>
    <form method="post" action="app/restart.php">
        <input type="submit" value="Restart">
    </form>
    <strong><?php if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
            };
            unset($_SESSION['error']); ?></strong>
    <ol>
        <?php
        // DB connection
        // Get the database connection using the GameDatabase instance
        $db = $gameDatabase->getDatabaseConnection();

        // Prepare a database query using the connection
        $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ?');

        // Bind the game_id parameter
        $stmt->bind_param('i', $_SESSION['game_id']);

        // Execute the database query
        $stmt->execute();
        $result = $stmt->get_result(); // Get the query result.

        // Display the list of moves from the database.
        while ($row = $result->fetch_array()) {
            echo '<li>' . $row[2] . ' ' . $row[3] . ' ' . $row[4] . '</li>';
        }
        ?>
    </ol>
    <form method="post" action="app/undo.php">
        <input type="submit" value="Undo">
    </form>
</body>

</html>