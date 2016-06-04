<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per registrare un nuovo utente
 */

if ($login)
    response (401, array(
        "error" => "L'utente è già connesso, effettua prima il logout", 
        "code" => APIStatus::LoginAlreadyDone));

if (!isset($_GET["username"]) || !isset($_GET["password"]) || !isset($_GET["name"]) || !isset($_GET["surname"]))
    response (400, array(
        "error" => "Specificare i parametri username,password,name,surname",
        "code" => APIStatus::MissingParameter));

$username = $_GET["username"];
$password = $_GET["password"];
$name = $_GET["name"];
$surname = $_GET["surname"];

if (!preg_match("/^$shortName$/", $username))
    response (400, array(
        "error" => "Lo username è in un formato non valido",
        "code" => APIStatus::MalformedParameter));

$user = User::fromUsername($username);

if ($user)
    response (409, array(
        "error" => "Esiste già un utente con questo username",
        "code" => APIStatus::SignupAlreadyExists
    ));

$res = User::signup($username, $password, $name, $surname);
if (!$res)
    response (500, array(
        "error" => "Registrazione non riuscita",
        "code" => APIStatus::FatalError
    ));

// effettua il login
$_SESSION["id_user"] = $res->id_user;

response(201, array(
    "status" => "ok",
    "code" => APIStatus::Done
));
