<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

require_once __DIR__ . "/../../../common/print_user_badge.php";

$alive = $game->getAlive();
$dead = $game->getDead();

?>
<div class="col-md-6">
    <h2>Vivi</h2>
    <?php if (!$alive): ?>
        <p>Sono morti tutti...</p>
    <?php else: ?>
        <ul>
            <?php foreach ($alive as $alive_user): ?>
                <li>
                    <h5>
                        <a href="<?= $baseDir ?>/user/<?= $alive_user->username ?>"><?= $alive_user->username ?></a>
                        <?php printUserBadge($alive_user); ?>
                    </h5>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<div class="col-md-6">
    <h2>Morti</h2>
    <?php if (!$dead): ?>
        <p>Non è morto nessuno... per ora!</p>
    <?php else: ?>
        <ul>
            <?php foreach ($dead as $dead_user): ?>
                <li>
                    <h5>
                        <a href="<?= $baseDir ?>/user/<?= $dead_user->username ?>"><?= $dead_user->username ?></a>
                        <?php printUserBadge($dead_user); ?>
                    </h5>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
