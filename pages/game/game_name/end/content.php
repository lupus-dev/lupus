<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../../../common/print_user_badge.php";
require_once __DIR__ . "/../../../common/print_user_role.php";

$game_status = $game->status;

if ($game_status < GameStatus::TermByAdmin) {
    $team = $game_status - GameStatus::Winy;
    if ($team != 99) {
        $winner = Team::fromTeamCode($team);
        if (!$winner)
            die ("La partita non ha uno stato valido...");
        $winner = $winner::$name;
    } else
        $winner = "Morte";
} else {
    if ($game_status == GameStatus::TermByAdmin)
        $message = "Partita terminata dall'amministratore";
    else if ($game_status == GameStatus::TermBySolitude)
        $message = "Partita terminata per solitudine";
    else if ($game_status == GameStatus::TermByVote)
        $message = "Partita terminata per votazione";
    else if ($game_status == GameStatus::TermByBug)
        $message = "Partita terminata a causa di un bug :(";
    else if ($game_status == GameStatus::TermByGameMaster)
        $message = "Partita terminata dal GameMaster";
    else
        $message = "Partita terminata male :(";
}

$alive = $game->getAlive();
$dead = $game->getDead();

$events = Event::getGameEvent($game);
$curr_day = -1;

?>
<div class="page-header">
    <h1><?= $game->game_descr ?> <small><?= $game->game_name ?></small></h1>
</div>
<?php if (isset($winner)): ?>
    <h1>La partita si è conclusa</h1>
    <h3>Ha vinto la fazione <span class="label label-info"><?= $winner ?></span></h3>
<?php else: ?>
    <h1><?= $message ?></h1>
<?php endif; ?>
<hr>
<h2>Al termine della parita, i giocatori...</h2>
<div class="col-sm-6">
    <h2>Vivi</h2>
    <?php if (!$alive): ?>
        <p>Sono morti tutti...</p>
    <?php else: ?>
        <ul>
            <?php foreach ($alive as $alive_user): ?>
                <li>
                    <h5>
                        <?= $alive_user->username ?>
                        <?php printUserBadge($alive_user); ?>
                        <?php printUserRole(Role::getRole($alive_user, $game)); ?>
                    </h5>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<div class="col-sm-6">
    <h2>Morti</h2>
    <?php if (!$dead): ?>
        <p>Non è morto nessuno... per ora!</p>
    <?php else: ?>
        <ul>
            <?php foreach ($dead as $dead_user): ?>
                <li>
                    <h5>
                        <?= $dead_user->username ?>
                        <?php printUserBadge($dead_user); ?>
                        <?php printUserRole(Role::getRole($dead_user, $game)); ?>
                    </h5>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<div class="clearfix"></div>
<hr>
<div class="col-md-8">
    <?php include __DIR__ . "/../running/news.php"; ?>
</div>
