<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

?>

<p style="text-align: right"><em><?= $game->game_name ?>, <?= $date ?> alle <?= $hour ?></em></p>

<p>Benvenuto, ti trovi nel villaggio di <em><?= $game->game_name ?></em>!</p>

<p>Il sindaco della città ti porge i suoi omaggi e ti avverte di alcuni pericolosi eventi
    che hanno modificato l'umore di questa tranqulla cittadina.</p>
<p>Un branco di feroci lupi mannari sta prendendo piano piano il controllo della città,
    presta molta attenzione a non farti sbranare!!</p>


<?php if ($game->gen_info["gen_mode"] == "manual") { ?>
    <p>Alcuni analisti hanno fatto delle indagini sulla genealogia del villaggio, alcuni interessanti
        risultati sono stati resi pubblici. Nel villaggio sono presenti:</p>
    <?php $roles = $game->gen_info["manual"]["roles"] ?>
    <table class="table">
        <?php foreach ($roles as $role => $freq) { ?>
            <?php if ($freq > 0) { ?>
                <tr>
                    <td><?= $role ?></td>
                    <td><?= $freq ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
<?php } ?>
