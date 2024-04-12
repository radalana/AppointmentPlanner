<?php
class Appointment {
    public $id;
    public $titel;
    public $description;
    public $location;
    public $duration;

    public $creator;
    public $appOptons = [];

    function __construct($id, $titel, $descr, $creator, $loc, $duration) {
        $this->id = $id;
        $this->titel = $titel;
        $this->description=$descr;
        $this->creator = $creator;
        $this->location=$loc;
        $this->duration=$duration;
      }
}
