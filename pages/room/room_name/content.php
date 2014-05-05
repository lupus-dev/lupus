<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../../common/print_game_status.php";

$room_name = $matches[1];
$room = Room::fromRoomName($room_name);
$games = $room->getGame();

$games = array_reverse($games);

for ($i = 0; $i < count($games); $i++)
    $games[$i] = Game::fromRoomGameName($room->room_name, $games[$i]);

if (count($games) == 0)
    $last = "none";
else if ($games[0]->status == GameStatus::Setup)
    $last = "setup";
else if ($games[0]->status < GameStatus::Winy)
    $last = "playing";
else if ($games[0]->status < GameStatus::TermByAdmin)
    $last = "win";
else
    $last = "term";
?>
<div class="page-header">
    <h1><?= $room->room_descr ?> <small><?= $room->room_name ?></small></h1>
</div>
<?php if (count($games) > 0): ?>
    <?php if ($last == "win" || $last == "term"): ?>
        <h2>
            La stanza è libera! <small>Crea una nuova partita</small> 
            <button class="btn btn-success btn-lg">Crea!</button>
        </h2>
        <hr>
    <?php endif; ?>
    <?php if ($last == "setup"): ?>
        <h2>Una partita è in fase di progetto</h2>
    <?php elseif ($last == "playing"): ?>
        <h2>Una partita è in corso</h2>
    <?php elseif ($last == "win"): ?>    
        <h2>Tutte le partite sono concluse <small>L'ultima...</small></h2>
    <?php else: ?>
        <h2>L'ultima partita è stata bloccata</h2>
    <?php endif; ?>
    <h3>
        <?= $games[0]->game_descr ?>
        <?php $hasToVote = $games[0]->hasToVote($user); ?>
        <?php printGameStatus($games[0], $room, $hasToVote); ?>
        <small>
            <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>"><?= $room->room_name ?></a>
            /
            <a href="<?= $baseDir ?>/game/<?= $room->room_name ?>/<?= $games[0]->game_name ?>"><?= $games[0]->game_name ?></a>
        </small>
    </h3>
    <?php if (count($games) > 1): ?>
        <hr>
        <h2>Archivio partite</h2>
        <?php for ($i = 1; $i < count($games); $i++): ?>
            <p>
                <?= $games[$i]->game_descr ?>
                <?php $hasToVote = $games[$i]->hasToVote($user); ?>
                <?php printGameStatus($games[$i], $room, $hasToVote); ?>
                <small>
                    <a href="<?= $baseDir ?>/room/<?= $room->room_name ?>"><?= $room->room_name ?></a>
                    /
                    <a href="<?= $baseDir ?>/game/<?= $room->room_name ?>/<?= $games[$i]->game_name ?>"><?= $games[$i]->game_name ?></a>
                </small>
            </p>
        <?php endfor; ?>
    <?php endif; ?>
<?php else: ?>
    <h2>In questa stanza non si sono disputate partite</h2>
    <p>Creane una!</p>
    <button class="btn btn-success btn-lg">Crea!</button>
<?php endif; ?>
