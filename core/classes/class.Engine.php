<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che si occupa di far muovere una partita
 */
class Engine {
    /**
     * La partita è avanzata di un giorno/notte
     */
    const Ok = 1;
    /**
     * La partita non è avanzata a causa di alcuni voti richiesti
     */
    const NeedVote = 2;
    /**
     * La partita è stata terminata a causa di un ruolo non riconosciuto
     */
    const BadRole = 500;
    /**
     * La partita è stata terminata a causa di un'azione fallita
     */
    const BadAction = 501;
    
    /**
     * La partita da far muovere
     * @var \Game
     */
    public $game;
    
    /**
     * Crea un nuovo motore basandosi sulla partita specificata
     * @param \Game $game Partita in cui il motore girerà
     */
    public function __construct($game) {
        $this->game = $game;
    }
    
    /**
     * Esegue il motore della partita. Fa avazare il gioco se serve
     * @return int Ritorna un valore che indica il codice di uscita del motore
     * (vedi costanti in \Engine)
     */
    public function run() {
        $roles = $this->getAllRoles();
        if (!$roles) {
            $this->game->status(GameStatus::TermByBug);
            return Engine::BadRole;
        }
        foreach ($roles as $role)
            if ($role->needVote() != false)
                return Engine::NeedVote;
        shuffle($roles);
        usort($roles, array("Role", "cmpRole"));
        foreach ($roles as $role) 
            if (!$role->performAction()) {
                $this->game->status(GameStatus::TermByBug);
                return Engine::BadAction;
            }
        return Engine::Ok;
    }
    
    /**
     * Ottiene un vettore di Role contentente tutti i giocatori della partita
     * @return array|boolean False se si verifica un errore. Un vettore con tutti
     * i giocatori altrimenti
     */
    private function getAllRoles() {
        $id_game = $this->game->id_game;
        $query = "SELECT id_user,role FROM role WHERE id_game=$id_game";
        $res = Database::query($query);
        if (!$res)
            return false;
        
        $roles = array();
        
        foreach ($res as $role) {
            $id_user = $role["id_user"];
            $role = $role["role"];
            
            $user = User::fromIdUser($id_user);
            
            $role_name = firstUpper($role);
            
            if (!class_exists($role_name) || !in_array("Role", class_parents($role_name)))
                return false;
            
            $role = new $role_name($user, $this->game);
            $roles[] = $role;
        }
        
        return $roles;
    }
}