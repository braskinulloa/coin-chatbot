<?php

namespace App\Gateways;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class Currency
{
    public function changeCurrency($amount = 0, $to = 'EUR', $from = ''):  int{
      $from = $from != ''? $from : User::find(Auth::id())->currency;
      $client = new Client([
                    'base_uri'        => 'https://www.amdoren.com/api/currency.php?api_key='.env('API_CONVERT_CURRENCY_KEY').'&from='.$from.'&to='.$to.'&amount='.$amount,
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
