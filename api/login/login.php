<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

if (!isset($_GET["username"]) || !isset($_GET["password"]))
    response (400, array("error" => "Specificare username e password"));

$username = $_GET["username"];
$password = $_GET["password"];

$id_user = User::checkLogin($username, $password);

if (!$id_user)
    response (401, array("error" => "Nome utente/password errati"));

$_SESSION["id_user"] = $id_user;

response(202, array("ok" => "Login effettuato"));