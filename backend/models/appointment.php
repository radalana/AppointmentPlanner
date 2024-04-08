<?php
class Appointment {
    public $id;
    public $titel;
    public $description;
    public $location;
    public $duration;
    public $appOptons = [];

    function __construct($id, $titel, $descr, $loc, $duration) {
        $this->id = $id;
        $this->titel = $titel;
        $this->description=$descr;
        $this->location=$loc;
        $this->duration=$duration;
      }
}
