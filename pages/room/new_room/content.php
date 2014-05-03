<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$canPublic = $user->canCreatePublicRoom();
$canPrivate = $user->canCreatePrivateRoom();
$aviablePrivate = min(array(
    Level::getLevel($user->level)->privateRoom - count($user->getPrivateRoom()),
    Level::getLevel($user->level)->aviableRoom - count($user->getPrivateRoom()) - count($user->getPublicRoom())
        ));
?>
<div class="page-header">
    <h1>Crea una nuova stanza</h1>    
</div>
<?php if ($canPublic): ?>
    <div class="col-md-6">
        <div class="has-error has-feedback short-name">
            <label for="room-name">Nome della stanza</label>
            <input class="form-control" id="room-name" onchange="checkRoomName()">
            <span class="glyphicon glyphicon-remove form-control-feedback" id="room-name-icon"></span>
        </div>    
        <div class="has-error has-feedback">
            <label for="room-desc">Descrizione della stanza</label>
            <input class="form-control" id="room-desc" onchange="checkRoomDescr()">
            <span class="glyphicon glyphicon-remove form-control-feedback" id="room-desc-icon"></span>
        </div>    
        <?php if ($canPrivate): ?>
            <input type="checkbox" id="private"> <label for="private">Privata</label>
            <span>(ancora <?= $aviablePrivate ?>)</span>
        <?php endif; ?>
        <br>
        <button class="btn btn-success btn-lg" onclick="newRoom()">Crea!</button>
    </div>
<?php else: ?>
    <h3>Non puoi creare altre stanze!</h3>
    <p>Gioca e aumenta il tuo livello per sbloccare altre stanze!</p>
<?php endif; ?>
<script>
    $("#room-name").popover({
        html: true,
        content: "<ul><li>Il nome della stanza non può essere più lungo di 10 caratteri<li>E' formato da sole lettere maiuscole/minuscole e numeri<li>Il primo carattere deve essere una lettera<li>Il nome di una stanza è unico",
        title: "Errore nel formato",
        container: "body",
        trigger: "manual"
    });
</script>