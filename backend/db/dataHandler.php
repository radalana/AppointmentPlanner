<?php
include("./models/appointment.php");
class DataHandler
{
    public function queryPersons()
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

    private static function getDemoData()
    {
        return [ new Person(1, "Jane", "Doe", "jane.doe@fhtw.at", 1234567, "Central IT"),
                new Person(2, "John", "Doe", "john.doe@fhtw.at", 34345654, "Help Desk"),
                new Person(3, "baby", "Doe", "baby.doe@fhtw.at", 54545455, "Management"),
                new Person(4, "Mike", "Smith", "mike.smith@fhtw.at", 343477778, "Faculty") ];
    }
}
