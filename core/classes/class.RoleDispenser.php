<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class RoleDispenser {

    private function __construct() {
        
    }

    public static function Compute($game) {
        
    }

    /**
     * Ottiene i nomi dei ruoli validi ed utilizzabili
     * @param boolean $debugEnabled True se i ruoli di sperimentali sono abilitati
     * @return boolean|array Ritorna un vettore di string con i nomi dei ruoli 
     * utilizzabili. False se si verifica un errore
     */
    private static function getRoles($debugEnabled) {
        $dir = __DIR__ . "/roles/";

        $files = scandir($dir);
        if (!$files)
            return false;

        $roles = array();

        foreach ($files as $file) {
            $matches = array();
            // seleziona dalla cartella solo i file che rispettano il formato corretto
            if (preg_match("/role\.([a-zA-Z0-9]+)\.php/", $file, $matches)) {
                $role = $matches[1];
                if (class_exists($role) && in_array("Role", class_parents($role)))
                    if ($role::$enabled && !($role::$debug && !$debugEnabled))
                        $roles[] = $matches[1];
            }
        }

        return $roles;
    }

}
