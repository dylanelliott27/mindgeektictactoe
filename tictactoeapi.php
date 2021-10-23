<?php
session_start();

class Board {
    public function __construct() {
        $this->winning_combinations = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8],
            [6,4,2] ];
        $this->players = [];
        $this->board_state = ["", "", "", "", "", "", "", "", ""];
        $this->current_player = 0;
        $this->winner = "";
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

    public function setup_players($players) {
        if($players == 1){
            array_push($this->players, new Player(0, 'x'));
            array_push($this->players, new Player(1, 'o', true));
        }
        else {
            array_push($this->players, new Player(0, 'x'));
            array_push($this->players, new Player(1, 'o'));
        }

        $this->current_player = $this->players[0];
    }

    public function get_board_state_json() {
        return json_encode([
            'current_player' => ['id' => $this->current_player->get_id(),
                'marker' => $this->current_player->get_marker(),
                'name' => $this->current_player->get_name(),
                'bot' => $this->current_player->is_bot()],
            'board_state' => $this->board_state
        ]);
    }

    public function is_valid_move() {
        $requested_cell = $_REQUEST['requested_cell'];

        if( $this->get_board_state()[$requested_cell] != '' ){
            return false; // Cell is already filled
        }

        return true;
    }

    public function has_winner() {
        for($i = 0; $i < count($this->winning_combinations); $i++) {
            $combination_first_int = $this->winning_combinations[$i][0];
                $combination_second_int = $this->winning_combinations[$i][1];
                $combination_third_int = $this->winning_combinations[$i][2];

                if($this->board_state[$combination_first_int] === $this->current_player->get_marker() &&
                    $this->board_state[$combination_second_int] === $this->current_player->get_marker() &&
                    $this->board_state[$combination_third_int] === $this->current_player->get_marker()) {
                    $this->winner = $this->current_player->get_name();
                    return true;
                }
            }

            return false;
    }

    public function has_tie() {
        foreach( $this->board_state as $cell ) {
            if($cell == '') {
                // If a cell is empty, game is still ongoing
                // has_winner is checked before this. So only two possible options: game ongoing or all cells filled
                // with no winner
                return false;
            }
        }

        return true;
    }

    public function get_current_player() {
        return $this->current_player;
    }

    public function get_board_state() {
        return $this->board_state;
    }

    public function update_current_player() {
        // Player idx will always be same as array idx, so we can just go by that for now
        if( $this->current_player->get_id() == 0 ) {
            $this->current_player = $this->players[1];
        }
        else {
            $this->current_player = $this->players[0];
        }
    }

    public function update_board_state() {
        $this->board_state[$_REQUEST['requested_cell']] = $this->current_player->get_marker();
    }
}

class Player {
    private $id;
    private $marker;
    private $name;
    private $bot;

    public function __construct($id, $marker, $bot = false) {
        $this->id = $id;
        $this->marker = $marker;
        $this->name = "Player " . $id;
        $this->bot = $bot;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_marker() {
        return $this->marker;
    }

    public function get_name() {
        return $this->name;
    }
    public function is_bot() {
        return $this->bot;
    }
}

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
        if( isset($_SESSION['game_data']) ) {
            // Just act as if game is being reset for now
            unset($_SESSION['game_data']);
        }

        $board_instance = new Board();
        $board_instance->setup_players($_REQUEST['players']);
        echo json_encode(['boardHtml' => $board_instance->craft_table_html(), 'boardState' => json_decode($board_instance->get_board_state_json())]);
        $_SESSION['game_data'] = $board_instance;
        die();
    }

    public function check_winner() {
        echo "checked";
        die();
    }

    public function pick_random_cell($board_instance) {
        $empty_cells = [];
        foreach($board_instance->get_board_state() as $idx => $cell) {
            if($cell == ''){
                array_push($empty_cells, $idx);
            }
        }
        return $empty_cells[array_rand($empty_cells)];
    }

    public function handle_move() {

        if( !isset($_REQUEST['requested_cell']) ) {
            echo ['error' => 'No cell requested'];
            die();
        }

        $board_instance = $_SESSION['game_data'];

        if($board_instance->current_player->is_bot()){
            $_REQUEST['requested_cell'] = $this->pick_random_cell($board_instance);
        }

        if( !$board_instance->is_valid_move() ) {
            echo json_encode(['error' => 'Invalid move']);
            die();
        }

        $board_instance->update_board_state();

        if( $board_instance->has_winner() ) {
            $json_response = json_decode($board_instance->get_board_state_json());
            $json_response->win = true;
            echo json_encode($json_response);
            unset($_SESSION['game_data']);
        }

        else if( $board_instance->has_tie() ) {
            $json_response = json_decode($board_instance->get_board_state_json());
            $json_response->win = 'tie';
            echo json_encode($json_response);
            unset($_SESSION['game_data']);
        }
        else {
            $board_instance->update_current_player();
            echo $board_instance->get_board_state_json();
        }

        die();
    }

}

$http_handler = new TicTacToeReqHandler();

$http_handler->intercept_request();




