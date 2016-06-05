<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../common/print_user_badge.php";
require_once __DIR__ . "/../common/print_game_status.php";
require_once __DIR__ . "/../common/print_game.php";

$thisUser = $u->id_user == $user->id_user;

if ($thisUser)
    $notifications = Notification::getLastNotifications($user, null, true, 10);

$games = $u->getEndedGame($thisUser);

?>
<div class="page-header">
    <h1>
        <?= $u->username ?>
        <small><?= $u->name ?> <?= $u->surname ?></small>
        <?= printUserBadge($u) ?>
    </h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="page-header">
            <h2><?= $thisUser?"Hai":"Ha" ?> giocato in queste partite</h2>
        </div>
        <?php foreach ($games as $game) { ?>
            <p>
                <?php $room = Room::fromRoomName($game["room_name"]); ?>
                <?php $game = Game::fromRoomGameName($game["room_name"], $game["game_name"]); ?>
                <?php printGame($game, $room, $user); ?>
            </p>
        <?php } ?>
        <?php if (count($games) == 0) { ?>
            <em>Nessuna partita <?= $thisUser ? '' : 'pubblica' ?> giocata...</em>
        <?php } ?>
    </div>

    <?php if ($thisUser) { ?>
        <div class="col-md-6">
            <div class="page-header">
                <h2>Notifiche recenti</h2>
            </div>
            <?php foreach ($notifications as $noti) { ?>
                <a href="<?= $noti->link ?>" class="notification-element <?= $noti->hidden?'notification-hidden':'' ?>">
                    <?= $noti->message ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</div>
