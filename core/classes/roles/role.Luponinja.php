<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta il ruolo Lupo ninja
 */
class Luponinja extends Lupo {
    
    public static $role_name = "luponinja";
    public static $name = "Luponinja";
    public static $debug = true;
    public static $gen_probability = 0.5;
    public static $gen_number = 1;
    
    /**
     * Esegue l'azione associata ai lupi:
     * <ol>
     * <li>Cerca i voti degli altri lupi
     * <li>Se non è stato l'ultimo a votare si ferma
     * <li>Altrimenti ordina i bersagli per numero di voti
     * <li>Se il più votato ha raggiunto il quorum, cerca di ucciderlo
     * <li>Altrimenti fa nulla
     * </ol>
     * @return boolean Ritorna true se tutto è andato per il verso giusto. False
     * se il giocatore da uccidere non esiste
     */
    public function performActionNight() {
        // esegue le azioni associate ad un normale lupo
        $res = parent::performActionNight();
        if (!$res)
            return false;
        // se non è questo utente che deve agire, non fa nulla
        if ($this->voted == null)
            return true;
        // se il lupo ha agito, rimuove la visita
        $this->unvisit($this->voted);
        return true;
    }
    
    public function splash() {
        // Muahahaah I lied!
        return "Sei un lupo";
    }
}