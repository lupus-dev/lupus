<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per disconnettere l'utente cancellando la sua sessione
 */

// se l'utente non è connesso ritorna un errore
if (!$login)
    response (401, array("error" => "Utente non connesso"));

// disconnette l'utente
unset($_SESSION["id_user"]);

// (distrugge la sessione, forse overkilled?)
session_destroy();
session_unset();

// responso positivo
response(202, array("ok" => "Disconnesso"));