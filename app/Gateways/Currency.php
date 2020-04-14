<?php

namespace App\Gateways;

use App\User;
use GuzzleHttp\Client;

class Currency
{
    public function changeCurrency($amount = 0, $to = 'EUR', $from = ''):  int{
      /****************** CHANGE TO AUTH USER*/
      $from = $from != ''? $from : User::find(1)->currency;
      $client = new Client([
                    'base_uri'        => 'https://www.amdoren.com/api/currency.php?api_key=fViqy8bwBqQvWiXbR6z62rce2c5zkJ&from='.$from.'&to='.$to.'&amount='.$amount,
                    'timeout'         => 0,
                    'allow_redirects' => false
                   //  'proxy'           => '192.168.16.1:10'
      ]);
      $req = $client->request('GET')->getBody()->getContents();
      return json_decode($req, true)['amount'];         
    }
    public function currencyType($currency){
        $json = json_decode(file_get_contents(base_path('resources/json/currency_types.json')), true);
        try {
            return $json[$currency];
          } catch (\Throwable $th) {
            return null;
          }
    }
}
