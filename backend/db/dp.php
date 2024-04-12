<?php
class Controller {
    private $host = "localhost";
    private $user = "bif2webscriptinguser";
    private $database = "appointment_finder";
    
    private $password = 'bifpass';
    public $con = null;

    public function __construct() {
        $this->con = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->con->connect_error) {
            echo "Connection Error: " . $this->con->connect_error;
            exit();
        }
        echo "Connenction successfull!" . $this->con->connect_error;
    }
}