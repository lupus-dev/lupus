<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/room_status.php";
$public_rooms = $user->getPublicRoom();
?>
<?php if (count($public_rooms) > 0): ?>
    <div class="page-header">
        <h1>Le tue stanze pubbliche</h1>
    </div>
    <?php foreach ($public_rooms as $room): ?>
        <?php $room = Room::fromRoomName($room); ?>
        <h3>
            <?= $room->room_descr ?>
            <?php printRoomStatus($room) ?>
            <small>
                <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>"><?= $room->room_name ?></a>
            </small>
        </h3>
    <?php endforeach; ?>
<?php else: ?>
    <h3>Non hai stanze pubbliche</h3>
<?php endif; ?>
