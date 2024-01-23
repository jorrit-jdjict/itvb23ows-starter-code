<?php

// namespace controllers;

// use components\boardComponent;
// use components\playerComponent;
// use controllers\databaseController;


// class moveController
// {
//     private databaseController $db;
//     private $from;
//     private $to;
//     private $player;
//     private boardComponent $board;
//     private playerComponent $playerComponent;
//     private $hand;


//     public function __construct($from, $to, $board, $db)
//     {
//         $this->db = $db;
//         $this->from = $from;
//         $this->to = $to;
//         $this->player = $_SESSION['player'];
//         $this->board = $board;
//         $this->hand = $_SESSION['hand'][$this->player];
//     }

//     public function move()
//     {
//         unset($_SESSION['error']); // Clear any previous error messages from the session.

//         $board = $this->board->getBoard();

//         if (!isset($board[$this->from])) {
//             $_SESSION['error'] = 'Board position is empty';
//         } elseif (
//             isset($board[$this->from][count($board[$this->from]) - 1]) &&
//             $board[$this->from][count($board[$this->from]) - 1][0] != $this->player
//         ) {
//             $_SESSION['error'] = "Tile is not owned by player";
//         } elseif ($this->hand['Q']) {
//             $_SESSION['error'] = "Queen bee is not played";
//         } else {
//             $tile = array_pop($board[$this->from]); // Remove a tile from the 'from' position.

//             // Check if the move would split the hive.
//             if (!$this->board->hasNeighBour($this->to)) {
//                 $_SESSION['error'] = "Move would split hive";
//             } else {
//                 $all = array_keys($board);
//                 $queue = [array_shift($all)];

//                 // Check if the move would split the hive by iterating through neighboring positions.
//                 while ($queue) {
//                     $next = explode(
//                         ',',
//                         array_shift($queue)
//                     );
//                     foreach ($this->board->getOffset() as $pq) {
//                         list($p, $q) = $pq;
//                         $p += $next[0];
//                         $q += $next[1];
//                         if (in_array("$p,$q", $all)) {
//                             $queue[] = "$p,$q";
//                             $all = array_diff($all, ["$p,$q"]);
//                         }
//                     }
//                 }

//                 // If there are remaining positions in 'all', the move would split the hive.
//                 if ($all) {
//                     $_SESSION['error'] = "Move would split hive";
//                 } else {
//                     if ($this->from == $this->to) {
//                         $_SESSION['error'] = 'Tile must move';
//                     } elseif (isset($board[$this->to]) && $tile[1] != "B") {
//                         $_SESSION['error'] = 'Tile not empty';
//                     } elseif ($tile[1] == "Q" || $tile[1] == "B") {
//                         if (!$this->board->slide($this->from, $this->to)) {
//                             $_SESSION['error'] = 'Tile must slide';
//                         } else {
//                             $_SESSION['error'] = null;
//                         }
//                     }
//                 }
//             }

//             // Handle error cases by restoring the tile to its original position.
//             if (isset($_SESSION['error'])) {
//                 if (isset($board[$this->from])) {
//                     array_push($board[$this->from], $tile);
//                 } else {
//                     $board[$this->from] = [$tile];
//                 }
//             } else { // If no errors, proceed with the move.
//                 if (isset($board[$this->to])) {
//                     array_push($board[$this->to], $tile);
//                 } else {
//                     $board[$this->to] = [$tile];
//                 }

//                 // TODO
//                 $this->playerComponent->playerSwitch();

//                 unset($thisBoard[$this->from]);

//                 $args = [
//                     'type' => 'move',
//                     'gameID' => $_SESSION['game_id'],
//                     'from' => $this->from,
//                     'to' => $this->to,
//                     'previousMove' => $_SESSION['last_move'],
//                 ];

//                 $lastMove = $this->db->makeMove($args);

//                 if ($lastMove !== false) {
//                     $_SESSION['last_move'] = $lastMove;
//                 } else {
//                 }
//             }

//             $_SESSION['board'] = $board; // Update the game board in the session.
//         }
//     } 
// }
