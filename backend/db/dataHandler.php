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
    public function queryAppointments()
    {
        if ($this->db->connect()) {
            $columns = 'appointments.id as id, title, description, creator, location_id as loc, duration_min, expiration_date, created_at, option1, option2, option3, option4, option5';
            $joins = [['type' => 'INNER', 'table' => 'date_options', 'condition' => 'appointments.date_options_id=date_options.id']];
            $rows = $this->db->select('appointments', $columns, null, 'created_at DESC', $joins);
                if ($rows === false) {
                    echo "Error fetching data";
                    return;
                }
            $result = array_map(function ($row) { 
            $dateOptions = [$row['option1'], $row['option2'], $row['option3'], $row['option4'], $row['option5']];
            $dateOptions = array_filter($dateOptions);
                return new Appointment(
                    $row['id'],
                    $row['title'],
                    $row['description'],
                    $row['creator'],
                    $row['loc'],
                    $row['duration'],
                    $row['expiration_date'],
                    $row['created_at'],
                    $dateOptions);
            }, $rows);
        return $result;
    }else {
        //echo "Error fetching data from the database.";
        return false;
    } 
    }
    private function isUserNameTaken($nickname){
        if ($this->db->connect()) {
            return $this->db->isExists('users', 'nickname', $nickname);
        }
        return;
    }
    private function queryUserIdByNickname($nickname) {
            //get
            if ($this->db->connect()) {
               $rows = $this->db->select('users', 'id', "nickname='{$nickname}'");
                return $rows[0]['id'];
            }
            return false;
    }
    private function putNewUserwithNickname($nickname) {
        if ($this->db->connect()) {
            return $this->db->insert('users', [$nickname], 'nickname');
        }
        return false;
    }
    public function sendVote($vote)//отправить голос в дб
    {
        ['appId' => $appId, 'options' => $userDateOptions, 'user' => $user, 'comment'=> $comment ]  = $vote;// z.b selectedOpt = [true, false, true, true, true]
        $user_id = null;
        if ($this->isUserNameTaken($user)) {
            $user_id = $this->queryUserIdByNickname($user);
        }else {
           if ($this->putNewUserwithNickname($user)){
                $user_id = $this->queryUserIdByNickname($user);
           } else {
                throw new Exception("User doesn'exist and impossible create new");
           }
           
        }
        
        if ($this->db->connect()) {
            //getUserId() or name is unique you dont need name;
            $rows = 'user_id, appointment_id, opt1_check, opt2_check, opt3_check, opt4_check, opt5_check, comment';// $opt3 - $op5 может и не быть
            $values = [$user_id, $appId, ...$userDateOptions, $comment]; // $opt3 - $op5 может и не быть
            return $this->db->insert('user_selections', $values, $rows);
        }
        return false;
    }
    /*
    public function queryPersonByName($name)//удалить аппоитмент
    {
        $result = [];
        foreach ($this->queryPersons() as $val) {
            if (stripos($val->lastname, $name) !== FALSE) {
                $result[] = $val;
            }
        }
        return $result;
    }
    */
}
