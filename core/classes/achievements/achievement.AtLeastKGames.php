<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class AtLeastKGames extends Achievement {

    /**
     * Metaobiettivo disabilitato
     * @var bool
     */
    public static $enabled = false;

    protected $K = 0;

    /**
     * L'utente può sbloccare l'obiettivo solo se ha giocato almeno K partite
     * @param User $user Utente da controllare
     * @return bool True se l'utente può sbloccare l'obiettivo, false altrimenti
     */
    public function canObtain($user) {
        $sql = "SELECT COUNT(*) AS conteggio FROM player WHERE id_user = ?";
        $res = Database::query($sql, [$user->id_user]);

        if (!$res) return false;

        $num = $res[0]["conteggio"];

        return $num >= $this->K;
    }
}
