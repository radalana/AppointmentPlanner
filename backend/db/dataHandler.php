<?php
include("./models/appointment.php");
class DataHandler
{
    public function queryAppointments()
    {
        return DataHandler::getDemoData();
    }

    public function queryPersonById($id)
    {
        $result = [];
        foreach ($this->queryPersons() as $val) {
            if ($val->id == $id) {
                $result[] = $val;
            }
        }
        return $result;
    }

    public function queryPersonByName($name)
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
