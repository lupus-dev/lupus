<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Stampa il label con lo stato di una partita
 * @param \Game $game Partita da visualizzare
 * @param \Room $room Stanza della partita
 * @param boolean $waiting Indica se il giocatore deve votare
 */
function printGameStatus($game, $room, $waiting) {
    $status = $game->status;
    if ($status == GameStatus::Setup) {
        $mex = "Setup";
        $color = "default";
    } else if ($status == GameStatus::Running) {
        if ($waiting) {
            $mex = "Vota!";
            $color = "success";
        } else {
            $mex = "Aspetta!";
            $color = "success";
        }
    } else if ($status == GameStatus::NotStarted) {
        $mex = "In attesa";
        $color = "primary";
    } else if ($status >= GameStatus::Winy && $status < GameStatus::TermByAdmin) {
        $mex = "Finita";
        $color = "primary";
        $waiting = false;
    } else if ($status >= GameStatus::TermByAdmin) {
        $mex = "Bloccata";
        $color = "danger";
        $waiting = false;
    }
    ?>
    <?php if ($waiting): ?>
        <a href="game/<?= $room->room_name ?>/<?= $game->game_name ?>" class="label label-<?= $color ?>">
            <?= $mex ?>
            <span class='glyphicon glyphicon-hand-left'></span></a>
    <?php else: ?>
        <span class="label label-<?= $color ?>">
            <?= $mex ?></span>
    <?php endif; ?>
    <?php
}
