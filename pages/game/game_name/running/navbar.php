<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$day = GameTime::fromDay($game->day) == GameTime::Day;

$inverted = $day ? "" : "navbar-inverse";

$d = (int)($game->day / 2) + 1;
$dayName = $day ? "Giorno $d" : "Notte $d";

?>
<nav class="navbar navbar-default navbar-fixed-top <?= $inverted ?>">
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
                <li>
                    <span class="navbar-brand"><?= $dayName ?></span>
                </li>
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