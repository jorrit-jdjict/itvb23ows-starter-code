<?php

session_start(); // Data store for session

// Include the databaseController class
use controllers\databaseController;
use controllers\moveController;
use controllers\passController;
use controllers\movesController;
use controllers\restartController;
use controllers\undoController;


use components\boardComponent;
use components\gameComponent;
use components\playerComponent;
use controllers\rulesController;

use function PHPUnit\Framework\throwException;

require_once __DIR__ . '/vendor/autoload.php';

// Create an instance of the databaseController and restartController class
$database = databaseController::getInstance();
$restartController = new restartController($database);

// if restart
if (array_key_exists('restart', $_POST) || $_SESSION['board'] == null) {
    $restartController->restart();
}

// Sessions for board, player and hand, acting as a storage.
if (isset($_SESSION['board'])) {
    $board = new boardComponent($_SESSION['board']);
}

$hand = $_SESSION['hand'];
$player = new playerComponent($_SESSION['player'], $hand);
$game = new gameComponent($board, $player, $_SESSION['game_id']);
$rules = new rulesController($board);

$actions = [
    'move' => 'moveController',
    'pass' => 'passController',
    'play' => 'playController',
    'undo' => 'undoController',
];

foreach ($actions as $action => $controllerClass) {
    if (isset($_POST[$action])) {
        if ($action == 'move') {
            $controller = new movesController($database, $game->getBoard(), $player, $game, $rules);
            $controller->move($_POST['from'], $_POST['to']);
        } elseif ($action == 'pass') {
            $controller = new movesController($database, $game->getBoard(), $player, $game, $rules);
            $controller->pass();
        } elseif ($action == 'play') {
            $controller = new movesController($database, $game->getBoard(), $player, $game, $rules);
            $controller->play($_POST['piece'], $_POST['to']);
        } elseif ($action == 'undo') {
            $controller = new movesController($database, $game->getBoard(), $player, $game, $rules);
            $controller->undo();
        }

        header('location: index.php');
        exit;
    }
}

function debug_to_console()
{
    echo "<script>console.log('Debug Objects: " . json_encode($_SESSION) . "' );</script>";
}

debug_to_console();

?>
<!DOCTYPE html>
<html lang="en-GB" xml:lang="en-GB">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hive</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#000000">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" type="text/css" href="./app/assets/css/style.css">
    <script src="./app/assets/js/scripts.js"></script>
</head>

<body>
    <div class="board">
        <?php
        $min_p = 1000;
        $min_q = 1000;

        // What does P and Q mean?
        // Find the minimum p and q values on the game board. 
        foreach ($game->getBoard()->getBoard() as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p) {
                $min_p = $pq[0];
            }
            if ($pq[1] < $min_q) {
                $min_q = $pq[1];
            }
        }

        // // Generate HTML code for displaying the game board.
        foreach (array_filter($game->getBoard()->getBoard()) as $pos => $tile) {
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
            echo (($pq[0] - $min_p) * 60 +  ($pq[1] - $min_q) * 50);
            echo 'px; top: ';
            echo (($pq[1] - $min_q)) * 30;
            echo "px;\">($pq[0],$pq[1])<span>";
            echo $tile[$h - 1][1];
            echo '</span></div>';
        }
        ?>
    </div>

    <!-- HANDS -->
    <div class="hand">
        White:
        <?php
        foreach ($game->getPlayer()->getHand()[0] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player0"><span>' . $tile . "</span></div> ";
            }
        }
        ?>
    </div>
    <div class="hand">
        Black:
        <?php
        foreach ($game->getPlayer()->getHand()[1] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player1"><span>' . $tile . "</span></div> ";
            }
        }
        ?>
    </div>

    <!-- TURN -->
    <div class="turn">
        Turn: <?php if ($game->getPlayer()->getPlayerID() == 0) {
                    echo "White";
                } else {
                    echo "Black";
                } ?>
    </div>

    <!-- Place -->
    <form method="post">
        <select name="piece">
            <?php
            foreach ($game->getPlayer()->getStonesInHand() as $tile => $ct) {
                echo '<option value="' . htmlspecialchars($ct) . '">' . htmlspecialchars($ct) . '</option>';
            }
            ?>
        </select>
        <select name="to">
            <?php
            foreach ($game->getBoard()->getAllPossiblePositions($game->getBoard(), $game->getPlayer()->getStonesInHand(), $game->getPlayer()) as $pos) {
                echo "<option value=\"$pos\">$pos</option>";
            }
            ?>
        </select>
        <input type="submit" name="play" value="Play">
    </form>

    <!-- Move -->
    <form method="post">
        <select name="from">
            <?php
            foreach ($game->getPlayer()->getStonesOfPlayerOnBoard($game->getBoard()->getBoard()) as $tile => $pos) {
                echo "<option value=\"$tile\">$tile</option>";
            }
            ?>
        </select>
        <select name="to">
            <?php
            foreach ($game->getBoard()->getAllPossiblePositions($game->getBoard(), $game->getPlayer()->getStonesInHand(), $game->getPlayer()) as $pos) {
                echo "<option value=\"$pos\">$pos</option>";
            }
            ?>
        </select>
        <input type="submit" name="move" value="Move">
    </form>

    <!-- Pass -->
    <form method="post">
        <input type="submit" name="pass" value="Pass">
    </form>

    <!-- Restart -->
    <form method="post">
        <input type="submit" name="restart" value="Restart">
    </form>

    <ol>
        <?php
        $result = $database->getPreviousMoves($_SESSION['game_id']);

        // Display the list of moves from the database.
        while ($row = $result->fetch_array()) {
            echo '<li>' . $row[2] . ' ' . $row[3] . ' ' . $row[4] . '</li>';
        }
        ?>
    </ol>
    <form method="post">
        <input type="submit" name="undo" value="Undo">
    </form>

    <!-- Errors -->
    <strong>
        <?php if (isset($_SESSION['error'])) { ?>
            <div id="error-container"><?php echo ($_SESSION['error']); ?></div>
        <?php  } ?>

        <pre>
            <?php var_dump($board); ?>
        </pre>
    </strong>
</body>

</html>