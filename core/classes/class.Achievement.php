<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

abstract class Achievement {

    /**
     * Nome identificativo dell'obiettivo. Deve essere lo stesso che compone il
     * nome del file: achievement.Xxxxx.php
     * @var string
     */
    public static $achievement_name = "";

    /**
     * Nome dell'obiettivo, visulizzato come titolo
     * @var string
     */
    public static $name = "";

    /**
     * Descrizione dell'obiettivo
     * @var string
     */
    public static $description = "";

    /**
     * Indica se l'obiettivo è attivo oppure no, è utile per nascondere degli
     * obiettivi o per fare dell'ereditarietà
     * @var bool
     */
    public static $enabled = true;

    /**
     * Indice di difficoltà dell'achievement
     * @var int
     */
    public static $difficulty = 0;

    /**
     * Verifica se l'utente può sbloccare l'obiettivo
     * @param User $user Utente da controllare
     * @return bool True se l'utente può sbloccare l'obiettivo, false altrimenti
     */
    public abstract function canObtain($user);

    /**
     * Dati aggiuntivi per gli obiettivi
     * @var array
     */
    protected $data;

    /**
     * Costruisce un'istanza di un Achievement generale
     * @param array $data Dati aggiuntivi per l'obiettivo
     */
    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * Verifica se l'utente può sbloccare l'obiettivo, se si allora lo sblocca
     * @param User $user Utente da controllare
     * @return bool True se l'utente ha sbloccato l'obiettivo, false altriementi
     */
    public function checkObtaining($user) {
        if ($this->hasUnlocked($user))
            return false;
        if ($this->canObtain($user)) {
            if ($this->unlock($user)) {
                $achievement_name = $this->getAchievementName();
                Notification::addNotification($user,
                    "<b>Complimenti!</b> Hai sbloccato l'obiettivo <code>" . $achievement_name::$name . "</code>",
                    "/user");
            }
        }
        return false;
    }

    /**
     * Verifica se l'utente ha già sbloccato l'obiettivo
     * @param User $user Utente da controllare
     * @return bool True se l'utente ha già sbloccato l'obiettivo
     */
    private function hasUnlocked($user) {
        $sql = "SELECT * FROM achievements WHERE id_user = ? AND achievement_name = ?";
        $res = Database::query($sql, [$user->id_user, $this->getAchievementName()]);
        if (!$res || count($res) == 0) return false;
        return true;
    }

    /**
     * Sblocca l'obiettivo all'utente
     * @param User $user Utente che ha sbloccato l'obiettivo
     * @return bool True se l'operazione ha avuto successo, false altrimenti
     */
    private function unlock($user) {
        $sql = "INSERT INTO achievements (id_user, achievement_name) VALUES (?,?)";
        $res = Database::query($sql, [ $user->id_user, $this->getAchievementName() ]);
        if (!$res) return false;

        logEvent("L'utente $user->username ha sbloccato l'obiettivo " . $this->getAchievementName(), LogLevel::Debug);

        return true;
    }

    /**
     * Esegue la rivalutazione di tutti gli obiettivi che rispettano le caratteristiche indicate
     * @param User $user Utente da controllare
     * @param array $data Dati aggiuntivi da passare alla classe obiettivo
     * @param array $only Elenco delle sole classi obiettivo da filtrare. È possibile passare il nome
     * delle classi da verificare ma anche delle classi padre. Vettore vuoto annulla il filtraggio
     */
    public static function triggerCompleteCheck($user, $data = [], $only = []) {
        $achievements = Achievement::getAllAchievements();

        foreach ($achievements as $achievement) {
            if (count($only) == 0 ||
                    in_array($achievement, $only) ||
                    array_intersect($only, class_parents($achievement))) {
                $a = new $achievement($data);
                $a->checkObtaining($user);
            }
        }
    }

    /**
     * Ottiene una lista degli obiettivi sbloccati dall'utente
     * @param User $user Utente da controllare
     * @return bool|array False se si verifica un errore, un array di obiettivi altrimenti
     */
    public static function getUserAchievements($user) {
        $sql = "SELECT * FROM achievements WHERE id_user=?";
        $res = Database::query($sql, [$user->id_user]);
        if ($res === false) return false;

        $achievements = [];

        foreach ($res as $a) {
            $achievement_name = $a["achievement_name"];
            $achievements[$achievement_name] = $a["unlock_date"];
        }

        return $achievements;
    }

    public static function getAchievementInfo($achievement_name) {
        return [
            "achievement_name" => $achievement_name,
            "name" => $achievement_name::$name,
            "description" => $achievement_name::$description
        ];
    }

    /**
     * Ritorna il nome dell'Achievement dell'istanza
     * @return string
     */
    protected function getAchievementName() {
        $class_name = get_class($this);
        return $class_name::$achievement_name;
    }

    /**
     * Ottiene un elenco dei nomi di tutti gli obiettivi
     * @return boolean|array Ritorna un vettore di string con i nomi degli obiettivi
     * False se si verifica un errore
     */
    public static function getAllAchievements() {
        // i ruoli sono nella cartella /core/classes/roles
        $dir = __DIR__ . "/achievements/";

        $files = scandir($dir);
        if (!$files) {
            logEvent("La cartella degli obiettivi non è accessibile", LogLevel::Error);
            return false;
        }
        $achievements = array();

        foreach ($files as $file) {
            $matches = array();
            // seleziona dalla cartella solo i file che rispettano il formato corretto
            if (preg_match("/achievement\.([a-zA-Z0-9]+)\.php/", $file, $matches)) {
                $achievement = $matches[1];
                if (class_exists($achievement) && in_array("Achievement", class_parents($achievement))) {
                    if ($achievement::$enabled == true)
                        $achievements[] = $matches[1];
                } else
                    logEvent("Nella cartella degli achievement c'è un file che non codifica un obiettivo valido ($file)", LogLevel::Notice);
            } else if (!startsWith($file, "."))
                logEvent("Nella cartella degli obiettivi è presente un file ($file) con nome non valido", LogLevel::Notice);
        }

        usort($achievements, function ($a, $b) {
            return $a::$difficulty > $b::$difficulty ? 1 : -1;
        });
        return $achievements;
    }
}
