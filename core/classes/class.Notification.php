<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

/**
 * Una notifica che un utente riceve
 */
class Notification {
    /**
     * Identificativo della notifica
     * @var integer
     */
    public $id_notification;
    /**
     * Identificativo dell'utente che possiede la notifica
     * @var integer
     */
    public $id_user;
    /**
     * Data e ora della notifica
     * @var DateTime
     */
    public $date;
    /**
     * Testo del messaggio
     * @var string
     */
    public $message;
    /**
     * Eventuale link della notifica
     * @var string|null
     */
    public $link;
    /**
     * Indica se la notifica è stata nascosta dall'utente
     * @var boolean
     */
    public $hidden;


    /**
     * Costruisce una notifica basandosi su una riga del database
     * @param array $row Ennupla dalla tabella notifcation
     */
    private function __construct($row) {
        $this->id_notification = $row['id_notification'];
        $this->id_user = $row['id_user'];
        $this->date = new DateTime($row['date']);
        $this->message = $row['message'];
        $this->link = $row['link'];
        $this->hidden = (boolean)$row['hidden'];
    }

    /**
     * Ritorna la notifica con l'id specificato
     * @param int $id_notification Id della notifica da cercare
     * @return Notification|false La notifica oppure false se si verifica un errore
     */
    public static function fromId($id_notification) {
        $sql = "SELECT * FROM notification WHERE id_notification = ?";
        $res = Database::query($sql, [$id_notification]);

        if (!$res) return false;

        return new Notification($res[0]);
    }

    /**
     * Aggiunge una notifica all'utente
     * @param User $user Utente a cui appartiene la notifica
     * @param string $message Messaggio della notifica
     * @param string $link Collegamento della notifica, può essere null
     * @return Notification|false Ritorna la notifica appena creata, false in caso di errore
     */
    public static function addNotification($user, $message, $link) {
        $sql = "INSERT INTO notification (id_user, message, link, seen, hidden) VALUES (?, ?, ?, 0, 0)";
        $res = Database::query($sql, [$user->id_user, $message, $link]);

        if (!$res) return false;

        $id_notification = Database::lastInsertId();
        return Notification::fromId($id_notification);
    }

    /**
     * Ottiene le ultime notifiche dell'utente che non sono state cancellate
     * @param User $user Utente che possiede le notifiche
     * @param DateTime $since Ottenere le notifiche posteriori a questa data
     * @param bool $includeHidden Indica se includere anche le notifiche nascoste
     * @param int $limit Numero di notifiche da restituire
     * @return array|false Ultime notifiche oppure false in caso di errore
     */
    public static function getLastNotifications($user, $since = null, $includeHidden = false, $limit = 5) {
        if (!$since)
            $since = new DateTime('1970-01-01');

        $sql = "SELECT * 
                FROM notification 
                WHERE id_user=? AND hidden<=? AND date>?
                ORDER BY `date` DESC
                LIMIT ?";
        $res = Database::query($sql, [$user->id_user, $includeHidden?1:0, $since->format("Y-m-d H:i:s"), $limit]);

        if ($res === false) return false;

        $notifications = [];

        foreach ($res as $n)
            $notifications[] = new Notification($n);

        return $notifications;
    }

    /**
     * Nasconde una notifica marcandola come 'hidden'
     * @return bool True se l'operazione ha avuto successo, false altrimenti
     */
    public function hide() {
        $sql = "UPDATE notification SET hidden=1 WHERE id_notification=?";
        $res = Database::query($sql, [$this->id_notification]);

        if (!$res) return false;

        $this->hidden = true;
        return true;
    }
}
