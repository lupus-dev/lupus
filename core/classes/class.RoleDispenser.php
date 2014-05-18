<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class RoleDispenser {

    /**
     * Numero minimo di giocatori in una partita
     */
    const MinPlayers = 7;

    /**
     * Numero di lupi da generare se ci sono almeno Lupus1 lupi
     */
    const LupusMin1 = 2;

    /**
     * Numero minimo di giocatori perchè vengano generati LupusMin2 lupi
     */
    const Lupus2 = 15;

    /**
     * Numero di lupi da generare se ci sono almeno Lupus2 lupi
     */
    const LupusMin2 = 3;

    private function __construct() {
        
    }

    /**
     * Genera e assegna i ruoli agli utenti nella partita
     * @param \Game $game Partita in cui aggiungere i ruoli
     * @return boolean True se l'operazione riesce. False altrimenti
     */
    public static function Compute($game) {

        $room = Room::fromIdRoom($game->id_room);
        if (!$room) {
            logEvent("La stanza {$game->id_room} non esiste", LogLevel::Warning);
            return false;
        }
        $admin = User::fromIdUser($room->id_admin);
        if (!$admin) {
            logEvent("L'amministratore della stanza {$room->room_name} non esiste", LogLevel::Warning);
            return false;
        }
        // verifica se l'amministratore dispone dei ruoli beta
        $debugEnabled = Level::getLevel($admin->level)->betaFeature;
        // ottiene tutti i ruoli disponibili
        $roles = RoleDispenser::getAviableRoles($debugEnabled);
        if (!$roles) {
            logEvent("Impossibile recuperare i nomi dei ruoli", LogLevel::Error);
            return false;
        }
        // genera i ruoli casualmente e li mescola
        $rand_roles = RoleDispenser::generateRoles($roles, $game->num_players);
        if (!$rand_roles) {
            logEvent("Impossibile generare i ruoli", LogLevel::Warning);
            return false;
        }
        shuffle($rand_roles);
        // assegnai ruoli ai giocatori
        $status = RoleDispenser::assignRoles($rand_roles, $game);
        if (!$status) {
            logEvent("Errore nell'assegnazione dei ruoli", LogLevel::Warning);
            return false;
        }
        return true;
    }

    /**
     * Ottiene i nomi dei ruoli validi ed utilizzabili
     * @param boolean $debugEnabled True se i ruoli di sperimentali sono abilitati
     * @return boolean|array Ritorna un vettore di string con i nomi dei ruoli 
     * utilizzabili. False se si verifica un errore
     */
    private static function getAviableRoles($debugEnabled) {
        // i ruoli sono nella cartella /core/classes/roles
        $dir = __DIR__ . "/roles/";

        $files = scandir($dir);
        if (!$files) {
            logEvent("La cartella dei ruoli non è accessibile", LogLevel::Error);
            return false;
        }
        $roles = array();

        foreach ($files as $file) {
            $matches = array();
            // seleziona dalla cartella solo i file che rispettano il formato corretto
            if (preg_match("/role\.([a-zA-Z0-9]+)\.php/", $file, $matches)) {
                $role = $matches[1];
                if (class_exists($role) && in_array("Role", class_parents($role))) {
                    if ($role::$enabled && !($role::$debug && !$debugEnabled))
                        $roles[] = $matches[1];
                } else
                    logEvent("Nella cartella dei ruoli c'è un file che non codifica un ruolo valido ($file)", LogLevel::Notice);
            } else if (!startsWith($file, "."))
                logEvent("Nella cartella dei ruoli è presente un file ($file) con nome non valido", LogLevel::Notice);
        }

        return $roles;
    }

    /**
     * Genera casualmente una serie di ruoli in base al numero di giocatori
     * nella partita
     * @param array $roles_name Vettore con i nomi dei ruoli disponibili. Ogni
     * nome deve essere il nome corretto di una classe derivata da \Role.
     * @param int $num_players Numero di giocatori nella partita
     * @return boolean|array Ritorna un vettore parzialmente disordinato (da 
     * mescolare) con i nomi dei ruoli generati. False se si verifica un errore.
     */
    private static function generateRoles($roles_name, $num_players) {
        if ($num_players < RoleDispenser::MinPlayers) {
            logEvent("La partita non ha un numero sufficiente di giocatori ($num_players)", LogLevel::Warning);
            return false;
        }

        $roles_probability = array();
        $roles = array();

        $prob_sum = 0;
        foreach ($roles_name as $role) {
            $role_prob = $role::$gen_probability;
            $roles_probability[] = $role_prob;
            $prob_sum += $role_prob;
        }
        // se la somma delle probabilità è zero, allora i ruoli non sono buoni
        if ($prob_sum == 0) {
            logEvent("I ruoli non sono consisitenti", LogLevel::Error);
            return false;
        }
        // shifta tutti i ruoli affinchè la somma sia 1
        for ($i = 0; $i < count($roles_probability); $i++)
            $roles_probability[$i] /= $prob_sum;

        // sceglie il numero di lupi
        $num_lupus = ($num_players < RoleDispenser::Lupus2) ?
                RoleDispenser::LupusMin1 : RoleDispenser::LupusMin2;

        // genera i lupi
        for ($i = 0; $i < $num_lupus; $i++) {
            $roles[] = Lupo::$role_name;
            $num_players--;
        }

        while ($num_players > 0) {
            // numero casuale compreso tra 0 e 1
            $rand = rand() / getrandmax();
            $curr_sum = $roles_probability[0];
            $curr_pos = 1;
            // cerca quale ruolo è stato generato
            while ($curr_sum < $rand) 
                $curr_sum += $roles_probability[$curr_pos++];
            $curr_pos--;
            // nome del ruolo generato
            $role = $roles_name[$curr_pos];
            // se il ruolo prevede di generare troppi utenti, la generazione 
            // viene scartata
            if ($num_players < $role::$gen_number) {
                logEvent("Un ruolo è stato scartato perchè troppo popoloso ($role)", LogLevel::Debug);
                continue;
            }
            // genera una serie di ruoli uguali
            for ($i = 0; $i < $role::$gen_number; $i++) {
                $roles[] = $roles_name[$curr_pos];
                $num_players--;
            }
        }
        return $roles;
    }

    /**
     * Assegna i ruoli agli utenti nella partita
     * @param array $roles Elenco mescolato dei ruoli della partita
     * @param \Game $game Partita a cui appartengono i ruoli
     * @return boolean Ritorna true se l'operazione è riuscita. False altrimenti
     */
    private static function assignRoles($roles, $game) {
        $usernames = $game->getPlayers();
        if (count($roles) != count($usernames)) {
            logEvent("Il numero di ruoli generati non corrisponde con il numero di giocatori", LogLevel::Error);
            return false;
        }
        $query = "INSERT INTO role (id_game,id_user,role,status,data) VALUES ";
        $values = array();

        $id_game = $game->id_game;
        $alive = RoleStatus::Alive;

        for ($i = 0; $i < count($roles); $i++) {
            $user = User::fromUsername($usernames[$i]);
            if (!$user) {
                logEvent("L'utente {$usernames[$i]} non esiste", LogLevel::Warning);
                return false;
            }
            $id_user = $user->id_user;
            $role = $roles[$i]::$role_name;
            $values[] = "($id_game,$id_user,'$role',$alive,'')";
        }

        $query .= implode(", ", $values);
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
    }

}
