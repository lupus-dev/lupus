<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per ottenere un autocompletamento per gli username degli utenti
 */

// l'utente deve essere connesso
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

if (!isset($_GET["q"]))
    response(400, array(
        "error" => "Specificare il parametro 'q'",
        "code" => APIStatus::MissingParameter));

$q = $_GET["q"];

$sql = "SELECT id_user, username
        FROM user
        WHERE username LIKE ? AND id_user != ? AND NOT EXISTS(
            SELECT * FROM room_acl WHERE id_room=? AND user.id_user=room_acl.id_user
        )
        ORDER BY CASE 
            WHEN username LIKE ? THEN 0
            WHEN username LIKE ? THEN 1
            WHEN username LIKE ? THEN 2
            ELSE 3
        END, username
        LIMIT 5";
$res = Database::query($sql, [ "%$q%", $room->id_admin, $room->id_room, "$q%", "%$q", "%$q%" ]);

$output = [];
foreach ($res as $u)
    $output[$u['id_user']] = $u['username'];

response(200, $res);
