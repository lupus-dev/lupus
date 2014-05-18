<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$players = count($game->players["players"]) . "/" . $game->players["num_players"];
$level = Level::getLevel($user->level);
$joinable = (count($user->getActiveGame()) + 1) <= $level->aviableGame;
?>
<div class="page-header">
    <h1><?= $game->game_descr ?> <small><?= $game->game_name ?></small></h1>
</div>
<h4>Giocatori iscritti <span id="player-left"><?= $players ?></span></h4>
<button class="btn btn-success btn-lg <?= $joinable ? "" : "disabled" ?>" onclick="joinGame()">Entra!</button>
<?php if (!$joinable) : ?>
<h5>Hai finito il numero di partite in cui puoi giocare... aumenta di livello
per giocare ancora di più!</h5>
<?php endif; ?>
<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>
<?php insertScript("waitGameStatus.js") ?>
<?php insertScript("joinGame.js") ?>
<script>
    pollSuccess = function() {
        $("#player-left").text(regPlayers + "/" + numPlayers);
    };
</script>