<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

header("refresh: 5; url=../../index");
?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <title>Partita non trovata - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
    </head>
    <body>
        <div class="container" role="main">
            <?php include __DIR__ . "/../../common/navbar.php"; ?>
            <div class="page-header">
                <h1>La partita <small><?= $room_name ?>/<?= $game_name ?></small> non esiste</h1>
            </div>
            <h3>Tra 5 secondi verrari reindirizzato</h3>
        </div>
    </body>
</html>