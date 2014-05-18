<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$gen_info = $game->gen_info;

$auto = ($gen_info["gen_mode"] == "auto") ? "checked" : "";
$manual = ($gen_info["gen_mode"] == "manual") ? "checked" : "";

$autoNumPlayers = $gen_info["auto"]["num_players"];
$autoRoles = $gen_info["auto"]["roles"];

$manualRoles = $gen_info["manual"]["roles"];

$level = Level::getLevel($user->level);
$aviableRoles = RoleDispenser::getAviableRoles($level->betaFeature);
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
    <div class="radio">
        <label>
            <input type="radio" name="gen_mode" id="gen-auto" value="auto" <?= $auto ?>>
            Generazione automatica dei ruoli
        </label>
    </div>
    <div class="radio">
        <label>
            <input type="radio" name="gen_mode" id="gen-manual" value="manual" <?= $manual ?>>
            Generazione manuale dei ruoli
        </label>
    </div>
    <div id="gen-auto-form">
        <div class="short-name">
            <label for="gen-auto-numplayers">Numero di giocatori</label>
            <select class="form-control" id="auto-num-players">
                <?php
                for ($i = RoleDispenser::MinPlayers; $i <= 18; $i++)
                    echo "<option value='$i' " . (($i == $autoNumPlayers) ? "selected" : "") . ">$i</option>";
                ?>
            </select>
        </div>
        <div class="short-name">
            <label>Ruoli utilizzabili</label>
            <table class="table table-condensed first-col-small">
                <thead>
                    <tr><th></th><th>Ruolo</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($aviableRoles as $role): ?>
                        <tr>
                            <td>
                                <input type="checkbox" data-role="<?= $role ?>" class="auto-role"
                                <?= in_array($role, $autoRoles) ? "checked" : "" ?>
                                       <?= ($role == "Lupo") ? "disabled" : "" ?>>
                            </td>
                            <td><?= $role ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>        
    </div>
    <div id="gen-manual-form">
        <div class="short-name">
            <table class="table table-condensed">
                <thead>
                    <tr><th>Ruolo</th><th>Frequenza</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($aviableRoles as $role): ?>
                        <tr>
                            <td><?= $role ?></td>
                            <td>
                                <input type="number" data-role="<?= $role ?>" class="form-control input-sm manual-role"
                                       value="<?= (isset($manualRoles[$role])) ? $manualRoles[$role] : "0" ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>   
    </div>

    <br>
    <button class="btn btn-info" id="save" onclick="saveGame(false)">Salva</button>
    <button class="btn btn-success pull-right" id="start" onclick="startGame()">Avvia</button>
</div>
<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>
<?php insertScript("setupGame.js"); ?>