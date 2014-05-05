<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

function printUserRole($role_name) {
    $roleTeam = $role_name::$team_name;
    $mana = $role_name::$mana;
    $color = ($roleTeam == Antagonist::$team_name) ? "danger" : "success";
    $glyph = ($mana == Mana::Bad) ? "fire" : "leaf";
    ?>
    <span class="label label-<?= $color ?>">
        <?= $role_name::$name ?>
        <span class="glyphicon glyphicon-<?= $glyph ?>"></span>
    </span>
    <?php
}
