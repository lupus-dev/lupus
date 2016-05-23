<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$players = array_diff($game->getPlayers(), [$user->username]);

?>
<div class="page-header">
    <h1>Admin - <?= $game->game_descr ?> <small><?= $game->game_name ?></small></h1>
</div>
<p>Se l'amministratore di questa stanza, puoi terminare la partita o espellere giocatori</p>

<div class="col-md-6">
    <div class="panel panel-danger">
        <div class="panel-heading">Espelli un giocatore</div>
        <div class="panel-body">
            <p><strong>Attenzione!</strong> Un giocatore espulso rimarrà nella partita ma viene ucciso da una
            divinità e l'evento viene segnalato a tutti i giocatori!</p>
            <div class="form-group">
                <label for="kick-player">Espelli un giocatore</label>
                <select id="kick-player" class="form-control">
                    <?php foreach ($players as $player) { ?>
                        <option><?= $player ?></option>
                    <?php } ?>
                </select>
            </div>
            <button class="btn btn-danger" id="do-kick-player">Espelli</button>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="panel panel-danger">
        <div class="panel-heading">Termina la partita</div>
        <div class="panel-body">
            <p><strong>Attenzione!</strong> Non è possibile annullare questa operazione, la partita verrà
            interrotta.</p>
            <button class="btn btn-danger" id="do-term-game">Termina la partita</button>
        </div>
    </div>
</div>
