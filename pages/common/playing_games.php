<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/print_game_status.php";
require_once __DIR__ . "/print_game.php";
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
            <?php printGame($game, $room, $user); ?>
        </h3>
    <?php endforeach; ?>

<?php else: ?>
    <div class="page-header">
        <h1>Non stai giocando...</h1>
    </div>    
<?php endif; ?>
<p>Per un divertimento ancora maggiore, cerca una partita!
    <a href="<?= $baseDir ?>/join" class="btn btn-success">Cerca</a>
</p>