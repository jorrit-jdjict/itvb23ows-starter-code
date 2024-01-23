<?php

namespace controllers;

use components\boardComponent;
use components\playerComponent;
use controllers\databaseController;
use components\gameComponent;

class movesController
{
    private databaseController $db;
    private boardComponent $board;
    private playerComponent $playerComponent;
    private gameComponent $game;
    private $from;
    private $to;
    private $player;
    private $hand;
    private $piece;

    public function __construct(databaseController $db, boardComponent $board, playerComponent $playerComponent, gameComponent $game)
    {
        $this->db = $db;
        $this->board = $board;
        $this->playerComponent = $playerComponent;
        $this->game = $game;
    }

    public function move($from, $to)
    {
        unset($_SESSION['error']);
        $this->from = $from;
        $this->to = $to;
        $this->player = $_SESSION['player'];
        $this->hand = $_SESSION['hand'][$this->player];

        $board = $this->board->getBoard();

        if (!isset($board[$this->from])) {
            $_SESSION['error'] = 'Board position is empty';
        } elseif (
            isset($board[$this->from][count($board[$this->from]) - 1]) &&
            $board[$this->from][count($board[$this->from]) - 1][0] != $this->player
        ) {
            $_SESSION['error'] = "Tile is not owned by player";
        } elseif ($this->hand['Q']) {
            $_SESSION['error'] = "Queen bee is not played";
        } else {
            $tile = array_pop($board[$this->from]);

            if (!$this->board->hasNeighBour($this->to)) {
                $_SESSION['error'] = "Move would split hive";
            } else {
                $all = array_keys($board);
                $queue = [array_shift($all)];

                while ($queue) {
                    $next = explode(
                        ',',
                        array_shift($queue)
                    );
                    foreach ($this->board->getOffset() as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }

                if ($all) {
                    $_SESSION['error'] = "Move would split hive";
                } else {
                    if ($this->from == $this->to) {
                        $_SESSION['error'] = 'Tile must move';
                    } elseif (isset($board[$this->to]) && $tile[1] != "B") {
                        $_SESSION['error'] = 'Tile not empty';
                    } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!$this->board->slide($this->from, $this->to)) {
                            $_SESSION['error'] = 'Tile must slide';
                        } else {
                            $_SESSION['error'] = null;
                        }
                    }
                }
            }

            if (isset($_SESSION['error'])) {
                if (isset($board[$this->from])) {
                    array_push($board[$this->from], $tile);
                } else {
                    $board[$this->from] = [$tile];
                }
            } else {
                if (isset($board[$this->to])) {
                    array_push($board[$this->to], $tile);
                } else {
                    $board[$this->to] = [$tile];
                }

                $this->playerComponent->playerSwitch();

                unset($thisBoard[$this->from]);

                $args = [
                    'type' => 'move',
                    'gameID' => $_SESSION['game_id'],
                    'from' => $this->from,
                    'to' => $this->to,
                    'previousMove' => $_SESSION['last_move'],
                ];

                $lastMove = $this->db->makeMove($args);

                if ($lastMove !== false) {
                    $_SESSION['last_move'] = $lastMove;
                }
            }

            $_SESSION['board'] = $board;
        }
    }

    public function pass()
    {
        $args = [
            'type' => 'pass',
            'gameID' => $_SESSION['game_id'],
            'previousMove' => $_SESSION['last_move'],
        ];

        $lastMove = $this->db->makeMove($args);

        if ($lastMove !== false) {
            $_SESSION['last_move'] = $lastMove;
            $this->playerComponent->playerSwitch();
        }
    }

    public function play($piece, $to)
    {
        unset($_SESSION['error']);
        $this->player = $_SESSION['player'];
        $this->piece = $piece;
        $this->to = $to;
        $this->hand = $_SESSION['hand'][$this->player];

        $board = $this->board->getBoard();

        if (!$this->hand[$this->piece]) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($board[$this->to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($board) && !$this->board->hasNeighBour($this->to)) {
            $_SESSION['error'] = "board position has no neighbor";
        } elseif (array_sum($this->hand) < 11 && !$this->board->neighboursAreSameColor($this->player, $this->to)) {
            $_SESSION['error'] = "Board position has opposing neighbor";
        } elseif ($this->piece != 'Q' && array_sum($this->hand) <= 8 && $this->hand['Q']) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['error'] = null;
            $board[$this->to] = [[$this->player, $this->piece]];
            $this->hand[$this->piece]--;
            $_SESSION['player'] = 1 - $_SESSION['player'];

            $args = [
                'type' => 'play',
                'piece' => $this->piece,
                'gameID' => $_SESSION['game_id'],
                'to' => $this->to,
                'previousMove' => $_SESSION['last_move'],
            ];

            $lastMove = $this->db->makeMove($args);

            if ($lastMove !== false) {
                $_SESSION['last_move'] = $lastMove;
            }
        }

        $_SESSION['board'] = $board;
        $_SESSION['hand'][$this->player] = $this->hand;
    }

    public function restart()
    {
        unset($_SESSION['error']);
        $_SESSION['board'] = [];
        $_SESSION['hand'] = [
            0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
        ];
        $_SESSION['player'] = 0;

        $args = [
            'type' => 'restart',
        ];

        $this->db->makeMove($args);
    }

    public function undo()
    {
        if (count($this->game->getBoard()->getBoard()) != 0) {
            $args = [
                'type' => 'undo',
                'previousMove' => $_SESSION['last_move'],
            ];

            $result = $this->db->makeMove($args);

            $args = [
                'type' => 'delete',
                'moveID' => $_SESSION['last_move'],
            ];

            $this->db->makeMove($args);

            $_SESSION['last_move'] = $result[5];
            $this->db->unserializeGameState($result[6]);

            $this->game->getPlayer()->playerSwitch();
        }
    }
}
