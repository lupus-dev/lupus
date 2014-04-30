<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$_index = startsWith($request, "index") ? "active" : "";
$_game = startsWith($request, "game") ? "active" : "";
$_room = startsWith($request, "room") ? "active" : "";

?>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= $baseDir ?>">Lupus in tabula</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <?php if (!$login): ?>
                    <li class="active"><a href="<?= $baseDir ?>/login">Login</a></li>
                <?php else: ?>
                    <li class="<?= $_index ?>"><a href="<?= $baseDir ?>/index">Home</a></li>                    
                    <li class="<?= $_game ?>"><a href="<?= $baseDir ?>/game">Partite</a></li>                    
                    <li class="<?= $_room ?>"><a href="<?= $baseDir ?>/room">Stanze</a></li>                    
                <?php endif; ?>
            </ul>
            <?php if ($login): ?>
                <div class="navbar-right">
                    <span class="navbar-text">
                        Benvenuto <?= $user->name ?> <?= $user->surname ?> 
                        <small>(<?= $user->username ?>)</small>
                    </span>
                    <button type="button" class="btn btn-warning navbar-btn" onclick="logout()">Logout</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>