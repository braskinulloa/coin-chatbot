<?php

namespace App\Gateways;

class Bot {
  public function ask($question = 'nothing'){
    $json = json_decode(file_get_contents(base_path('resources/json/qa.json')), true);
    $index = array_rand($json[$question]);
    return $json[$question][$index];
  }
}