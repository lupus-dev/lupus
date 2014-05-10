<?php

/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Classe che rappresenta la chat di una partita
 */
class Chat {

    /**
     * Ottiene tutti i messaggi dell'utente all'interno della partita
     * @param \Game $game Partita in cui cercare
     * @param \User $user Utente a cui appartiene la chat
     * @return array|boolean Ritorna un vettore di \ChatMessage. False se si verifica
     * un errore
     */
    public static function getAllMessages($game, $user) {
        $role = firstUpper(Role::getRole($user, $game));
        $chat_groups = $role::$chat_groups;

        $id_user = $user->id_user;
        $id_game = $game->id_game;
        $groups = implode(",", $chat_groups);

        // seleziona tutti i messaggi nei sui gruppi
        // e tutti i messaggi inviati da lui o destinati a lui
        $query = "SELECT id_chat,id_game,id_user_from,dest,`group`,text,timestamp FROM chat WHERE id_game=$id_game AND ("
                . "`group` IN ($groups) OR "
                . "(`group`=0 AND (id_user_from=$id_user OR dest=$id_user)))";
        $res = Database::query($query);

        if (!$res)
            return false;
        
        $messages = array();
        foreach ($res as $mex)
            $messages[] = ChatMessage::fromDatabase($mex);
        
        return $messages;
    }

    /**
     * Ottiene tutti i messaggi dell'utente all'interno della partita e di un 
     * gruppo
     * @param \Game $game Partita in cui cercare
     * @param \User $user Utente a cui appartiene la chat
     * @param \ChatGroup $group Gruppo con cui filtrare i messaggi
     * @return array|boolean Ritorna un vettore di \ChatMessage. False se si verifica
     * un errore
     */
    public static function getGroupMessage($game, $user, $group) {
        $id_user = $user->id_user;
        $id_game = $game->id_game;
        
        if ($group != ChatGroup::User) 
            $where = "`group`=$group";
        else
            $where = "`group`=$group AND (id_user_from=$id_user OR dest=$id_user)";

        $query = "SELECT id_chat,id_game,id_user_from,dest,`group`,text,timestamp FROM chat "
                . "WHERE id_game=$id_game AND ($where)";
        $res = Database::query($query);

        if (!$res)
            return false;
        
        $messages = array();
        foreach ($res as $mex)
            $messages[] = ChatMessage::fromDatabase($mex);
        
        return $messages;
    }
    
    /**
     * Invia un messaggio in chat
     * @param \Game $game Partita in cui inviare il messaggio
     * @param int $id_user Identificativo dell'utente mittente
     * @param int $dest Identificativo del destinatario (0 se il gruppo non è User)
     * @param \ChatGroup $group Gruppo in cui inviare il messaggio
     * @param string $text Il testo del messaggio
     * @return boolean True se l'operazione ha avuto successo, false altrimenti
     */
    public static function sendMessage($game, $id_user, $dest, $group, $text) {
        $id_game = $game->id_game;
        $text = Database::escape($text);
        
        $query = "INSERT INTO chat (id_game,id_user_from,dest,`group`,text) "
                . "VALUE ($id_game, $id_user, $dest, $group, '$text')";
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
    }
    
    /**
     * Ottiene il timestamp dell'ultimo messaggio inviato
     * @param \Game $game Partita a cui appartiene la chat
     * @param int $id_user Identificativo dell'utente a cui appartiene la chat
     * @param \ChatGroup $group Gruppo della chat
     * @param int $id_dest Identificativo del destinatario
     * @return int Il timestamp dell'ultimo messaggio
     */
    public static function getLastTimestamp($game, $id_user, $group, $id_dest = 0) {
        $id_game = $game->id_game;
        
        $where = ($group == ChatGroup::User) ? 
                "`group`=$group AND ((id_user_from=$id_user AND dest=$id_dest) OR (id_user_from=$id_dest AND dest=$id_user))" : 
                "`group`=$group";
        
        $query = "SELECT timestamp FROM chat WHERE id_game=$id_game AND "
                . "($where) ORDER BY timestamp DESC LIMIT 1";
        $res = Database::query($query);
        if (!$res)
            return 0;
        return strtotime($res[0]["timestamp"]);
    }
}
