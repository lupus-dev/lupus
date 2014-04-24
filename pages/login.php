<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if ($login)
    redirect("index");

?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/common/head.php"; ?>
        <title>Login - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
        <?php insertScript("login.js"); ?>
    </head>
    <body>
        <div class="container">
            <?php include __DIR__ . "/common/navbar.php"; ?>
            <?php include __DIR__ . "/login/content.php"; ?>
        </div>
    </body>
</html>