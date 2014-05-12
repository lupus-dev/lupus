<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */
?>
<h2 id="chat">Chat</h2>
<ul class="nav nav-tabs chat-groups"></ul>
<div class="chat-panel">
    <div class="chat-body">
        <p>Caricamento in corso...</p>
    </div>
    <form class="row chat-form" action="#" id="chat-form">
        <div class="col-sm-11">
            <input class="form-control input-sm" id="chat-text">
        </div>
        <div class="col-sm-1">
            <button type="submit" class="btn btn-sm btn-default pull-right btn-send-mex">
                Invia
            </button>
        </div>
    </form>
</div>
<?php insertScript("chat.js") ?>
<script>
    var username = '<?= $user->username ?>';
</script>