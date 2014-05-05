<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */
?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <title><?= $game_name ?> - Terminata - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
    </head>
    <body>
        <div class="container" role="main">
            <?php include __DIR__ . "/../../common/navbar.php"; ?>
            <?php include __DIR__ . "/end/content.php"; ?>
        </div>
    </body>
</html>