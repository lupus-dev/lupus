<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

function printRoomStatus($room) {
    $status = $room->isAllTerminated();
    if ($status) {
        $color = "success";
        $mex = "Libera";
    } else {
        $color = "warning";
        $mex = "In corso";
    }
    ?>
    <span class="label label-<?= $color ?>"><?= $mex ?></span>
    <?php
}
