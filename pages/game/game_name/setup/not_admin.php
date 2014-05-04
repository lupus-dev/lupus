<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

?>
<div class="page-header">
    <h1>La partita <small><?= $room_name ?>/<?= $game_name ?></small> non è pronta!</h1>
</div>
<h3>Attendi che l'amministratore finisca di creare la partita</h3>
<h3>La pagina si aggiornerà automaticamente quando la partita sarà pronta!</h3>
<script>
    var room_name = "<?= $room_name ?>";
    var game_name = "<?= $game_name ?>";
</script>
<?php insertScript("waitGameStatus.js") ?>