<?php

use App\Guide;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            'login' => 'Just type "login" and follow my instructions',
            'register' => 'Just type "register" and follow my instructions',
            'convert' => 'Convert a currency value to another currency, if expecified
                Examples: 
                ( convert 100 -> convert 100 dolars to euros )
                ( convert 100 to CAD -> convert 100 dolars to canadian dolars )
                ( convert 100 EUR to USD -> convert 100 euros to dolars )',
            'change' => 'Change profile parameters [\'currency\', \'name\', \'password\'] 
            Example: change name to Pedro',
            'account' => 'Change account parameters [\'deposit\', \'withdraw\', \'balance\'] 
            Example: account deposit 100 USD -> deposit the amount to your account, in case that your account has different currency, first 
            converts the amount to your current currency'
        ];
        foreach($values as $key => $value){
            Guide::create([
                'title' => $key,
                'description' => $value
            ]);
        }
    }
}
