<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../common/print_game.php";

$games = Game::getOpenGames($user);
$rooms = array();
?>
<div class="page-header">
    <h1>Cerca una partita</h1>
</div>
<?php if (!$games): ?>
    <h3>Nessuna partita disponibile!</h3>
<?php else: ?>
    <?php
    foreach ($games as $game) {
        if (!isset($rooms[$game->id_room]))
            $rooms[$game->id_room] = Room::fromIdRoom ($game->id_room);
        $numPlayers = $game->num_players;
        $conPlayers = $game->getNumPlayers();
        
        echo "<h3>";
        printGame($game, $rooms[$game->id_room], $user, false);
        echo "<small>$conPlayers/$numPlayers</small>";
        echo "</h3>";
    }
    ?>
<?php endif; ?>
