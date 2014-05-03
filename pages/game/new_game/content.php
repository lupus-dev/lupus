<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$room_name = $matches[1];

$room = Room::fromRoomName($room_name);

if ($room) {
    if ($room->id_admin != $user->id_user)
        $admin = false;
    else
        $admin = true;

    $allTerm = $room->isAllTerminated();
}
?>
<div class="page-header">
    <h1>Crea una nuova partita <small><?= $room_name ?></small></h1>    
</div>
<?php if ($room): ?>
    <?php if ($admin): ?>        
        <?php if ($allTerm): ?>
            <?php include __DIR__ . "/form.php"; ?>
            <script>var room_name ="<?= $room_name ?>";</script>
        <?php else: ?>
            <h2>C'è ancora una partita in corso!</h2>
        <?php endif; ?>
    <?php else: ?>
        <h2>Non sei l'amministratore di questa stanza!</h2>
    <?php endif; ?>
<?php else: ?>
    <h2>La stanza non esiste!</h2>
<?php endif; ?>
