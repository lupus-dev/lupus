lupus
=====

Lupus in Tabula!

## Setup

1. Scaricare il repository in una cartella del server web
2. Creare un database ed importare il file `specifiche/lupus.sql` per creare la struttura
3. Rinominare `config/example.config.ini` in `config/config.ini`
3. Configurare i parametri del server in `config/config.ini` e `js/default.js`
4. Scaricare le librerie aggiuntive con `composer install` 
5. Configurare il server web
    - Per apache: attivare `mod_rewrite` e se necessario usare .htaccess come esempio
    - Per nginx: usare l'esempio di configurazione `nginx_example.conf`
