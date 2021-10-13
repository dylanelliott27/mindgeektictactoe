<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tic Tac Toe!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body{
            background-color: black;
        }
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .ui{
            font-family: 'poppins';
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            text-align: center;
            padding: 0 20px;
            color: white;
        }
        .current-player-text{
            font-family: 'poppins';
        }
        .welcome-message> h1{
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 38px;
        }
        .welcome-message > p{
            font-size: 22px;
        }
        .cell{
            cursor: pointer;
            transition: background-color .4s ease-in-out;
        }
        td{
            border: 1px solid red;
            height: 150px;
            width: 150px;
        }
        .game-table{
            margin-top: 40px;
        }
        .current-player-text{
            font-size: 38px;
            text-transform: uppercase;
        }
        .options{
            margin-top: 40px;
            font-family: 'poppins';
        }

        .one-player-btn{
            font-family: 'poppins';
            letter-spacing: 1px;
            padding: 10px 50px;
            margin-right: 10px;
            background-color: transparent;
            box-shadow: 0 0 0px #03c9d3, 0 0 2px #03c9d3, 0 0 14px #03c9d3, 0 0 0px #03c9d3;
            border: 1px solid white;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: box-shadow 0.2s ease-in-out;
        }
        .two-player-btn{
            font-family: 'poppins';
            letter-spacing: 1px;
            padding: 10px 50px;
            margin-left: 10px;
            color: white;
            font-weight: bold;
            background-color: transparent;
            box-shadow: 0 0 0px #e92020, 0 0 2px #e92020, 0 0 14px #e92020, 0 0 0px #e92020;
            border: 1px solid white;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: box-shadow 0.2s ease-in-out;
        }

        .one-player-btn:hover{
            box-shadow: 0 0 5px #03c9d3, 0 0 6px #03c9d3, 0 0 30px #03c9d3, 0 0 0px #03c9d3;
        }
        .two-player-btn:hover{
            box-shadow: 0 0 5px #e92020, 0 0 6px #e92020, 0 0 30px #e92020, 0 0 0px #e92020;
        }

    </style>
</head>
<body>
    <section class="ui">
        <section class="welcome-message">
            <h1>Welcome to my Mindgeek 2021 coding challenge!</h1>
            <p>By: Dylan Elliott</p>
        </section>
        <div class="options">
            <button class="one-player-btn" data-player-choice="1">ONE PLAYER</button>
            <button class="two-player-btn" data-player-choice="2">TWO PLAYER</button>
        </div>
        <h1 class="current-player-text"></h1>
        <table class="game-table">
    </section>

    </table>
    <script>
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

            HTTPHelper.get('start_game')
            .then(startGameData => {new TicTacToeGame(startGameData); console.log(startGameData)})
            .catch(error => console.error(error))
        }

        const HTTPHelper = Object.create(Object);

        HTTPHelper.get = async function(action) {
            const response = await fetch( './tictactoeapi.php' + "?action=" + action );

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

            HTTPHelper.post(postInfo).then(res => {
                if( 'error' in res ) { // Invalid move, user modified gameState array?
                    console.warn("Invalid move request" + res.error);
                    return;
                }

                //this.updateState(res);
                this.hydrateUI(res);
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
                this.headingRef.innerText = data.current_player.name + " wins!!!";
            }
            this.boardRef.removeEventListener('click', this.boardListenerCallback);
        }

        TicTacToeGame.prototype.updateCurrentPlayerHeading = function(data) {
            this.headingRef.innerText = data.current_player.name;
        }

    </script>
</body>
</html>