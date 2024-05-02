<?php
require __DIR__ . '/../db/dataHandler.php';

class SimpleLogic
{
    private $dh;
    
    function __construct()
    {
        $this->dh = new DataHandler();
    }
    /* not here, but for now, remove static*/
    function validate($userSelection) {
        $errors = [];
        return $errors;
    }
    function format($userSelection) {
        $result = [];
        $result["user"] = htmlspecialchars(stripcslashes(trim($userSelection["user"])));
        $result["comment"] = htmlspecialchars(stripcslashes(trim($userSelection["comment"])));
        //$userName = $result["user"];
        $result["appId"] = $userSelection["appId"];
        $options = [];
        for($i = 0; $i < 5; $i++) {
            if (array_key_exists($i, $userSelection)) {
                $options[] = 1;
            }else {
                $options[] = 0;
            }
        }
        $result["options"] = $options;
        return $result;
    }
    
    function handleRequest($method, $userSelection="")
    {
        switch ($method) {
            case "queryAppointments": //queryAppointmentslist
                //return "appointments";
                return $this->dh->queryAppointments();//вернуть массив объектов
            case "sendVote"://еще trim здесь?
                $formatedUserSelection = $this->format($userSelection);
                $errors = $this->validate($formatedUserSelection);
                if (count($errors) == 0) {
                    return $this->dh->sendVote($formatedUserSelection); //$param - assoc array (в худшем случае объект)
                } 
                return false; //or null?
            default:
                null;
        }
    }
}
 