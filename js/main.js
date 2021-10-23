const optionContainer = document.querySelector('.options');
const welcomeMessage = document.querySelector('.welcome-message');
optionContainer.addEventListener('click', initGame);

function initGame(e) {
    if( ! e.target.hasAttribute('data-player-choice') ){
        return;
    }

    const playerCount = e.target.getAttribute('data-player-choice');
    optionContainer.style.display = "none";
    welcomeMessage.style.display = "none";

    HTTPHelper.get({action: 'start_game', players: playerCount})
        .then(startGameData => {new TicTacToeGame(startGameData); console.log(startGameData)})
        .catch(error => console.error(error))
}

const HTTPHelper = Object.create(Object);

HTTPHelper.get = async function({action, players}) {
    const response = await fetch( './tictactoeapi.php' + "?action=" + action + "&players=" + players );

    if( !response.ok ) console.error("Error fetching");

    return response.json();
}

HTTPHelper.post = async function(bodyData) {

    const response = await fetch( './tictactoeapi.php', {
        method: 'POST',
        body: bodyData
    });

    if( !response.ok ) console.error("Error fetching");

    return response.json();
}

function TicTacToeGame({boardHtml, boardState}) {
    this.gameState = ["", "", "", "", "", "", "", "", ""];
    this.winner = "";
    this.renderBoard(boardHtml);

    this.boardRef = document.querySelector('table');
    this.boardCells = document.querySelectorAll('.cell');
    this.boardListenerCallback = this.handleBoardClick.bind(this);
    this.boardRef.addEventListener('click', this.boardListenerCallback);
    this.headingRef = document.querySelector('.current-player-text');
    this.updateCurrentPlayerHeading(boardState);
}

TicTacToeGame.prototype.renderBoard = function(boardHtml) {
    const table = document.querySelector('.game-table');

    table.innerHTML = boardHtml;
}

TicTacToeGame.prototype.handleBoardClick = function(e) {
    if(! e.target.hasAttribute('data-cell') ) {
        return;
    }

    let cellIdx = e.target.getAttribute('data-cell');

    if(this.gameState[cellIdx] !== "") {
        // if cell already filled. This gets validated serverside below as well
        return;
    }

    let invalidMove = false;

    const postInfo = new FormData();
    postInfo.append('action', 'handle_move');
    postInfo.append('requested_cell', cellIdx);

    let nextPlayerIsBot = false;

    HTTPHelper.post(postInfo).then(res => {
        if( 'error' in res ) { // Invalid move, user modified gameState array?
            console.warn("Invalid move request" + res.error);
            return;
        }

        if(res.current_player.bot) nextPlayerIsBot = true;
        this.hydrateUI(res);

        if(nextPlayerIsBot) { // move to separate function....
            console.log("going")
            const postInfo = new FormData();
            postInfo.append('action', 'handle_move');
            postInfo.append('requested_cell', 0); // random for now

            HTTPHelper.post(postInfo).then(res => {
                if( 'error' in res ) {
                    console.warn("Invalid move request" + res.error);
                    return;
                }

                this.hydrateUI(res);
            }).catch(error => console.error(error))
        }
    })
        .catch(error => console.error(error))

    if( invalidMove ) {
        return;
    }




}

TicTacToeGame.prototype.hydrateUI = function(data) {
    const colors = {'x' : 'red', 'o' : 'blue'};

    this.boardCells.forEach(cell => {
        const cellIdx = cell.getAttribute('data-cell');
        const markerInIdx = data.board_state[cellIdx];

        cell.style.backgroundColor = colors[markerInIdx];
        cell.innerText = markerInIdx;
    })

    if( data.win ) {
        this.handleWin(data);
    }
    else {
        this.updateCurrentPlayerHeading(data);
    }
}

TicTacToeGame.prototype.handleWin = function(data) {

    if( data.win == 'tie' ) {
        this.headingRef.innerText = "Tie!!!!!!";
    }
    else {
        this.headingRef.innerText = data.current_player.name + "(" + data.current_player.marker + ")" + " wins!!!";
    }
    this.boardRef.removeEventListener('click', this.boardListenerCallback);
}

TicTacToeGame.prototype.updateCurrentPlayerHeading = function(data) {
    this.headingRef.innerText = data.current_player.name;
}