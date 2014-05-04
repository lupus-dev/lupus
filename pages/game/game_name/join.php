<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$inGame = $game->inGame($user->id_user);

?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <title><?= $game_name ?> - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
    </head>
    <body>
        <div class="container" role="main">
            <?php include __DIR__ . "/../../common/navbar.php"; ?>
            <?php 
                if ($inGame)
                    include __DIR__ . "/join/in_game.php";
                else
                    include __DIR__ . "/join/not_in_game.php";
            ?>
        </div>
    </body>
</html>