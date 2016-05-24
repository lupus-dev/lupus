<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!function_exists("randomName")) {
    function randomName() {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < 6; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        $randomString .= " ";
        for ($i = 0; $i < 8; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        return $randomString;
    }
}

?>

<h3 style="margin-top: 0">SCOOP!!</h3>
<p>Notizia bomba! Alcune troop di paparazzi del nostro quotidiano hanno appena tenuto sotto controllo
l'abitazione della nota celebrità <code><?= $username ?></code>.</p>

<?php if (count($visitors) == 0) { ?>
    <p>Forse non è una persona così importante... nessuno gli ha fatto visita questa notte... Che tristezza!</p>
<?php } else if (count($visitors) == 1) { ?>
    <p>Fate attenzione!! <code><?= $visitors[0] ?></code> ha fatto visita di soppiatto e credendo di non
        essere visto da nessuno ha fatto qualcosa...</p>
<?php } else { ?>
    <p>Che notte calda!! Diversi volti noti gli hanno fatto visita, un totale di ben <?= count($visitors) ?>
        persone sono andate a trovarlo. Le foto rivelano che gli ospiti sono stati
        <?php $chunks = array_chunk($visitors, count($visitors) - 1) ?>
        <?php foreach ($chunks[0] as $username) { ?>
            <code><?= $username ?></code>,
        <?php } ?>
        e <code><?= $chunks[1][0] ?></code>.
    </p>
<?php } ?>

<p class="pull-right">
    Articolo di <em>
        <?php if ($game->status >= GameStatus::Winy) { ?>
            <code><?= $event->event_data["paparazzo"] ?></code>
        <?php } else { ?>
            <?= randomName() ?>
        <?php } ?>
    </em>
</p>
