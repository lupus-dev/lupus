<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per creare una nuova partita
 */
if (!$login)
    response(401, array("error" => "Utente non connesso"));
if (!isset($_GET["descr"]) || !isset($_GET["roles"]))
    response(400, array("error" => "Specificare una descrizione e l'elenco dei ruoli"));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$game_descr = $_GET["descr"];
$roles = $_GET["roles"];
if (is_string($roles)) $roles = json_decode ($roles, true);
if (is_object($roles)) $roles = (array)$roles;

if (!is_array($roles))
    response (400, array("error" => "I ruoli non sono in un formato corretto"));

$room = Room::fromRoomName($room_name);
if (!$room)
    response (404, array("error" => "La stanza cercata non esiste"));
if ($room->id_admin != $user->id_user)
    response (401, array("error" => "La stanza non appartiene all'utente"));

if (!$room->isAllTerminated())
    response (401, array("error" => "C'è ancora una partita in corso in questa stanza"));

// aggiungere controllo sui ruoli

$existGame = Game::fromRoomGameName($room_name, $game_name);
if ($existGame)
    response (409, array("error" => "Esiste già una partita in questa stanza di nome '$game_name'"));

$res = Game::createGame($room_name, $game_name, $game_descr, $roles);

if (!$res)
    response (500, array("error" => "Non è stato possibile creare la partita"));

response(201, Game::makeResponse($res, $user));