<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login) 
    redirect("login");
?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <title>Crea una stanza - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
        <?php insertScript("newRoom.js"); ?>
    </head>
    <body>
        <div class="container new-room" role="main">
            <?php include __DIR__ . "/../../common/navbar.php"; ?>
            <?php include __DIR__ . "/content.php"; ?>
        </div>
    </body>
</html>