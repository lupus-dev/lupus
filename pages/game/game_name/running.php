<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$day = GameTime::fromDay($game->day) == GameTime::Day;
$body_class = $day ? "day" : "night";

$inGame = $game->inGame($user->id_user);

?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <title>Partita in corso - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
        <?php insertScript("game.js"); ?>
    </head>
    <body class="<?= $body_class ?>">
        <div class="container" role="main">
            <?php include __DIR__ . "/running/navbar.php"; ?>
            <?php if($inGame): ?>
                <?php include __DIR__ . "/running/in_game.php"; ?>
            <?php else: ?>
                <?php include __DIR__ . "/running/not_in_game.php"; ?>
            <?php endif; ?>
        </div>
    </body>
</html>