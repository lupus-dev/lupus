<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che contiene i gruppi delle chat
 */
class ChatGroup {

    /**
     * Il messaggio è diretto ad un utente specifico
     */
    const User = 1;
    /**
     * La chat è visibile a tutti i membri della partita
     */
    const Game = 2;
    /**
     * La chat è visibile solo ai lupi
     */
    const Lupi = 3;
    /**
     * La chat è visibile solo ai massoni
     */
    const Massoni = 4;

    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }

    /**
     * Ottiene il codice del gruppo della chat con il nome specificato
     * @param string $name Il nome della chat
     * @return \ChatGroup|boolean Il codice associato al nome della chat. False 
     * se la chat non esiste
     */
    public static function getChatGroup($name) {
        switch ($name) {
            case "User": return ChatGroup::User;
            case "Game": return ChatGroup::Game;
            case "Lupi": return ChatGroup::Lupi;
            case "Massoni": return ChatGroup::Massoni;
            default: 
                logEvent("Nome chat non riconosciuto: $name", LogLevel::Warning);
                return false;
        }
    }

    /**
     * Ottiene il nome della chat con il codice specificato
     * @param \ChatGroup $group Gruppo della chat
     * @return string|boolean Ritorna il nome della chat, false se non esiste
     */
    public static function getChatName ($group) {
        switch ($group) {
            case ChatGroup::User: return "User";
            case ChatGroup::Game: return "Game";
            case ChatGroup::Lupi: return "Lupi";
            case ChatGroup::Massoni: return "Massoni";
            default:
                logEvent("Gruppo chat non riconosciuto: $group", LogLevel::Warning);
                return false;
        }
    }
}
