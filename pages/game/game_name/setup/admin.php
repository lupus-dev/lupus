<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

for ($i = 8; $i <= 18; $i++) {
    $var = "s$i";
    if ($game->num_players == $i)
        $$var = "selected";
    else
        $$var = "";
}
?>
<div class="col-md-6">
    <div class="short-name">
        <label for="game-name">Nome della partita</label>
        <input class="form-control disabled" value="<?= $game_name ?>" disabled>
    </div>    
    <div class="has-success has-feedback">
        <label for="game-desc">Descrizione della partita</label>
        <input class="form-control" id="game-desc" onchange="checkGameDescr()" value="<?= $game->game_descr ?>">
        <span class="glyphicon glyphicon-ok form-control-feedback" id="game-desc-icon"></span>
    </div>
    <div class="short-name">
        <label for="game-num-player">Numero di giocatori</label>
        <select class="form-control" id="game-num-player">
            <option value="8" <?= $s8 ?>>8</option>
            <option value="9" <?= $s9 ?>>9</option>
            <option value="10" <?= $s10 ?>>10</option>
            <option value="11" <?= $s11 ?>>11</option>
            <option value="12" <?= $s12 ?>>12</option>
            <option value="13" <?= $s13 ?>>13</option>
            <option value="14" <?= $s14 ?>>14</option>
            <option value="15" <?= $s15 ?>>15</option>
            <option value="16" <?= $s16 ?>>16</option>
            <option value="17" <?= $s17 ?>>17</option>
            <option value="18" <?= $s18 ?>>18</option>
        </select>
    </div>    
    <br>
    <button class="btn btn-info" id="save" onclick="saveGame()">Salva</button>
    <button class="btn btn-success pull-right" id="start" onclick="startGame()">Avvia</button>
</div>
<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>
<?php insertScript("setupGame.js"); ?>