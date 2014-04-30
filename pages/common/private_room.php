<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/room_status.php";
$private_rooms = $user->getPrivateRoom();
?>
<?php if (count($private_rooms) > 0): ?>
    <div class="page-header">
        <h1>Le tue stanze private</h1>
    </div>
    <?php foreach ($private_rooms as $room): ?>
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
    <h3>Non hai stanze private</h3>
<?php endif; ?>
