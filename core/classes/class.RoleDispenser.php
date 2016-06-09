<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class RoleDispenser {

    private function __construct() {}

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

        $gen_info = $game->gen_info;

        if ($gen_info["gen_mode"] == "auto")
            $rand_roles = RoleDispenser::generateAutoRoles($gen_info["auto"]["roles"], $gen_info["auto"]["num_players"], $admin);
        else if ($gen_info["gen_mode"] == "manual")
            $rand_roles = RoleDispenser::generateManualRoles($gen_info["manual"]["roles"], $admin);
        else {
            logEvent("Modalità di generazione non riconosciuta ({$gen_info["gen_mode"]})", LogLevel::Warning);
            return false;
        }

        if (!$rand_roles) {
            logEvent("Impossibile generare i ruoli", LogLevel::Error);
            return false;
        }
        shuffle($rand_roles);
        // assegna i ruoli ai giocatori
        $status = RoleDispenser::assignRoles($rand_roles, $game);
        if (!$status) {
            logEvent("Errore nell'assegnazione dei ruoli", LogLevel::Error);
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
    public static function getAvailableRoles($debugEnabled) {
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
     * @param \User $user L'amministratore della stanza
     * @return boolean|array Ritorna un vettore parzialmente disordinato (da 
     * mescolare) con i nomi dei ruoli generati. False se si verifica un errore.
     */
    private static function generateAutoRoles($roles_name, $num_players, $user) {
        if ($num_players < Config::$min_players) {
            logEvent("La partita non ha un numero sufficiente di giocatori ($num_players)", LogLevel::Warning);
            return false;
        }

        $roles_probability = array();
        $roles = array();

        $prob_sum = 0;
        foreach ($roles_name as $role) {
            if (!Role::roleExists($role, $user))
                return false;
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
        $num_lupus = ($num_players < Config::$lupus_cutoff) ?
                Config::$lupus_low : Config::$lupus_hi;

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
     * Genera una serie di ruoli in base ai parametri specificati 
     * @param array $roles Un array associativo: la chiave è il nome del ruolo, 
     * il valore è la frequenza del ruolo
     * @param \User $user L'amministratore della stanza
     * @return boolean|array Ritorna un vettore ordinato (da mescolare) con i 
     * nomi dei ruoli generati. False se si verifica un errore.
     */
    private static function generateManualRoles($roles, $user) {
        $num_players = array_sum($roles);

        if ($num_players < Config::$min_players) {
            logEvent("La partita non ha un numero sufficiente di giocatori ($num_players)", LogLevel::Warning);
            return false;
        }

        $res = array();
        
        // espande i ruoli aggiungendo le ripetizioni
        foreach ($roles as $role => $freq) {
            if (!Role::roleExists($role, $user))
                return false;
            for ($i = 0; $i < $freq; $i++)
                $res[] = $role;
        }
        
        return $res;
    }

    /**
     * Assegna i ruoli agli utenti nella partita
     * @param array $roles Elenco mescolato dei ruoli della partita
     * @param \Game $game Partita a cui appartengono i ruoli
     * @return boolean Ritorna true se l'operazione è riuscita. False altrimenti
     */
    private static function assignRoles($roles, $game) {
        $usernames = $game->getPlayers();
        $id_game = $game->id_game;

        if (count($roles) != count($usernames)) {
            logEvent("Il numero di ruoli generati non corrisponde con il numero di giocatori", LogLevel::Error);
            logEvent("Giocatori registrati: " . json_encode($usernames), LogLevel::Error);
            logEvent("Giocatori generati: " . json_encode($roles), LogLevel::Error);
            return false;
        }

        /*
         * UPDATE player SET role = CASE id_user
         *      WHEN 102 THEN 'Lupo'
         *      WHEN 105 THEN 'Contadino'
         *      WHEN 108 THEN 'Lupo'
         * END
         * WHERE id_game = 5
         */

        $params = [];
        $query = "UPDATE player SET role=CASE id_user ";
        for ($i = 0; $i < count($roles); $i++) {
            $id_user = User::fromUsername($usernames[$i])->id_user;
            /** @var string $role_name just to suppress error messages... */
            $role = $roles[$i]::$role_name;
            $query .= "WHEN ? THEN ? ";
            $params[] = $id_user;
            $params[] = $role;
        }
        $query .= "END WHERE id_game=?";
        $params[] = $id_game;

        $res = Database::query($query, $params);
        if (!$res)
            return false;
        return true;
    }

}
