<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per aggiungere una nuova regola ACL alla stanza
 */
if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$room_name = $apiMatches[1];
$room = Room::fromRoomName($room_name);

if (!$room)
    response (404, array(
        "error" => "Stanza non trovata",
        "code" => APIStatus::NotFound));

if ($room->id_admin != $user->id_user)
    response(403, array(
        "error" => "Privilegi insufficienti per aggiungere una ACL",
        "code" => APIStatus::AccessDenied));

if (!isset($_POST["username"]))
    response(400, array(
        "error" => "Specificare il parametro username",
        "code" => APIStatus::MissingParameter));

$username = $_POST["username"];

$acl = User::fromUsername($username);

if (!$acl)
    response(404, array(
        "error" => "L'utente non esiste",
        "code" => APIStatus::NotFound));

if (in_array($acl->id_user, $room->getACLUsers(true)))
    response(400, array(
        "error" => "L'utente è già presente nell'ACL",
        "code" => APIStatus::ACLAlreadyPresent));

$res = $room->addACL($acl);

if (!$res)
    response(500, array(
        "error" => "Errore nell'inserire l'ACL",
        "code" => APIStatus::FatalError));

response(201, array(
    "status" => "Utente aggiunto all'ACL",
    "user" => array(
        "id_user" => $acl->id_user,
        "username" => $acl->username,
        "level" => $acl->level,
        "level-name" => Level::getLevel($acl->level)->name
    ),
    "code" => APIStatus::Done
));
