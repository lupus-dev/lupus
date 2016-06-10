<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

class LogLevel {
    /**
     * Un errore grave è accaduto
     */
    const Error = 0;
    /**
     * Un errore serio è accaduto, potrebbe non essere successo nulla di grave
     */
    const Warning = 1;
    /**
     * Qualcosa di strano è successo ma è tutto ok
     */
    const Notice = 2;
    /**
     * Informazioni utili per il debug
     */
    const Debug = 3;
    /**
     * Moooolte informazioni
     */
    const Verbose = 4;
    
    public static $levels = array(
        0 => "Error  ",
        1 => "Warning",
        2 => "Notice ",
        3 => "Debug  ",
        4 => "Verbose"
    );
}

/**
 * Effettua il log di un'evento se il livello di logging lo permette
 * @param string $event Evento da loggare
 * @param \LogLevel $level Livello di log richiesto
 */
function logEvent($event, $level = LogLevel::Debug) {
    if ($level > Config::$log_level)
        return;
    
    $date = date("Y-m-d H:i:s");
    $level = LogLevel::$levels[$level];

    if (!is_string($event))
        $event = var_export($event, true);

    $message = "[$date][$level] $event\n";
    
    $root_base = __DIR__ . "/../../";
    
    $file = fopen($root_base . Config::$log_path, "a+");
    
    fwrite($file, $message);
    
    fclose($file);
}

function getErrorType($errno) {
    switch($errno) {
        case E_ERROR:               return "Error";
        case E_WARNING:             return "Warning";
        case E_PARSE:               return "Parse Error";
        case E_NOTICE:              return "Notice";
        case E_CORE_ERROR:          return "Core Error";
        case E_CORE_WARNING:        return "Core Warning";
        case E_COMPILE_ERROR:       return "Compile Error";
        case E_COMPILE_WARNING:     return "Compile Warning";
        case E_USER_ERROR:          return "User Error";
        case E_USER_WARNING:        return "User Warning";
        case E_USER_NOTICE:         return "User Notice";
        case E_STRICT:              return "Strict Notice";
        case E_RECOVERABLE_ERROR:   return "Recoverable Error";
        default:                    return "Unknown error ($errno)";
    }
}

function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
    logEvent(getErrorType($errno) . ": " . $errstr, LogLevel::Error);
    logEvent("On file: " . $errfile . " line " . $errline, LogLevel::Error);
    logEvent("Error context: " . json_encode($errcontext, JSON_PRETTY_PRINT), LogLevel::Error);
}

function exception_handler($ex) {
    logEvent("Exception: " . $ex->getMessage(), LogLevel::Error);
    logEvent("On file {$ex->getFile()} line {$ex->getLine()}", LogLevel::Error);
    foreach (explode("\n", $ex->getTraceAsString()) as $line)
        logEvent("    " . $line, LogLevel::Error);

    echo "<b>Exception</b>: " . $ex->getMessage() . "<br>";
    echo "On file {$ex->getFile()} line {$ex->getLine()}" . "<br>";
    echo "Stacktrace:<br>";
    foreach (explode("\n", $ex->getTraceAsString()) as $line)
        echo "    " . $line . "<br>";
}

/**
 * Inizializza l'handler custom per gli errori e le eccezioni
 */
function initErrorHandler() {
    $root_base = __DIR__ . "/../../";
    $log_path = $root_base . Config::$log_path;

    if (!is_writable($log_path))
        return;

    set_error_handler("error_handler");
    set_exception_handler("exception_handler");
}
