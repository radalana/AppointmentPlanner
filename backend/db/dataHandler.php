<?php
//require_once "Database.php";
include __DIR__ . '/Database.php'; // Подключение файла Database.php из той же директории, что и dataHandler.php
include __DIR__ . '/../models/appointment.php';

class DataHandler
{
    private $db;

    public function __construct() {
        $this->db = new Database("localhost", "bif2webscriptinguser", "bif2021", "Appontments_planner");
    }
    public function queryAppointments()
    {
        //здесь делает запрос у датабанка?
        if ($this->db->connect()) {
            $rows = $this->db->select('appointments');
            $result = array_map(fn ($row) => new Appointment($row['id'], $row['title'], $row['description'], 
                                $row['creator'], $row['location'], $row['duration']), $rows);
            return $result;
            
        }else{
            echo "There was some error connecting to the database.";
        }
        //return DataHandler::getDemoData(); //должен вернуть список объектов
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
