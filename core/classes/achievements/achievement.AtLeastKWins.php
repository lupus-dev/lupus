<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class AtLeastKWins extends Achievement {

    public static $enabled = false;

    /**
     * Numero minimo di partite da vincere per sbloccare l'obiettivo,
     * viene overridato dalle classe derivate
     * @var int
     */
    protected $K = 0;

    /**
     * Verifica se l'utente può sbloccare l'obiettivo
     * @param User $user Utente da controllare
     * @return bool True se l'utente può sbloccare l'obiettivo, false altrimenti
     */
    public function canObtain($user) {
        $sql = "SELECT game.status, player.role
                FROM player
                JOIN game ON player.id_game = game.id_game
                WHERE id_user=? AND game.status >= ? AND game.status < ?";
        $res = Database::query($sql, [$user->id_user, GameStatus::Winy, GameStatus::Winy+100]);

        if (!$res) return false;

        $wins = 0;
        foreach ($res as $game) {
            $teamCode = $game['status'] - GameStatus::Winy;
            $role = firstUpper($game['role']);
            $roleTeam = firstUpper($role::$team_name);

            if ($teamCode == $roleTeam::$team_code)
                $wins++;
        }
        
        return $wins >= $this->K;
    }
}
