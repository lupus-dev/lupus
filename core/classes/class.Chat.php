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
     * @param int $dest Altro utente se la chat è privata
     * @return array|boolean Ritorna un vettore di \ChatMessage. False se si verifica
     * un errore
     */
    public static function getGroupMessage($game, $user, $group, $dest = 0) {
        $id_user = $user->id_user;
        $id_game = $game->id_game;

        if ($group != ChatGroup::User)
            $where = "`group`=$group";
        else
            $where = "`group`=$group AND ((id_user_from=$id_user AND dest=$dest) OR (id_user_from=$dest AND dest=$id_user))";

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
        $text = Database::escape(trim($text));
        if (!$text)
            return true;

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

    /**
     * Conta quanti messaggi sono presenti e sono successivi ad una certa data
     * @param \Game $game Partita a cui appartiene la chat
     * @param int $id_user Identificativo dell'utente a cui appartiene la chat
     * @param \ChatGroup $group Gruppo della chat
     * @param int $timestamp Timestamp precedente a tutti i messaggi da contare
     * @param int $id_dest Identificativo del destinatario
     * @return int Il numero di messaggi successivi a $timestamp
     */
    public static function getNumAfterTimestamp($game, $id_user, $group, $timestamp, $id_dest = 0) {
        $id_game = $game->id_game;

        $where = ($group == ChatGroup::User) ?
                "`group`=$group AND ((id_user_from=$id_user AND dest=$id_dest) OR (id_user_from=$id_dest AND dest=$id_user))" :
                "`group`=$group";

        $query = "SELECT COUNT(*) AS new FROM chat WHERE id_game=$id_game AND "
                . "($where) AND timestamp>FROM_UNIXTIME($timestamp)";
        $res = Database::query($query);
        if (!$res)
            return 0;
        return $res[0]["new"];
    }

    /**
     * Inizializza le informazioni sulla chat di un utente 
     * @param \Game $game Partita a cui appartiene la chat
     * @param \User $user Utente a cui appartiene la chat
     * @return boolean|array False se l'utente ho fa parte della partita, i 
     * dati creati altrimenti
     */
    private static function initializeChatInfo($game, $user) {
        $role_name = firstUpper(Role::getRole($user, $game));
        if (!$role_name)
            return false;
        $groups = $role_name::$chat_groups;
        $data = array(
            "groups" => array(),
            "users" => array()
        );
        
        foreach ($groups as $group)
            $data["groups"][$group] = 0;
        foreach ($game->getPlayers() as $player) {
            $user = User::fromUsername($player);
            $data["users"][$user->id_user] = 0;
        }
        
        $res = Chat::setUserChatInfo($game, $user, $data);
        if (!$res)
            return false;
        return $data;
    }

    /**
     * Ottiene le informazioni sugli ultimi update della chat di un utente
     * @param \Game $game Partita a cui appartiene la chat
     * @param \User $user Utente a cui appartiene la chat
     * @return boolean|array False se si verifica un errore. Un vettore con le 
     * informazioni sulla chat di un utente altrimenti
     */
    public static function getUserChatInfo($game, $user) {
        $id_game = $game->id_game;
        $id_user = $user->id_user;

        $query = "SELECT chat_info FROM role WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res)
            return false;

        $data = json_decode($res[0]["chat_info"], true);
        if (!$data)
            $data = Chat::initializeChatInfo($game, $user);
        return $data;
    }

    /**
     * Salva il vettore con le informazioni sulla chat di un utente
     * @param \Game $game Partita a cui appartiene la chat
     * @param \User $user Utente a cui appartiene la chat
     * @param array $data Dati della chat da salvare
     * @return boolean True se l'operazione ha avuto successo. False altrimenti
     */
    public static function setUserChatInfo($game, $user, $data) {
        $id_game = $game->id_game;
        $id_user = $user->id_user;
        
        $data = Database::escape(json_encode($data));

        $query = "UPDATE role SET chat_info='$data' WHERE id_game=$id_game AND id_user=$id_user";
        $res = Database::query($query);
        if (!$res)
            return false;
        return true;
    }

}
