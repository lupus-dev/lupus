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
