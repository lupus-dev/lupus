<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene le informazioni sugli stati
 */
class Status {

    /**
     * Elenco delgi stati di una partita
     * @var array
     */
    private static $status = array(
        0 => "Setup",
        100 => "Not started",
        101 => "Started",
        200 => "Win 0",
        201 => "Win 1",
        202 => "Win 2",
        300 => "Term by admin",
        301 => "Term by solitude",
        302 => "Term by vote",
        303 => "Term by bug",
        304 => "Term by GameMaster"
    );

    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }

    /**
     * Ottiene l'elenco degli stati riconosciuti
     * @return type
     */
    public static function getStatuses() {
        return Status::$status;
    }

    /**
     * Ottiene la stringa di uno stato
     * @param int $num Stato
     * @return boolean|string Ritona il messaggio dello stato $num. Se non esiste
     * ritorna false
     */
    public static function getStatus($num) {
        if (isset(Status::$status[$num]))
            return Status::$status[$num];
        return false;
    }

}
