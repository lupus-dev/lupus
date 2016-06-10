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

$achievements = Achievement::getUserAchievements($u);
$allAchievements = Achievement::getAllAchievements();

?>
<div class="page-header">
    <h1>
        <?= $u->username ?>
        <small><?= $u->name ?> <?= $u->surname ?></small>
        <?= printUserBadge($u) ?>
    </h1>
</div>

<div class="row">
    <?php foreach ($achievements as $achievement_name => $unlock_date) { ?>
        <?php $a = Achievement::getAchievementInfo($achievement_name); ?>
        <div class="col-md-4 achievement">
            <div class="panel panel-default">
                <div class="panel-body">
                    <img src="<?= $baseDir ?>/img/achievements/<?= $achievement_name ?>.png" class="achievement-icon pull-left">
                    <h4><?= $a["name"] ?></h4>
                    <p><?= $a["description"] ?></p>
                    <footer>Sbloccato il <?= (new DateTime($unlock_date))->format('d/m/Y \a\l\l\e H:i:s') ?></footer>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="row">
    <?php foreach ($allAchievements as $achievement_name) { ?>
        <?php if (!isset($achievements[$achievement_name])) { ?>
            <?php $a = Achievement::getAchievementInfo($achievement_name); ?>
            <div class="col-md-4 achievement">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <img src="<?= $baseDir ?>/img/achievements/<?= $a["achievement_name"] ?>.png" class="achievement-icon pull-left">
                        <h4><?= $a["name"] ?></h4>
                        <p><?= $a["description"] ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
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
