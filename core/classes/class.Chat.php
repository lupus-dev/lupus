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
        $query = "SELECT id_chat,id_game,id_user_from,dest,`group`,text FROM chat WHERE id_game=$id_game AND ("
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
            $where = "`group`=0 AND (id_user_from=$id_user OR dest=$id_user)";

        $query = "SELECT id_chat,id_game,id_user_from,dest,`group`,text FROM chat "
                . "WHERE id_game=$id_game AND ($where)";
        $res = Database::query($query);

        if (!$res)
            return false;
        
        $messages = array();
        foreach ($res as $mex)
            $messages[] = ChatMessage::fromDatabase($mex);
        
        return $messages;
    }
    
}
