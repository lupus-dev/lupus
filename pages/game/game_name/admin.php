<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login) redirect("login");

$room_name = $matches[1];
$game_name = $matches[2];

$room = Room::fromRoomName($room_name);
$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    require __DIR__ . "/not_found.php";

$admin = ($room->id_admin == $user->id_user);

if (!$admin) redirect("game/$room_name/$game_name");

?>
<!doctype html>
<html>
<head>
    <?php include __DIR__ . "/../../common/head.php"; ?>
    <title><?= $game_name ?> - Terminata - Lupus in Tabula</title>
    <?php insertScript("default.js"); ?>
    <?php insertScript("gameAdmin.js"); ?>
</head>
<body>
<div class="container" role="main">
    <?php
        if ($game->status == GameStatus::Running)
            include __DIR__ . "/running/navbar.php";
        else
            include __DIR__ . "/../../common/navbar.php";
    ?>
    <?php include __DIR__ . "/admin/content.php"; ?>
    <script>
        var room_name = "<?= $room_name ?>";
        var game_name = "<?= $game_name ?>";
    </script>
</div>
</body>
</html>
