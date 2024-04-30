<?php
class Appointment {
    public $id;
    public $title;
    public $descr;
    public $location;
    public $duration;

    public $creator;
    public $dateOptions = [];
    public $expireDate;

    public $created_at;
    function __construct($id, $title, $descr, $creator, $loc, $duration, $expireDate, $created_at, $dateOptions) {
        $this->id = $id;
        $this->title = $title;
        $this->descr=$descr;
        $this->creator = $creator;
        $this->location=$loc;
        $this->duration=$duration;
        $this->expireDate = $expireDate;
        $this->created_at = $created_at;
        $this->dateOptions = $dateOptions; 
      }
}
