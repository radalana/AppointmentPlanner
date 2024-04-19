<?php
//include("./models/appointment.php"); зачем здесь
require_once "Database.php";


class DataHandler
{
    private $db = new Database("localhost", "bif2webscriptinguser", "bif2021", "Appontments_planner");
    public function queryAppointments()
    {
        //здесь делает запрос у датабанка?
        if ($db->connect()) {
            $db->select('')
        }else{
            echo "There was some error connecting to the database.";
        }
        //return DataHandler::getDemoData(); //должен вернуть список объектов
    }

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
}
