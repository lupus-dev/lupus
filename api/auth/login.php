<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per effettuare il login tramite sessione
 * /login?username=(username)&password=(password)
 * /login/(username)?password=(password)
 * 
 * il secondo ha priorità maggiore
 */

if ($login)
    response (401, array(
        "error" => "L'utente è già connesso, effettua prima il logout", 
        "code" => APIStatus::LoginAlreadyDone));

// se la richiesta è del tipo /login/(username)
// (username) ha priorità maggiore di $_GET
if (isset($apiMatches[1]))
    $_GET["username"] = $apiMatches[1];

// se non ci sono i parametri necessari
if (!isset($_GET["username"]) || !isset($_GET["password"]))
    response (400, array(
        "error" => "Specificare username e password",
        "code" => APIStatus::MissingParameter));

$username = $_GET["username"];
$password = $_GET["password"];

// verifica se il login è corretto
$id_user = User::checkLogin($username, $password);

// se non è corretto ritona un errore
if (!$id_user)
    response (401, array(
        "error" => "Nome utente/password errati",
        "code" => APIStatus::Fail));

// altrimenti salva nella sessione il login
$_SESSION["id_user"] = $id_user;
response(202, array(
    "ok" => "Login effettuato",
    "code" => APIStatus::Done));
