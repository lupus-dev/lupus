<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/*
 * API per effettuare una votazione nella parita
 */

if (!$login)
    response(401, array("error" => "Utente non connesso"));

$room_name = $apiMatches[1];
$game_name = $apiMatches[2];

$game = Game::fromRoomGameName($room_name, $game_name);

if (!$game)
    response(400, array("error" => "La partita $room_name/$game_name non esiste"));

if (!$game->inGame($user->id_user))
    response(401, array("error" => "Non fai parte di questa partita"));

$gameStatus = $game->status;
if ($gameStatus != GameStatus::Running)
    response (401, array("error" => "La partita non è in corso"));

if (!isset($_GET["vote"]))
    response(400, array("error" => "Specificare il voto"));

$vote = $_GET["vote"];

$engine = new Engine($game);

$role = Role::fromUser($user, $engine);

if (!$role)
    response(500, array("error" => "L'utente ha un ruolo non valido"));

$time = GameTime::fromDay($game->day);
switch ($time) {
    case GameTime::Day:
        // verifica se l'utente deve votare
        if ($role->needVoteDay()) {
            // controlla che il suo voto sia valido
            if (!$role->checkVoteDay($vote))
                response(400, array("error" => "Voto non valido"));
            else
            // verifica che la votazione sia riuscita
            if (!$role->vote($vote))
                response(500, array("error" => "Votazione non riuscita"));
        } else
            response(400, array("error" => "L'utente non deve votare"));
        break;
    case GameTime::Night:
        if ($role->needVoteNight()) {
            if (!$role->checkVoteNight($vote))
                response(400, array("error" => "Voto non valido"));
            else
            if (!$role->vote($vote))
                response(500, array("error" => "Votazione non riuscita"));
        } else
            response(400, array("error" => "L'utente non deve votare"));
        break;
    default:
        logEvent("Tempo non riconosciuto ({$engine->game->day} => $time)", LogLevel::Notice);
        response(500, array("error" => "Tempo di gioco non riconosciuto"));
        break;
}

// la votazione è riuscita, avvia il motore
$status = $engine->run();

// verifica lo stato della partita
if ($status == Engine::NextDay)
    response(201, array(
        "vote" => "Votazione effettuata",
        "game" => "Partita avanzata"
    ));
if ($status == Engine::NeedVote)
    response (201, array(
        "vote" => "Votazione effettuata",
        "game" => "Partita in attesa"
    ));
if ($status == Engine::EndGame)
    response (201, array(
        "vote" => "Votazione effettuata",
        "game" => "Partita terminata"
    ));
if ($status >= 500)
    response (500, array("error" => "Un errore del server ha interrotto la partita"));