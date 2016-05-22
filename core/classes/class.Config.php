<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Contiene la configurazione del server
 */
class Config {
    
    public static $db_string = "mysql:host=localhost;dbname=lupus";
    public static $db_user = "root";
    public static $db_password = "password";

    public static $log_level = 3;
    public static $log_path = "log/log.txt";
    
    public static $webapp_base = "/lupus";
    
    public static $ini = "";


    /**
     * Load the configuration from an ini file
     * @param string $ini
     */
    static function loadConfig($ini = NULL) {
        if ($ini == NULL)
            $ini = __DIR__ . "/../../config/config.ini";

        $config = parse_ini_file($ini, true);
        Config::$ini = $config;

        Config::$db_string = $config["database"]["string"];
        Config::$db_user = $config["database"]["username"];
        Config::$db_password = $config["database"]["password"];

        Config::$log_level = $config["log"]["level"];
        
        Config::$webapp_base = $config["webapp"]["basedir"];
    }

    
    /**
     * Il costruttore è privato perchè è una classe statica
     */
    private function __construct() {
        
    }
}
