<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

?>
<p><strong>Miracolo!!</strong> Forse un miracolo, forse fortuna, fatto stà che
    <code><a href="<?= $baseDir ?>/user/<?= $dead ?>"><?= $dead ?></a></code>
    è resuscitato, bentornato tra i vivi!</p>
<?php if ($game->status >= GameStatus::Winy) { ?>
    <p>Un uccellino ha avvisato la redazione, è stato il becchino
        <code><a href="<?= $baseDir ?>/user/<?= $becchino ?>"><?= $becchino ?></a></code> che attraverso
        degli esperimenti ha portato in vita <code><a href="<?= $baseDir ?>/user/<?= $dead ?>"><?= $dead ?></a>.</p>
<?php } ?>
