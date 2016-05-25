<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$events = Event::getGameEvent($game);

$days = [];
foreach ($events as $event) {
    $news = Event::getNewsFromEvent($event, $user);
    if (!$news) continue;
    if (!isset($days[$news["day"]]))
        $days[$news["day"]] = array();
    $days[$news["day"]][] = $news;
}

$keys = array_keys($days);
rsort($keys);

?>
<h2>Giornale</h2>
<div class="newspaper">
    <?php foreach ($keys as $day) { ?>
        <?php $newses = $days[$day]; ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="newspaper-header">
                    <h1>Lupus in Tabula</h1>
                    <p class="newspaper-day"><?= GameTime::getNameFromDay($day, true) ?></p>
                </div>

                <?php foreach ($newses as $news) { ?>
                    <div class="news col-md-6">
                        <?= $news["news"] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
