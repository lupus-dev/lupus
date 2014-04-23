<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * Questo file si occupa di integrare tutti i file necessari per ogni script 
 * del server. Andrebbe incluso in ogni punto di accesso del server (wrapper,
 * index delle api, ecc...)
 */

// include tutte le classi
require_once __DIR__ . "/requireDir.php";
requireDir(__DIR__ . "/classes");
requireDir(__DIR__ . "/functions");

// carica il file di configurazione
Config::loadConfig();
Database::connect() || die("Errore");

$baseDir = "/lupus";

session_start();

