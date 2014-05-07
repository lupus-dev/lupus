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
    // ---------- LOGIN ----------
    const LoginDone = 100;
    const LoginAlreadyDone = 101;
    const LoginMissingParameter = 102;
    const LoginFailed = 103;
    // ---------- LOGOUT ----------
    const LogoutDone = 110;
    const LogoutFailed = 111;
    // ---------- GAME ----------
    const GameFound = 120;
    const GameNotFound = 121;    
    // ---------- JOIN ----------
    const JoinDoneGameStarted = 130;
    const JoinDoneGameWaiting = 131;
    const JoinFailedAlreadyIn = 132;
    const JoinFailedGameClose = 133;
    const JoinFailedGameFull = 134;
    // ---------- NEW GAME ----------
    const NewGameDone = 140;
    const NewGameMissingParameter = 141;
    const NewGameAccessDenied = 142;
    const NewGameAlreadyRunning = 143;
    const NewGameAlreadyExists = 144;
    const NewGameNotEnouthPlayers = 145;
    const NewGameMalformed = 146;
    // ---------- START ----------
    const StartDone = 150;
    const StartAccessDenied = 151;
    const StartNotInSetup = 152;
    // ---------- VOTE ----------
    const VoteDoneNextDay = 160;
    const VoteDoneWaiting = 161;
    const VoteDoneGameEnd = 162;
    const VoteAccessDenied = 163;
    const VoteGameNotRunning = 164;
    const VoteMissingParameter = 165;
    const VoteNotValid = 166;
    const VoteNotNeeded = 167;
    // ---------- NEW ROOM ----------
    const NewRoomDone = 170;
    const NewRoomMissingParameter = 171;
    const NewRoomRoomsEnded = 172;
    const NewRoomPrivateRoomsEnded = 173;
    const NewRoomAlreadyExists = 174;
    const NewRoomMalformed = 175;
    // ---------- ROOM ----------
    const RoomFound = 180;
    const RoomNotFound = 181;
    const RoomAccessDenied = 182;
    // ---------- USER ----------
    const UserFound = 190;
    const UserNotFound = 191;
    // ---------- CHECK-ROOM-NAME ----------
    const CheckRoomNameAccepted = 200;
    const CheckRoomNameMissingParameter = 201;
    const CheckRoomNameMalformed = 202;
    const CheckRoomNameExisting = 203;
    // ---------- CHECK-ROOM-DESCR ----------
    const CheckRoomDescrAccepted = 210;
    const CheckRoomDescrMissingParameter = 211;
    const CheckRoomDescrMalformed = 212;
    // ---------- CHECK-GAME-NAME ----------
    const CheckGameNameAccepted = 220;
    const CheckGameNameMissingParameter = 221;
    const CheckGameNameMalformed = 222;
    const CheckGameNameNotFound = 223;
    const CheckGameNameExisting = 224;
    // ---------- CHECK-GAME-DESCR ----------
    const CheckGameDescrAccepted = 230;
    const CheckGameDescrMissingParameter = 231;
    const CheckGameDescrMalformed = 232;
    // ---------- SETUP-GAME ----------
    const SetupSuccess = 240;
    const SetupAccessDenied = 241;
    const SetupNotInSetup = 242;
    const SetupMissingParameter = 243;
    // ---------- SIGNUP ----------
    const SignupSuccess = 250;
    const SignupMissingParameter = 251;
    const SignupAlreadyExists = 252;
    const SignupMalformed = 253;
}