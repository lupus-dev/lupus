<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Costanti che identificano lo stato di "privatezza" di una stanza
 */
class RoomPrivate {
    private function __constructor() {}

    /**
     * La stanza è aperta, tutti possono entrarci ed è elencata
     */
    const Open = 0;
    /**
     * La partita è aperta a tutti coloro che hanno il link, non è elencata
     */
    const LinkOnly = 1;
    /**
     * La partita è aperta solo ai giocatori abilitati, è elencata solo a questi giocatori
     */
    const ACL = 2;
}
