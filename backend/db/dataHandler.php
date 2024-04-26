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
        $this->db = new Database("localhost", "bif2webscriptinguser", "bif2021", "Appontments_planner");
    }
    public function queryAppointments()
    {
        if ($this->db->connect()) {
        $columns = 'title, description, creator, location_id as loc, duration_min, expiration_date, created_at, option1, option2, option3, option4, option5';
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
        echo ("Error fetching data from the database.");
    }
        
    }
    /*
    public function queryPersonById($id)//отправить голос в дб
    {
        $result = [];
        foreach ($this->queryPersons() as $val) {
            if ($val->id == $id) {
                $result[] = $val;
            }
        }
        return $result;
    }

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
