<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$canPublic = $user->canCreatePublicRoom();
$canPrivate = $user->canCreatePrivateRoom();
$aviablePrivate = Level::getLevel($user->level)->privateRoom - count($user->getPrivateRoom());
    
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
