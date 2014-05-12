<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$events = Event::getGameEvent($game);
$curr_day = -1;
?>
<h2>Giornale</h2>
<div class="newspaper">
    <?php foreach ($events as $event): ?>    
        <?php $news = Event::getNewsFromEvent($event, $user); ?>        
        <?php if ($news): ?>
            <?php if ($news["day"] != $curr_day): ?>
                <h1><?= GameTime::getNameFromDay($news["day"], true) ?></h1>
            <?php endif; ?>
            <?php $curr_day = $news["day"]; ?>
            <div class="news"><?= $news["news"] ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
