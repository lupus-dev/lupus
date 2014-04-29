<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/game_status.php";
$endedGames = $user->getEndedGame();

?>
<?php if (count($endedGames) > 0): ?>
    <div class="page-header">
        <h1>Hai giocato in queste partite</h1>
    </div>
    <?php foreach ($endedGames as $game): ?>
        <h3>
        <?php $room = Room::fromRoomName($game["room_name"]); ?>
            <?php $game = Game::fromRoomGameName($game["room_name"], $game["game_name"]); ?>
            <?= $game->game_descr ?>
            <?php printGameStatus($game, $room, false); ?>
            <small>
                <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>"><?= $room->room_name ?></a>
                /
                <a href="<?= $baseDir ?>/admin/<?= $room->room_name ?>/<?= $game->game_name ?>"><?= $game->game_name ?></a>
            </small>
        </h3>
    <?php endforeach; ?>
<?php endif; ?>
