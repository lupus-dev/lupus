<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * API per ottenere un elenco delle notifiche più recenti di una certa data
 */
if (!$login)
    response(401, array(
        "error" => "Utente non connesso",
        "code" => APIStatus::NotLoggedIn));

$since = isset($_GET["since"]) ? $_GET["since"] : null;
$includeHidden = isset($_GET["hidden"]) ? (boolean)$_GET["hidden"] : false;
$limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;

$date = null;
try {
    if ($since)
        $date = new DateTime($since);
} catch (Exception $ex) {
    response(400, array(
        "error" => "Parametro since in un formato errato",
        "details" => $ex->getMessage(),
        "code" => APIStatus::MalformedParameter));
}

$notifications = Notification::getLastNotifications($user, $date, $includeHidden, $limit);

if ($notifications === false)
    response(500, array(
        "error" => "Impossibile ottenere l'elenco delle notifiche",
        "code" => APIStatus::FatalError));

$res = [];

foreach ($notifications as $notification)
    $res[] = array(
        "id_notification" => $notification->id_notification,
        "date" => $notification->date->format("Y-m-d H:i:s"),
        "message" => $notification->message,
        "link" => $notification->link,
        "hidden" => $notification->hidden
    );

response(200, $res);
