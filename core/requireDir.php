<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Esegue un require_once ricorsivo su tutti i file della directory indicata
 * @param string $dir La direcory da includere
 */
function requireDir($dir) {
    $d = scandir($dir);
    foreach ($d as $file) {
        if ($file == "." || $file == "..") continue;
        if (is_dir("$dir/$file")) 
            requireDir ("$dir/$file");
        else
            require_once "$dir/$file";
    }
}