<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        /*.cell{
            border: 1px solid red;
            width: 33%;
            height: 150px;
            float: left;
        } */
        td{
            border: 1px solid red;
            height: 150px;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="options">
        <button data-player-choice="1">1player</button>
        <button data-player-choice="2">2player</button>
    </div>
    <h1 class="currentPlayerText"></h1>
    <!-- <div class="board">
        <div data-cell="0" class="cell"></div>
        <div data-cell="1" class="cell"></div>
        <div data-cell="2" class="cell"></div>
        <div data-cell="3" class="cell"></div>
        <div data-cell="4" class="cell"></div>
        <div data-cell="5" class="cell"></div>
        <div data-cell="6" class="cell"></div>
        <div data-cell="7" class="cell"></div>
        <div data-cell="8" class="cell"></div>
    </div> !-->
    <table>
        <?php $cell_counter = 0; ?>
        <?php for($i = 0; $i < 3; $i++):?>
            <tr>
                <?php for($k = 0; $k < 3; $k++):?>
                    <td class="cell" data-cell="<?php echo $cell_counter?>"></td>
                    <?php $cell_counter++ ?>
                <?php endfor;?>
            </tr>
        <?php endfor; ?>
    </table>
    <script>

        const optionContainer = document.querySelector('.options').addEventListener('click', initGame);

        function initGame(e) {
            if( ! e.target.hasAttribute('data-player-choice') ){
                return;
            }

            const playerCount = e.target.getAttribute('data-player-choice');

            new Board(playerCount);
        }

        function UIHelper() {
            this.currentPlayerHeading = document.querySelector('.currentPlayerText');
        }

        function HTTPHelper() {
            this.ajaxUrl = './tictactoeapi';
        }

        HTTPHelper.prototype.get = function(action) {

        }

        /*function Board(playerCount) {
            this.winningCombinations = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8],
                [6,4,2] ];
            this.cellNodeArray = Array.from(document.querySelectorAll('.cell'));
            this.boardRef = document.querySelector('table');
            this.currentPlayerHeading = document.querySelector('.currentPlayerText');
            this.players = [];
            this.gameState = ["", "", "", "", "", "", "", "", ""];
            this.currentPlayer = 0;
            this.winner = "";

            this.cleanBoard();
            this.initPlayers(playerCount);

            this.boardListenerRef = this.handleBoardClick.bind(this);
            this.boardRef.addEventListener('click', this.boardListenerRef);

        }

        Board.prototype.initPlayers = function(playerCount) {
            let markerOptions = ['x', 'o'];

            for(let i = 0; i < playerCount; i++) {
                this.players.push(new Player(i, markerOptions[i]));
            }

            this.currentPlayer = this.players[0];

            this.updateCurrentPlayerHeading();
        }

        Board.prototype.updateCurrentPlayerHeading = function(){
            this.currentPlayerHeading.innerText = this.currentPlayer.getName();
        }

        Board.prototype.markCell = function(cellNode) {
            console.log(typeof(cellNode));
        }

        Board.prototype.handleBoardClick = function(e) {
            if(! e.target.hasAttribute('data-cell') ) {
                return;
            }

            let cellIdx = e.target.getAttribute('data-cell');

            if(this.gameState[cellIdx] !== "") {
                // if cell already filled.
                return;
            }

            this.gameState[cellIdx] = this.currentPlayer.getMarker();

            if( this.currentPlayer.getMarker() == 'x' ) {
                e.target.style.backgroundColor = "red";
            }
            else{
                e.target.style.backgroundColor = "blue";
            }

            if( this.isGameFinished() ) {
                this.handleGameOver();
                return;
            }

            this.switchTurns();
        }

        Board.prototype.handleGameOver = function() {
            if( this.winner !== "tie" ) {
                this.currentPlayerHeading.innerText = this.winner + " wins!";
            }
            else {
                this.currentPlayerHeading.innerText = "Tie!"
            }
            // clean up / reset resources
            this.boardRef.removeEventListener('click', this.boardListenerRef);
            this.currentPlayer = 0;
            this.players = [];
        }

        Board.prototype.cleanBoard = function() {
            this.gameState.forEach(cell => cell = '');
            this.cellNodeArray.forEach(cell => cell.style.backgroundColor = "white");
        }

        Board.prototype.isGameFinished = function() {
            for(let i = 0; i < this.winningCombinations.length; i++) {
                let combinationFirstInt = this.winningCombinations[i][0];
                let combinationSecondInt = this.winningCombinations[i][1];
                let combinationThirdInt = this.winningCombinations[i][2];

                if(this.gameState[combinationFirstInt] === this.currentPlayer.getMarker() &&
                    this.gameState[combinationSecondInt] === this.currentPlayer.getMarker() &&
                    this.gameState[combinationThirdInt] === this.currentPlayer.getMarker()) {
                    this.winner = this.currentPlayer.getName();
                    return true;
                }
            }

            if( this.isTie() ) {
                this.winner = "tie";
                return true;
            }

            return false;

        }

        Board.prototype.isTie = function() {
            return this.gameState.every((cell) => cell != "");
        }

        Board.prototype.switchTurns = function() {
            if( this.currentPlayer.getID() == 0 ) {
                this.currentPlayer = this.players[1];
            }

            else {
                this.currentPlayer = this.players[0];
            }

            this.currentPlayerHeading.innerText = this.currentPlayer.getName();

        }



        function Player(id, marker) {
            this.id = id;
            this.name = "Player: " + id;
            this.marker = marker;
        }

        Player.prototype.getName = function() {
            return this.name;
        }

        Player.prototype.getID = function() {
            return this.id;
        }

        Player.prototype.getMarker = function() {
            return this.marker;
        }*/

    </script>
</body>
</html>