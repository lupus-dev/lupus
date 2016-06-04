<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per rimuovere regola ACL alla stanza
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
        "error" => "Privilegi insufficienti per rimuovere una ACL",
        "code" => APIStatus::AccessDenied));

if (!isset($_POST["id_user"]))
    response(400, array(
        "error" => "Specificare il parametro id_user",
        "code" => APIStatus::MissingParameter));

$id_user = $_POST["id_user"];

$acl = User::fromIdUser($id_user);

if (!$acl)
    response(404, array(
        "error" => "L'utente non esiste",
        "code" => APIStatus::NotFound));

if ($acl->id_user == $room->id_admin)
    response(400, array(
        "error" => "Non è possibile rimuovere l'admin dalle ACL",
        "code" => APIStatus::ACLCannotRemoveAdmin
    ));

if (!in_array($acl->id_user, $room->getACLUsers(false)))
    response(400, array(
        "error" => "L'utente non è presente nelle ACL",
        "code" => APIStatus::NotFound));

$res = $room->removeACL($acl);

if (!$res)
    response(500, array(
        "error" => "Errore nel rimuovere l'ACL",
        "code" => APIStatus::FatalError));

response(201, array(
    "status" => "Utente rimosso dall'ACL",
    "code" => APIStatus::Done
));
