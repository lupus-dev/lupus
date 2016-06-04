<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per nascondere una notifica
 */
if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

if (!isset($_POST["id_notification"]))
    response(400, array(
        "error" => "Specificare il parametro id_notification",
        "code" => APIStatus::MissingParameter));

$id_notification = $_POST["id_notification"];
$notification = Notification::fromId($id_notification);

if (!$notification)
    response(404, array(
        "error" => "Notifica non trovata",
        "code" => APIStatus::NotFound));

if ($notification->id_user != $user->id_user)
    response(403, array(
        "error" => "L'utente non ha i permessi per nascondere la notifica",
        "code" => APIStatus::AccessDenied));

$res = $notification->hide();

if (!$res)
    response(500, array(
        "error" => "Errore nel nascondere la notifica",
        "code" => APIStatus::FatalError));

response(200, array(
    "status" => "Notifica nascosta",
    "code" => APIStatus::Done
));
