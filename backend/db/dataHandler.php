<?php
//require_once "Database.php";
include __DIR__ . '/Database.php'; // Подключение файла Database.php из той же директории, что и dataHandler.php
include __DIR__ . '/../models/appointment.php';

//реализовать prepared_statements!!!
class DataHandler
{
    private $db;

    public function __construct()
    {
        $this->db = new Database("localhost", "bif2webscriptinguser", "bif2021", "Appointments_planner");
    }

    // Abfrage aller Termine
    public function queryAppointments()
    {
        // Datenbankverbindung überprüfen
        if ($this->db->connect()) {
            // Spalten für die Abfrage festlegen
            $columns = 'appointments.id as id, title, description, creator, location, duration_min, expiration_date, created_at, option1, option2, option3, option4, option5';
            // Verknüpfungen für die Abfrage festlegen
            $joins = [['type' => 'INNER', 'table' => 'date_options', 'condition' => 'appointments.date_options_id=date_options.id']];
            // Termine abrufen
            $rows = $this->db->select('appointments', $columns, null, 'created_at DESC', $joins);
            // Fehlerbehandlung bei erfolgloser Abfrage
                if ($rows === false) {
                    echo "Error fetching data";
                    return;
                }
             // Ergebnis transformieren
            $result = array_map(function ($row) { 
            $dateOptions = [$row['option1'], $row['option2'], $row['option3'], $row['option4'], $row['option5']];
            $dateOptions = array_filter($dateOptions);
            // Benutzerstimmen abrufen
            $votionsResultList = $this->queryUserSelectionsWithNickname($row['id']);
                // Terminobjekt erstellen
                $details = new AppointmentsDetails($row['creator'], $row['description'], $row['location'], $row['duration_min'], $dateOptions);
                return new Appointment(
                    $row['id'],
                    $row['title'], 
                    $row['location'],
                    $row['duration'],
                    $details).setVotionsResult($votionsResultList);
            }, $rows);
        return $result;
    }else {
        // Fehlermeldung bei fehlgeschlagener Datenbankverbindung
        return false;
    } 
    }

    // Überprüfen, ob der Benutzername bereits vorhanden ist
    private function isUserNameTaken($nickname){
        if ($this->db->connect()) {
             // Benutzername in der Tabelle users suchen
            return $this->db->isExists('users', 'nickname', $nickname);
        }
        return;
    }

    // Benutzer-ID anhand des Benutzernamens abrufen
    private function queryUserIdByNickname($nickname) {
            //get
            if ($this->db->connect()) {
                // Benutzer-ID in der Tabelle users abrufen
               $rows = $this->db->select('users', 'id', "nickname='{$nickname}'");
                return $rows[0]['id'];
            }
            return false;
    }

    // Neuen Benutzer mit Benutzernamen hinzufügen
    private function putNewUserwithNickname($nickname) {
        if ($this->db->connect()) {
            // Neuen Benutzer in der Tabelle users einfügen
            return $this->db->insert('users', [$nickname], 'nickname');
        }
        return false;
    }

    // Benutzerstimme in die Datenbank eintragen
    public function sendVote($vote)//отправить голос в дб
    {
        // Daten aus dem Abstimmungsformular extrahieren
        ['appId' => $appId, 'options' => $userDateOptions, 'user' => $user, 'comment'=> $comment ]  = $vote;// z.b selectedOpt = [true, false, true, true, true]
        $user_id = null;
        // Überprüfen, ob der Benutzer bereits vorhanden ist
        if ($this->isUserNameTaken($user)) {
            // Benutzer-ID abrufen
            $user_id = $this->queryUserIdByNickname($user);
        }else {
            // Neuen Benutzer mit dem Benutzernamen erstellen
           if ($this->putNewUserwithNickname($user)){
             // Benutzer-ID abrufen
                $user_id = $this->queryUserIdByNickname($user);
           } else {
                throw new Exception("Benutzer existiert nicht und das Erstellen eines neuen Benutzers ist nicht möglich");
           }
           
        }
         // Datenbankverbindung überprüfen
        if ($this->db->connect()) {
             // Daten für die Benutzerstimme vorbereiten
            $rows = 'user_id, appointment_id, opt1_check, opt2_check, opt3_check, opt4_check, opt5_check, comment';// $opt3 - $op5 может и не быть
            $values = [$user_id, $appId, ...$userDateOptions, $comment];
            // Benutzerstimme in die Tabelle user_selections eintragen
            return $this->db->insert('user_selections', $values, $rows);
        }
        return false;
    }
    

     // Benutzerstimmen zu einem bestimmten Termin abrufen
    public function queryUserSelectionsWithNickname($appId)
    {   
        // Spalten und Bedingungen für die Abfrage festlegen
        $table = "user_selections";
        $rows = "users.nickname as user, user_selections.opt1_check as choice1, user_selections.opt2_check as choice2,
                user_selections.opt3_check as choice3, 
                user_selections.opt4_check as choice4, 
                user_selections.opt5_check as choice5,
                user_selections.comment as comment";
        $where = "appointment_id={$appId}";
        $order = "user_selections.created_at DESC";
        $joins = [['type' => 'INNER', 'table' => 'users', 'condition' => 'user_selections.user_id=users.id']];
        if ($this->db->connect()) {
            // Benutzerstimmen abrufen und transformieren
            $rows = $this->db->select($table, $rows, $where, $order, $joins);
            return array_map(function($row) {
                return [
                    'user' => $row['user'],
                    'choice1' =>$row['choice1'],
                    'choice2' =>$row['choice2'],
                    'choice3' =>$row['choice3'],
                    'choice4' =>$row['choice4'],
                    'choice5' =>$row['choice5']
                ];
                
            }, $rows);
         }
         return false;
    }
}
