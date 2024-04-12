<?php
include("db/dataHandler.php");

class SimpleLogic
{
    private $dh;
    
    function __construct()
    {
        $this->dh = new DataHandler();
    }

    function handleRequest($method, $param)
    {
        switch ($method) {
            case "queryPersons": //queryAppointmentslist
                return $this->dh->queryPersons();//queryAppointment?? или уже в queryAppointments
            case "queryPersonById":
                return $this->dh->queryPersonById($param);//delete appointment
            case "queryPersonByName":
                return $this->dh->queryPersonByName($param);//sent voice
            default:
                return null;
        }
    }
}
