<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$players = count($game->players["players"]) . "/" . $game->players["num_players"];

?>
<div class="page-header">
    <h1><?= $game->game_descr ?> <small><?= $game->game_name ?></small></h1>
</div>
<h4>Giocatori iscritti <span id="player-left"><?= $players ?></span></h4>
<button class="btn btn-success btn-lg" onclick="joinGame()">Entra!</button>
<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>
<?php insertScript("waitGameStatus.js") ?>
<?php insertScript("joinGame.js") ?>
<script>
    pollSuccess = function () {
        $("#player-left").text(regPlayers+"/"+numPlayers);
    };
</script>