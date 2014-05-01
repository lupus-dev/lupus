<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$aviableRoom = Level::getLevel($user->level)->aviableRoom - count($user->getPublicRoom()) - count($user->getPrivateRoom());
?>
<?php include __DIR__ . "/../common/public_room.php"; ?>
<br>
<?php include __DIR__ . "/../common/private_room.php"; ?>
<?php if ($aviableRoom > 0): ?>
    <br>
    <h1>Puoi creare nuove stanze!</h1>
    <p>
        Puoi creare ancora <?= $aviableRoom ?> stanz<?= ($aviableRoom == 1) ? "a" : "e" ?>
        <a class="btn btn-success btn-sm" href="room/_new">Crea!</a>
    </p>
<?php endif; ?>
