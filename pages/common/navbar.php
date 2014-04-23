<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */
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
                    <li><a href="<?= $baseDir ?>/index">Home</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>