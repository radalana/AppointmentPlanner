<?php
require __DIR__ . '/../db/dataHandler.php';

class SimpleLogic
{
    private $dh;
    
    function __construct()
    {
        $this->dh = new DataHandler();
    }

    function handleRequest($method, $param="")
    {
        switch ($method) {
            case "queryAppointments": //queryAppointmentslist
                //return "appointments";
                return $this->dh->queryAppointments();//вернуть массив объектов
            default:
                null;
        }
    }
}
