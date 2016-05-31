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
     * Il database connesso. null se non è connesso
     * @var \PDO
     */
    private static $db = null;

    /**
     * Il database di MongoDB. null se non è connesso
     * @var null|MongoDB\Client
     */
    public static $mongo = null;

    /**
     * Il costruttore è privato perchè è una classe statica
     */
    private function __construct() {}

    /**
     * Connette la classe al database specificato nel file di configurazione
     */
    public static function connect() {
        try {
            Database::$db = new PDO(Config::$db_string, Config::$db_user, Config::$db_password);
            Database::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            logEvent("Errore connessione DB", LogLevel::Error);
            logEvent($e->getMessage(), LogLevel::Error);
            return false;
        }

        try {
            Database::$mongo = new MongoDB\Client(Config::$mongo_string);
            Database::$mongo->listDatabases();
        } catch (Exception $ex) {
            if (Config::$mongo_fallback)
                // disable mongo using the fallback
                Database::$mongo = null;
            else {
                logEvent("Errore connessione a MongoDB", LogLevel::Error);
                logEvent($ex->getMessage(), LogLevel::Error);
                return false;
            }
        }
        logEvent("Connesso al DB", LogLevel::Verbose);
        return true;
    }
    /**
     * Esegue una query sql e ritorna il risultato come vettore
     * @param string $sql La query SQL da eseguire
     * @return boolean|mixed False se si verifica un errore. True nelle query 
     * che ritornano true. Un vettore nelle altre query
     */
    public static function query($sql, $options = []) {
        logEvent("Query: $sql", LogLevel::Verbose);

        try {
            $query = Database::$db->prepare($sql);
            $query->execute($options);

            if ($query->columnCount() == 0)
                return true;

            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logEvent("Query fallita: " . $sql, LogLevel::Error);
            logEvent($e->getMessage(), LogLevel::Error);
            logEvent($e->getTraceAsString(), LogLevel::Error);
            return false;
        }
    }

    /**
     * Ritorna l'id dell'ultima riga inserita nel database.
     * @param null|string $name Utile solo se il driver di PDO richiede il nome della sequenza
     * @return string L'id dell'ultima riga inserita
     */
    public static function lastInsertId($name = null) {
        return Database::$db->lastInsertId($name);
    }
}
