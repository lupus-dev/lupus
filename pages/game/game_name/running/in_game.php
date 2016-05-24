<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$engine = new Engine($game);
$role = Role::fromUser($user, $engine);

?>
<div class="col-sm-3 show-role">
    <div>Premi per il tuo ruolo</div>
    <div><?= $role->splash() ?></div>
</div>
<div class="clearfix"></div>
<div class="col-sm-6" class="vote">
    <?php include __DIR__ . "/vote.php"; ?>
</div>
<div class="col-sm-6 row" class="players">
    <?php include __DIR__ . "/players.php"; ?>
</div>
<div class="col-sm-8" class="news">
    <?php include __DIR__ . "/news.php"; ?>
</div>
<div class="col-sm-4" class="chat">
    <?php include __DIR__ . "/chat.php"; ?>
</div>

<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
    var pollFreq = <?= $needVote ? 10000 : 5000 ?>;
</script>

<?php insertScript("game.js"); ?>
