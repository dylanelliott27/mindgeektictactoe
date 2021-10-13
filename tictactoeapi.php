<?php
session_start();

class Board {
    public function __construct() {
        $this->winning_combinations = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8],
            [6,4,2] ];
        $this->players = [];
        $this->game_state = ["", "", "", "", "", "", "", "", ""];
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

    public function setup_players() {
        array_push($this->players, new Player(0, 'x'));
        array_push($this->players, new Player(1, 'o'));

        $this->current_player = $this->players[0];
    }

    public function get_board_state_json() {
        return json_encode([
            'current_player' => ['id' => $this->current_player->get_id(),
                'marker' => $this->current_player->get_marker(),
                'name' => $this->current_player->get_name()],
            'game_state' => $this->game_state
        ]);
    }
}

class Player {
    private $id;
    private $marker;
    private $name;

    public function __construct($id, $marker) {
        $this->id = $id;
        $this->marker = $marker;
        $this->name = "Player " . $id;
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
        $board_instance = new Board();
        $board_instance->setup_players();
        echo json_encode(['boardHtml' => $board_instance->craft_table_html(), 'boardState' => $board_instance->get_board_state_json()]);
        $_SESSION['game_data'] = $board_instance;
        die();
    }

    public function check_winner() {
        echo "checked";
        die();
    }

    public function handle_move() {
        $board_instance = $_SESSION['game_data'];
        echo $board_instance->get_board_state_json();
        die();
    }
}

$http_handler = new TicTacToeReqHandler();

$http_handler->intercept_request();




