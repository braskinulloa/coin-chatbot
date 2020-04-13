<?php

namespace App\Http\Controllers;

use App\Gateways\Currency;
use App\Gateways\TemporalAuth;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BotController extends Controller
{
    protected $gest_chats = [];
    protected $auth;
    //dd(unserialize(Crypt::decryptString($this->auth)));
    public function __construct()
    {   
        $this->gest_chats = session()->has('gest_chats') ? session('gest_chats') : $this->gest_chats;
        $this->auth = new TemporalAuth();
    }

    public function index(){
        dump(Auth::check());
        dump(request()->session()->all());
        request()->session()->push('gest_chats', session()->has('gest_chats') ? $this->gest_chats : []);
        $user = Auth::check() ? Auth::user() : null;
        return view('welcome', ['user' => $user,
                                'gest_chats' => $this->gest_chats,
                                'name' => Crypt::encryptString($this->auth->name),
                                'password' => Crypt::encryptString($this->auth->password),
                                'current_action' => Crypt::encryptString($this->auth->current_action)]);
    }
    public function hear(User $user){

        if(request()['question'] != null && !Auth::guest()) {
            $user->chats()->create(
                [
                'message'  => request()['question'], 
                'from_bot' => 0
                ]
            );

        }else{
            array_push($this->gest_chats, [
                'message'  => request()['question'], 
                'from_bot' => 0
                ]);
            session(['gest_chats' => $this->gest_chats]);    
        }
        $this->ask($user, request());
        return $this->index();
    }
    public function ask(User $user, $request){
        // if (Auth::guest()) {
        //     $this->analizeQuestion($user, $request);
        // }
        $question = $request['question'];
        $json = json_decode(file_get_contents(base_path('resources/json/qa.json')), true);
        $question = $question!= null ? $question: 'nothing';
        try {
          $index = array_rand($json[$question]);
          $user->chats()->create(
            [
            'message'  => $json[$question][$index],
            'from_bot' => 1
            ]
          );
        } catch (\Throwable $th) {
            $this->analizeQuestion($user, $request);
        }
      }
      public function analizeQuestion(User $user, $request){
        $response = '';
        $question = $request['question'];
        $str_arr = explode(" ", $question);
        switch ($str_arr[0]) {
          case 'convert':
            $response = $this->convert($str_arr);
            break;
          case 'change':
            $response = $this->change($str_arr);
            break;  
          case 'register':
            $this->auth->current_action = 'register';
            // $response = 'Prefect!, now tipe your user name :-)';
            array_push($this->gest_chats, [
                'message'  => 'Prefect!, now tipe your user name :-)', 
                'from_bot' => 1
                ]);  
            return;
            break;  
          default:
            switch (Crypt::decryptString($request->get('current_action'))) {
                case 'register':
                    // $response = $this->register_req($request);
                    array_push($this->gest_chats, [
                        'message'  => $this->register_req($request), 
                        'from_bot' => 1
                        ]);
                    return;
                break;
                case 'login':
                    $response = $this->register_req($request);
                break;
                default:
                    $response = 'I don\'t understand, sorry :-(';
                break;
            }
            break;
        }
        $user->chats()->create(
            [
            'message'  => $response,
            'from_bot' => 1
            ]
        );
      }
    
      public function convert($str_arr = []){
        $val = 0;
        $currency_converter = new Currency();
        switch (count($str_arr)) {
          case 2:
            if(is_numeric($str_arr[1]) && floatval($str_arr[1])>0){
              $val = $currency_converter->changeCurrency($str_arr[1]);
              return $val.' EUR';
            }
            break;
          case 4:
            if(is_numeric($str_arr[1] 
                && floatval($str_arr[1])>0) 
                && $str_arr[2] == 'to' 
                && $currency_converter->currencyType(strtoupper($str_arr[3])) != null){
              $val = $currency_converter->changeCurrency($str_arr[1], strtoupper($str_arr[3]));
              return $val.' '.strtoupper($str_arr[3]);
            }
          break;
          case 5:
            if( is_numeric($str_arr[1] 
                && floatval($str_arr[1])>0) 
                && $currency_converter->currencyType(strtoupper($str_arr[2])) != null 
                && $str_arr[3] == 'to' 
                && $currency_converter->currencyType(strtoupper($str_arr[4])) != null){
              $val = $currency_converter->changeCurrency($str_arr[1], strtoupper($str_arr[4]), strtoupper($str_arr[2]));
              return $val.' '.strtoupper($str_arr[4]);
            }
            break;
          default:
            return 'I don\'t understand, sorry :-(';
            break;
        }
        if(!is_numeric($str_arr[1]) && floatval($str_arr[1]) < 0){
          return 'Invalid number! Try again!';
        }
        return 'I don\'t understand, sorry :-(';
      }
    
      public function change($str_arr = []){
        $valid = ['currency', 'name', 'password'];
        if(count($str_arr) == 4 && $str_arr[2] == 'to' && in_array($str_arr[1], $valid)){
          try {
            if($str_arr[1]  == 'currency'){
              $currency_converter = new Currency();
              if ($currency_converter->currencyType(strtoupper($str_arr[3])) == null) {
                return 'Currency '.$str_arr[3].' do not exists! Try again! :-(';
              }
            }
            if($str_arr[1]  == 'name' && (preg_match("/^[a-zA-Z'-]+$/", $str_arr[3]) == 0 || strlen($str_arr[3]) > 20)){
              return 'The name given is not valid! Try again! :-(';
            }
            $this->user->update([$str_arr[1] => $str_arr[3]]);
            $this->user->save();
            return 'Your '.$str_arr[1].' changed to '.$str_arr[3];
          } catch (\Throwable $th) {
            dd($th);
            return 'I don\'t understand, sorry :-(';
          }
        }
        return 'I don\'t understand, sorry :-(';
      }
      private function register_req($request){
        $question = $request['question'];
        session(['name' => $question ]);
        $messages = [
                'name.required'     => 'Sorry!, your name is required :-(',
                'name.unique:users'       => 'Sorry that name is taken! :-(',
                'name.max'          => 'Sorry that name is to large! :-(',
                'password.required'           => 'Sorry! a password is required! :-(',
        ];
        $request->request->set('name',Crypt::decryptString($request->name));
        $request->request->set('password',Crypt::decryptString($request->password));
        $request->request->set('current_action',Crypt::decryptString($request->current_action));
        if($request->current_action && !$request->name){
            $request->request->set('name', $question);
        }
        else if($request->current_action && $request->name && !$request->password){
            $request->request->set('password', $question);
        }
        
        $validated_data = Validator::make($request->all(), [
          'name'     =>  ['required', 'unique:users', 'max:45'],
          'password' =>  'required'
        ], $messages);
        dump($request->all());
        if ($validated_data->errors()) {
            if ($validated_data->errors()->get('name')) {
                $this->auth = new TemporalAuth('', '', 'register');
                return $validated_data->errors()->get('name')[0];
            }
            else if ($request->name == $question) {
                $this->auth = new TemporalAuth($request->name, $request->password, 'register');
                return 'Nice name!, now choose you password!';
            }
            else if ($validated_data->errors()->get('password')) {
                $this->auth = new TemporalAuth($request->name, '', 'register');
                return $validated_data->errors()->get('password')[0];
            }

        }
        $this->auth = new TemporalAuth();
        $user = $this->register($request);
        Auth::attempt(['name' => $user->name, 'password' => $user->password]);
        return 'Welcome '.$request->name.' thanks for signing up to our page! Enjoy! :-)';

      }
      public function register($request){
        $user = User::create([
            'name' => $request->name, 
            'password' => Hash::make($request->password)
        ]);
        return $user;    
      }
      public function cleanRequest($request){
        $request->request->set('name', Crypt::encryptString(''));
        $request->request->set('password', Crypt::encryptString(''));
        $request->request->set('current_action', Crypt::encryptString(''));
        return $request;
      }
}
