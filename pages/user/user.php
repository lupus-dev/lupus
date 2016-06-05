<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login)
    redirect("login");

$u = $user;
if (count($matches) == 2)
    $u = User::fromUsername($matches[1]);

if (!$u) redirect("index");

?>
<!doctype html>
<html>
    <head>
        <?php include __DIR__ . "/../common/head.php"; ?>
        <title><?= $u->username ?> - Lupus in Tabula</title>
        <?php insertScript("default.js"); ?>
    </head>
<body>
    <div class="container" role="main">
        <?php include __DIR__ . "/../common/navbar.php"; ?>
        <?php include __DIR__ . "/content.php"; ?>
    </div>
</body>
</html>
