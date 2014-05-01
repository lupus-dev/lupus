<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login) 
    redirect("login");
$room_name = $matches[1];
?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <title><?= $room_name ?> - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
    </head>
    <body>
        <div class="container" role="main">
            <?php include __DIR__ . "/../../common/navbar.php"; ?>
            <?php include __DIR__ . "/content.php"; ?>
        </div>
    </body>
</html>