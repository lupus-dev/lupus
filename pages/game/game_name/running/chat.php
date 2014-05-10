<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */
?>
<h2>Chat</h2>
<ul class="nav nav-tabs chat-groups">
    <!--<li class="active"><a href="#">Partita</a></li>
    <li class=""><a href="#">Lupi</a></li>
    <li class="dropdown">
        <a class="dropdown-toggle" href="#" data-toggle="dropdown">
            Utente <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="#">utente</a></li>
            <li><a href="#">pippo</a></li>
            <li><a href="#">root</a></li>
        </ul>
    </li>-->
</ul>
<div class="chat-panel">
    <div class="chat-body">
        <p>Caricamento in corso...</p>
        <!--<div class="chat-message">
            <span class="chat-time">12 ott - 21.21.21</span>
            <span class="chat-from">edomora97</span>
            <span class="chat-mex">Ciao mondo!</span>
        </div>-->
    </div>
    <form class="row chat-form" action="#">
        <div class="col-sm-11">
            <input class="form-control input-sm" id="chat-text">
        </div>
        <div class="col-sm-1">
            <button type="submit" onclick="sendMessage()"
                    class="btn btn-sm btn-default pull-right btn-send-mex">
                Invia
            </button>
        </div>
    </form>
</div>
<?php insertScript("chat.js") ?>
<script>
    var username = '<?= $user->username ?>';
</script>