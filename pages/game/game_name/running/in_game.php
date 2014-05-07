<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

?>
<div class="col-sm-6" class="vote">
    <?php include __DIR__ . "/vote.php"; ?>
</div>
<div class="col-sm-6 row" class="players">
    <?php include __DIR__ . "/players.php"; ?>
</div>
<div class="col-sm-6" class="news">
    <?php include __DIR__ . "/news.php"; ?>
</div>
<div class="col-sm-6" class="chat">
    <?php include __DIR__ . "/chat.php"; ?>
</div>

<div class="clearfix"></div>
<p><small>Il tuo ruolo è: <?= Role::getRole($user, $game) ?></small></p>

<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>