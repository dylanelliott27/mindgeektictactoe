<?php

class TicTacToeReqHandler {
    private $valid_actions = ['start_game', 'check_winner', 'handle_move'];

    public function intercept_request() {

        if( !isset($_REQUEST['action']) ) {
            echo json_encode(['error' => 'Error with the format of your request.']);
            die();
        }

        if( !in_array($_REQUEST['action'], $this->valid_actions) ) {
            echo json_encode(['error' => 'Invalid action.']);
            die();
        }

        call_user_func([$this, $_REQUEST['action']]);
    }

    public function start_game() {
        echo "started";
        die();
    }

    public function check_winner() {
        echo "checked";
        die();
    }

    public function handle_move() {
        echo "handled";
        die();
    }
}

$handler = new TicTacToeReqHandler();

$handler->intercept_request();




class Board {
    public function __construct() {
        $this->winningCombinations = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8],
            [6,4,2] ];
        $this->cellNodeArray = Array.from(document.querySelectorAll('.cell'));
        $this->boardRef = document.querySelector('table');
        $this->currentPlayerHeading = document.querySelector('.currentPlayerText');
        $this->players = [];
        $this->gameState = ["", "", "", "", "", "", "", "", ""];
        $this->currentPlayer = 0;
        $this->winner = "";
    }
}