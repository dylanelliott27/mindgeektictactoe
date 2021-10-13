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
        echo json_encode(['boardHtml' => $this->craft_table_html()]);
        die();
    }

    public function craft_table_html() {
        $cell_counter = 0;
        ob_start();
            ?>
                <?php for($i = 0; $i < 3; $i++):?>
                    <tr>
                        <?php for($k = 0; $k < 3; $k++):?>
                            <td class="cell" data-cell="<?php echo $cell_counter?>"></td>
                            <?php $cell_counter++ ?>
                        <?php endfor;?>
                    </tr>
                <?php endfor; ?>
            <?php
        return ob_get_clean();
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

$http_handler = new TicTacToeReqHandler();

$http_handler->intercept_request();




class Board {
    public function __construct() {
        $this->winningCombinations = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8],
            [6,4,2] ];
        $this->players = [];
        $this->gameState = ["", "", "", "", "", "", "", "", ""];
        $this->currentPlayer = 0;
        $this->winner = "";
    }
}