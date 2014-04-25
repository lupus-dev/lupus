<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe statica che gestisce la connessione al database
 */
class Database {
    /**
     * Il database connesso. Null se non è connesso
     * @var \mysqli
     */
    public static $mysqli = null;

    /**
     * Il costruttore è privato perchè è una classe statica
     */
    private function __construct() {}

    /**
     * Connette la classe al database specificato nel file di configurazione
     */
    public static function connect() {
        Database::$mysqli = mysqli_connect(
                Config::$db_host, Config::$db_user, Config::$db_password, Config::$db_database, Config::$db_port);
        
        if (!Database::$mysqli || Database::$mysqli->errno) {
            logEvent("Errore connessione DB", LogLevel::Error);
            return false;
        }
        logEvent("Connesso al DB", LogLevel::Verbose);
        return true;
    }
    /**
     * Esegue una query sql e ritorna il risultato come vettore
     * @param string $query La query SQL da eseguire
     * @return boolean|mixed False se si verifica un errore. True nelle query 
     * che ritornano true. Un vettore nelle altre query
     */
    public static function query($query) {
        logEvent("Query: $query", LogLevel::Verbose);
        $result = Database::$mysqli->query($query);
        if (Database::$mysqli->errno) {
            logEvent("Query fallita: " . Database::$mysqli->error, LogLevel::Error);
            return false;
        }
        if ($result === TRUE || $result === FALSE)
            return $result;
        $res = array();
        while ($row = $result->fetch_assoc())
            $res[] = $row;
        return $res;
    }
    /**
     * Effettua l'escape di una stringa usando il database connesso
     * @param string $string Stringa da "escappare"
     * @return string La stringa sicuras
     */
    public static function escape($string) {
        return Database::$mysqli->escape_string($string);
    }
}
