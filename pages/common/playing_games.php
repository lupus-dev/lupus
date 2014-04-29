<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/game_status.php";
$activeGames = $user->getActiveGame();

?>
<?php if (count($activeGames) > 0): ?>
    <div class="page-header">
        <h1>Stai giocando in queste partite</h1>
    </div>
    <?php foreach ($activeGames as $game): ?>
        <h3>
            <?php $room = Room::fromRoomName($game["room_name"]); ?>
            <?php $game = Game::fromRoomGameName($game["room_name"], $game["game_name"]); ?>
            <?= $game->game_descr ?>
            <?php $hasToVote = $game->hasToVote($user); ?>
            <?php printGameStatus($game, $room, $hasToVote); ?>
            <small>
                <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>"><?= $room->room_name ?></a>
                /
                <a href="<?= $baseDir ?>/game/<?= $room->room_name ?>/<?= $game->game_name ?>"><?= $game->game_name ?></a>
            </small>
        </h3>
    <?php endforeach; ?>
<?php else: ?>
    <div class="page-header">
        <h1>Non stai giocando...</h1>
    </div>
    <p>
        Non sprecare nemmeno un minuto! Entra in una partita!
    </p>
    <a href="<?= $baseDir ?>/join" class="btn btn-success btn-lg">Cerca</a>
<?php endif; ?>