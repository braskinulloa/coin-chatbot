<?php

namespace App\Gateways;

use GuzzleHttp\Client;

class Currency
{
    public function changeCurrency($from = 'USD', $to = 'EUR', $amount = 0) {
        $client = new Client([
                     'base_uri'        => 'https://www.amdoren.com/api/currency.php?api_key=fViqy8bwBqQvWiXbR6z62rce2c5zkJ&from='.$from.'&to='.$to.'&amount='.$amount,
                     'timeout'         => 0,
                     'allow_redirects' => false
                    //  'proxy'           => '192.168.16.1:10'
                 ]);
        $req = $client->request('GET')->getBody()->getContents();
        return json_decode($req, true)['amount'];         
    }
}
