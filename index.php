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
    <link rel="stylesheet" href="./css/style.css">
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
        <table class="game-table">      </table>
    </section>
    <script src="js/main.js"></script>
    <script>
        // connect to chat application on server
        let serverUrl = 'ws://127.0.0.1:5000';
        let socket = new WebSocket(serverUrl);

    </script>
</body>
</html>