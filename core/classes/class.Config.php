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
    
    public static $db_host = "localhost";
    public static $db_user = "root";
    public static $db_password = "password";
    public static $db_port = 3306;
    public static $db_database = "lupus";

    public static $log_level = 3;
    public static $log_path = "log/log.txt";
    
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

        Config::$db_host = $config["database"]["host"];
        Config::$db_user = $config["database"]["username"];
        Config::$db_password = $config["database"]["password"];
        Config::$db_port = $config["database"]["port"];
        Config::$db_database = $config["database"]["database"];
        
        Config::$log_level = $config["log"]["level"];
    }

    
    /**
     * Il costruttore è privato perchè è una classe statica
     */
    private function __construct() {
        
    }
}
