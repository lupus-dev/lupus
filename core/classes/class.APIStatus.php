<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che continene i codici di errore/successo utilizzati dalle API
 */
class APIStatus {
    // ---------- COMMON ----------
    const NotLoggedIn = 1;
    const FatalError = 2;
    const AccessDenied = 3;
    const NotFound = 4;
    const MissingParameter = 5;
    const MalformedParameter = 6;
    const Done = 7;
    const Fail = 8;
    const Found = 9;
    // ---------- LOGIN ----------
    const LoginAlreadyDone = 101;
    // ---------- JOIN ----------
    const JoinDoneGameStarted = 130;
    const JoinDoneGameWaiting = 131;
    const JoinFailedAlreadyIn = 132;
    const JoinFailedGameClose = 133;
    const JoinFailedGameFull = 134;
    const JoinFailedGamesEnded = 135;
    // ---------- NEW GAME ----------
    const NewGameAlreadyRunning = 143;
    const NewGameAlreadyExists = 144;
    // ---------- START ----------
    const StartNotInSetup = 152;
    const StartWithoutLupus = 153;
    // ---------- VOTE ----------
    const VoteDoneNextDay = 160;
    const VoteDoneWaiting = 161;
    const VoteDoneGameEnd = 162;
    const VoteGameNotRunning = 164;
    const VoteNotValid = 166;
    const VoteNotNeeded = 167;
    // ---------- NEW ROOM ----------
    const NewRoomRoomsEnded = 172;
    const NewRoomPrivateRoomsEnded = 173;
    const NewRoomAlreadyExists = 174;
    // ---------- CHECK-ROOM-NAME ----------
    const CheckRoomNameAccepted = 200;
    const CheckRoomNameExisting = 203;
    // ---------- CHECK-ROOM-DESCR ----------
    const CheckRoomDescrAccepted = 210;
    // ---------- CHECK-GAME-NAME ----------
    const CheckGameNameAccepted = 220;
    const CheckGameNameExisting = 224;
    // ---------- CHECK-GAME-DESCR ----------
    const CheckGameDescrAccepted = 230;
    // ---------- SETUP-GAME ----------
    const SetupNotInSetup = 242;
    // ---------- SIGNUP ----------
    const SignupAlreadyExists = 252;
    // ---------- CHAT ----------
    const ChatUserNotInGame = 263;
    const ChatInvalidUser = 264;
    // ---------- TERM GAME ----------
    const GameTerminated = 270;
    const GameTermNotRunning = 272;
    // ---------- KICK PLAYER ----------
    const PlayerKickNotValidState = 282;
    // ---------- ACL -----------
    const ACLAlreadyPresent = 290;
    const ACLCannotRemoveAdmin = 291;
}
