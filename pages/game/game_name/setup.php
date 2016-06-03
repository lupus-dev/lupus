<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


$admin = ($room->id_admin == $user->id_user);

?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../../common/head.php"; ?>
        <link rel="stylesheet" href="<?= $baseDir ?>/css/jquery-ui.min.css">
        <link rel="stylesheet" href="<?= $baseDir ?>/css/jquery-ui.theme.min.css">
        <title>Setup in progress - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
        <?php insertScript("jquery/jquery-ui.min.js"); ?>
    </head>
    <body>
        <div class="container" role="main">
            <?php include __DIR__ . "/../../common/navbar.php"; ?>
            <?php 
                if ($admin)
                    include __DIR__ . "/setup/admin.php";
                else
                    include __DIR__ . "/setup/not_admin.php";
            ?>
        </div>
    </body>
</html>
