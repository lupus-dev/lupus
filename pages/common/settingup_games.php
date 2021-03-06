<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/print_game_status.php";
require_once __DIR__ . "/print_game.php";

$setupGames = $user->getSetupGame();
$rooms = array_merge($user->getPublicRoom(), $user->getPrivateRoom());

?>
<?php if (count($setupGames) > 0): ?>
    <div class="page-header">
        <h2>Stai creando queste partite</h2>
    </div>
    <?php foreach ($setupGames as $game): ?>
        <h3>
            <?php $room = Room::fromRoomName($game["room_name"]); ?>
            <?php $game = Game::fromRoomGameName($game["room_name"], $game["game_name"]); ?>
            <?php printGame($game, $room, $user); ?>
        </h3>
    <?php endforeach; ?>
<?php else: ?>
    <div class="page-header">
        <h3>Non stai progettando partite...</h3>
    </div>
    <?php $num_free = 0; ?>
    <?php foreach ($rooms as $room_name): ?>
        <?php $room = Room::fromRoomName($room_name); ?>
        <?php if ($room->isAllTerminated()): $num_free++; ?>
            <h4>
                <?= $room->room_descr ?> 
                <small><?= $room->room_name ?></small>
                <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>" class="btn btn-success">
                    Crea!
                </a>
            </h4>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($num_free == 0): ?>
        <p>Infatti non hai stanze libere...</p>
        <?php if ($user->canCreatePublicRoom()): ?>
            <p>Cosa aspetti a crearne una?</p>
            <a href="<?= $baseDir ?>/room" class="btn btn-success">
                Crea!
            </a>
        <?php else: ?>
            <p>E sfortunatamente non puoi crearne... gioca e sblocca altre stanze!</p>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
