<?php

/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Esegue un require_once ricorsivo su tutti i file della directory indicata.
 * L'ordine in cui vengono eseguiti i require è:
 *  - Prima tutti i file in ordine alfabetico
 *  - Poi tutte le cartelle ricorsivamente in ordine alfabetico
 * @param string $dir La direcory da includere
 */
function requireDir($dir) {
    $d = scandir($dir);
    $dirs = [];
    foreach ($d as $file) {
        if ($file == "." || $file == "..") continue;
        if (is_dir("$dir/$file"))
            $dirs[] = "$dir/$file";
        else
            require_once "$dir/$file";
    }

    foreach ($dirs as $dir)
        requireDir($dir);
}
