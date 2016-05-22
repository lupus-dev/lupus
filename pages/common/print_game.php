<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Stampa il nome e lo stato della partita
 * @param \Game $game Partita da stampare
 * @param \Room $room Stanza della partita
 * @param \User $user Utente corrente
 */
function printGame($game, $room, $user, $status = true) {
    global $baseDir;
    ?>
    <a href="<?= $baseDir ?>/game/<?= $room->room_name ?>/<?= $game->game_name ?>"><?= $game->game_descr ?></a>
    <?php $hasToVote = $game->hasToVote($user); ?>
    <?php if($status) printGameStatus($game, $room, $hasToVote); ?>
    <small>
        <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>"><?= $room->room_name ?></a>
        /
        <a href="<?= $baseDir ?>/game/<?= $room->room_name ?>/<?= $game->game_name ?>"><?= $game->game_name ?></a>
    </small>
<?php } ?>
