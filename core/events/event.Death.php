<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if ($event->event_data["cause"] == "kill-day") {
    ?><p>Una votazione democratica ha decretato la messa al rogo di <?= $username ?>
    per sospettate azioni da lupo mannaro. Forse questa scelta renderà le strade
    della città più sicure.</p><?php
} else {
    ?><p>La nostra tranquilla cittadina non è più un posto sicuro, il cadavere di <?= $username ?>
    è stato ritrovato questa mattina. Si raccomanda i cittadini di prestare la massima attenzione.</p><?php
}

if ($game->status >= GameStatus::Winy) {
    switch ($event->event_data["cause"]) {
        case "kill-day":
            ?><p>Il fidato concittadino <?= $killer ?> è stato scelto dall'assemblea per imbastire
                il rogo.</p><?php
            break;
        case "kill-assassino":
            ?><p>Accurate analisi della polizia scientifica hanno scoperto che <?= $killer ?> ha assassinato
                brutalmente il povero <?= $username ?>.</p><?php
            break;
        case "kill-lupo":
            ?><p>I segni dei morsi di <?= $killer ?> sono l'evidente causa del decesso.</p><?php
            break;
        case "suicidio-pastore":
            ?><p>Il suo testamento recitava i motivi del suo suicidio, non avendo altre pecore da sacrificare
                ha deciso di lasciare questo mondo...</p><?php
            break;
    }
}
