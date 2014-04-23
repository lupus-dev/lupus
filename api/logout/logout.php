<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!$login)
    response (401, array("error" => "Utente non connesso"));

unset($_SESSION["id_user"]);

session_destroy();
session_unset();

response(202, array("ok" => "Disconnesso"));