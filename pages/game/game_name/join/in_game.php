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
    <h1>La partita <small><?= $room_name ?>/<?= $game_name ?></small> non è pronta!</h1>
</div>
<h3>Attenti che arrivino tutti i giocatori</h3>
<h3>La pagina si aggiornerà automaticamente...</h3>
<h4>Giocatori iscritti <span id="player-left"><?= $players ?></span></h4>
<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>
<?php insertScript("waitGameStatus.js") ?>
<script>
    pollSuccess = function () {
        $("#player-left").text(regPlayers+"/"+numPlayers);
    };
</script>