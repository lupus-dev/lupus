<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta un messaggio della chat
 */
class ChatMessage {

    /**
     * Identificativo del messagio
     * @var int
     */
    public $id_chat;

    /**
     * Identificativo della partita
     * @var int
     */
    public $id_game;

    /**
     * Identificativo dell'utente mittente
     * @var int
     */
    public $id_user_from;

    /**
     * Destinatario del messaggio. Se è destinato ad un gruppo dest=0
     * @var int
     */
    public $dest;

    /**
     * Gruppo di destinazione del messaggio
     * @var \ChatGroup
     */
    public $group;

    /**
     * Testo del messaggio
     * @var string
     */
    public $text;

    /**
     * Crea una istanza di \ChatMessage dai dati contenuti in una riga del database
     * @param array $data Vettore indicizzato per nome della colonna della tabella
     * chat del database contentente una riga della tabella
     * @return \ChatMessage Il messaggio presente nel database
     */
    public static function fromDatabase($data) {
        $message = new ChatMessage();
        $message->id_chat = $data["id_chat"];
        $message->id_game = $data["id_game"];
        $message->id_user_from = $data["id_user_from"];
        $message->dest = $data["dest"];
        $message->group = $data["group"];
        $message->text = $data["text"];

        return $message;
    }

    /**
     * Crea un vettore da usare come risposta per le API per mostrare un insieme 
     * di messaggi
     * @param array $messages Un vettore di \ChatMessage
     * @return array Ritorna un vettore di vettori con le informazioni dei messaggi
     */
    public static function makeResponseMultiple($messages) {
        // cache per gli username degli utenti
        $usernames = array();

        $res = array();

        if (!$messages)
            return array();
        
        foreach ($messages as $mex) {
            $id_mitt = $mex->id_user_from;
            // utilizza la cache per non ripetere le richieste al database
            if (isset($usernames[$id_mitt]))
                $username = $usernames[$id_mitt];
            else
                $username = $usernames[$id_mitt] = User::fromIdUser($id_mitt)->username;
            $group = ChatGroup::getChatName($mex->group);
            // se il messaggio è destinato ad un utente, ottiene l'username del
            // destinatario. Utilizza la cache degli username
            if ($mex->group == ChatGroup::User) {
                $id_dest = $mex->dest;
                if (isset($usernames[$id_dest]))
                    $dest = $usernames[$id_dest];
                else
                    $dest = $usernames[$id_dest] = User::fromIdUser($id_dest)->username;
            } else
                $dest = "";

            $res[] = array(
                "from" => $username,
                "group" => $group,
                "to" => $dest,
                "text" => $mex->text
            );
        }

        return $res;
    }

}
