<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/game_status.php";
require_once __DIR__ . "/print_game.php";
$endedGames = $user->getEndedGame();

?>
<?php if (count($endedGames) > 0): ?>
    <div class="page-header">
        <h1>Hai giocato in queste partite</h1>
    </div>
    <?php foreach ($endedGames as $game): ?>
        <p>
        <?php $room = Room::fromRoomName($game["room_name"]); ?>
            <?php $game = Game::fromRoomGameName($game["room_name"], $game["game_name"]); ?>
            <?php printGame($game, $room, $user); ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>
