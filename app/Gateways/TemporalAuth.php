<?php 

namespace App\Gateways;

class TemporalAuth {
    public $name, $password, $current_action;
    public function __construct($name = '', $password = '', $current_action = '')
    {
        $this->name = $name;
        $this->password = $password; 
        $this->current_action = $current_action;
    }
}