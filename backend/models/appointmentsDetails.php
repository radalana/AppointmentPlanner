<?php
use Creator;
class AppointmentsDetails{
    protected Creator $creator;
    protected $description;
    protected $dateOptions = [];
    protected $expireDate;
    protected $created_at;

    public function __construct(Creator $creator, $description,$dateOptions, $expireDate, $created_at) {
        $this->creator = $creator;
        $this->dateOptions = $dateOptions;
        $this->description = $description;
        $this->expireDate = $expireDate;
        $this->created_at = $created_at;
    }

    public function getDetails() {
        return [
            'creator' => $this->creator,
            'dateOptions' => $this->dateOptions,
            'expireDate' => $this->expireDate,
            'created_at' => $this->created_at,
            'description' => $this->description,
        ];
    }
}