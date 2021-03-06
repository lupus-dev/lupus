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

if (!file_exists(__DIR__ . "/../vendor/autoload.php"))
    die("Eseguire 'composer install' prima di avviare questa applicazione");

// include le librerie da Composer
require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/porting.php";

// include tutte le classi
require_once __DIR__ . "/requireDir.php";
requireDir(__DIR__ . "/classes");
requireDir(__DIR__ . "/functions");

// carica il file di configurazione
Config::loadConfig();

initErrorHandler();

$db = Database::connect() || die();

$baseDir = Config::$webapp_base;

session_start();

$id_user = false;
$login = false;
$user = null;

if (isset($_SESSION["id_user"])) {
    $id_user = $_SESSION["id_user"];
    $login = true;
    $user = User::fromIdUser($id_user);
}
