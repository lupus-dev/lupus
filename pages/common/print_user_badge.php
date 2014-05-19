<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Stampa il badge con il livello di un utente
 * @param \User $user Utente di cui stampare il livello
 */
function printUserBadge($user) {
    $level = Level::getLevel($user->level)->name;
    ?>
<span class="label label-<?= strtolower($level) ?>"><?= $level ?></span>
    <?php
}