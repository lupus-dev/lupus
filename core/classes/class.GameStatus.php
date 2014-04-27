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
class GameStatus {
    /**
     * La partita è ancora in fase di costruzione
     */
    const Setup = 0;
    /**
     * La paritita non è ancora iniziata
     */
    const NotStarted = 100;
    /**
     * La partita è in corso
     */
    const Running = 101;
    /**
     * La partita è terminata ed ha vinto la squadra y. Sommare a questo stato y
     */
    const Winy = 200;
    /**
     * La partita è terminata ma non ha vinto nessuno perchè tutti i giocatori
     * sono morti
     */
    const DeadWin = 299;
    /**
     * La partita è stata terminata dall'admin
     */
    const TermByAdmin = 300;
    /**
     * La partita è stata terminata per numero insufficiente di giocatori
     */
    const TermBySolitude = 301;
    /**
     * La partita è stata terminata per votazione
     */
    const TermByVote = 302;
    /**
     * La partita è stata terminata a causa di un bug
     */
    const TermByBug = 303;
    /**
     * La partita è stata terminata dal GameMaster
     */
    const TermByGameMaster = 304;
    
    /**
     * Costruttore privato
     */
    private function __construct() {
        
    }
}
