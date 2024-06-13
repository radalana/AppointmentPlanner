<?php
use AppointmentsDetails;
class Appointment {
    private $id;
    private $title;
    private $location;
    private $duration;
    private $results; //array of Object?/assoc array with user, options

    public function __construct($id, $title, $location, $duration, AppointmentsDetails $details) {
      // инициировать другие поля при необходимости
      $this->id = $id;
      $this->title = $title;
      $this->location = $location;
      $this->duration = $duration;
      $this->details = $details;
  }

  public function setVotionsResult($results) {
    $this->results = $results;
  }
}
