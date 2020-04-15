<?php

namespace App\Http\Controllers;

use App\Gateways\Currency;
use App\Gateways\TemporalAuth;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class BotController extends Controller
{
    protected $guest_chats = [];
    protected $auth;
    //dd(unserialize(Crypt::decryptString($this->auth)));
    public function __construct()
    {   
      if (session()->has('guest_chats')) {
        // session()->forget('guest_chats');
        $this->guest_chats = session('guest_chats');
      }
      $this->auth = new TemporalAuth();
    }

    public function index(){
      if (session()->has('guest_chats')) {
        foreach ($this->guest_chats as $g) {
          session()->push('guest_chats', $g);
        }
      }else{
        session()->put('guest_chats', []);
      }
      return view('welcome', [
                              'gest_chats' => session('guest_chats'),
                              'name' => Crypt::encryptString($this->auth->name),
                              'password' => Crypt::encryptString($this->auth->password),
                              'current_action' => Crypt::encryptString($this->auth->current_action)]);
    }
    public function hear(){
        $user = new User;
        if(request()['question'] != null && !Auth::guest()) {
            $user->find(Auth::id())->chats()->create(
                [
                'message'  => request()['question'], 
                'from_bot' => 0
                ]
            );

        }else{
            array_push($this->guest_chats, [
                'message'  => request()['question'], 
                'from_bot' => 0
                ]);
            session(['gest_chats' => $this->guest_chats]);    
        }
        $this->ask($user, request());
        return $this->index();
    }
    public function ask(User $user, $request){
        $question = $request['question'];
        $json = json_decode(file_get_contents(base_path('resources/json/qa.json')), true);
        $question = $question!= null ? $question: 'nothing';
        try {
          $index = array_rand($json[$question]);
          if (Auth::check()) {
            $user = $user->find(Auth::id());
            $user->chats()->create(
              [
              'message'  => $json[$question][$index],
              'from_bot' => 1
              ]
            );
          }else{
            array_push($this->guest_chats, [
              'message'  => $json[$question][$index],
              'from_bot' => 1
              ]);
            session(['gest_chats' => $this->guest_chats]);    
          }
        } catch (\Throwable $th) {
            $this->analizeQuestion($request);
        }
      }
      public function analizeQuestion($request){
        $response = '';
        $question = $request['question'];
        $str_arr = explode(" ", $question);
        switch ($str_arr[0]) {
          case 'convert':
            if(Auth::check()){
              $response = $this->convert($str_arr);
            }else{
              $response = 'You are not logged in yet!';
            }
            break;
          case 'change':
            if(Auth::check()){
              $response = $this->change(Auth::user(), $str_arr);
              break;  
            }
            $response = 'You are not logged in yet!';
            break;
          case 'register':
            if(Auth::check()){
              $response = 'Your logged in, try logging out tiping "logout" before a new registration!';
            } else {
              $this->auth->current_action = 'register';
              array_push($this->guest_chats, [
                  'message'  => 'Prefect!, now tipe your user name :-)', 
                  'from_bot' => 1
                  ]); 
              return;
            } 
            break;
          case 'account':
            if(Auth::check()){
              $response = $this->accountTransactions(Auth::user(), $str_arr);
            }else{
              $response = 'You are not logged in yet!';
            }
            break;
          case 'login':
            if(Auth::check()){
              $response = 'Your logged in, try logging out tiping "logout" before a new registration!';
            } else {
              $this->auth->current_action = 'login';
              array_push($this->guest_chats, [
                'message'  => 'Prefect!, now tipe your user name :-)', 
                'from_bot' => 1
                ]);  
              return;
              break; 
            }
          case 'logout':
            if(Auth::check()){
              User::find(Auth::id())->chats()->truncate();
              Auth::logout();
              session()->put('guest_chats', []);
              $response = 'You have successfully logged out!';
            }else{
              $response = 'You are not logged in yet!';
            }
            array_push($this->guest_chats, [
              'message'  => $response,
              'from_bot' => 1
            ]);
            return;
            break;    
          default:
            switch (Crypt::decryptString($request->get('current_action'))) {
                case 'register':
                    $this->auth->current_action = 'register';
                    array_push($this->guest_chats, [
                        'message'  => $this->manage_req($request), 
                        'from_bot' => 1
                        ]);
                    return;
                break;
                case 'login':
                  $this->auth->current_action = 'login';
                  array_push($this->guest_chats, [
                    'message'  => $this->manage_req($request), 
                    'from_bot' => 1
                    ]);
                return;
                break; 
                default:
                    $response = 'I don\'t understand, sorry :-(';
                break;
            }
            break;
        }
        if(Auth::check()){
          $user = User::find(Auth::id());
          $user->chats()->create(
              [
              'message'  => $response,
              'from_bot' => 1
              ]
          );
        }else{
          array_push($this->guest_chats, [
            'message'  => $response, 
            'from_bot' => 1
            ]);
        }
      }
    
      public function convert($str_arr = []){
        $val = 0;
        $currency_converter = new Currency();

        if(!is_double($str_arr[1]) && doubleval($str_arr[1]) < 0){
          return 'Invalid number! Try again!';
        }
        switch (count($str_arr)) {
          case 2:
            $val = $currency_converter->changeCurrency($str_arr[1]);
            return $val.' EUR';
            break;
          case 4:
            if( $str_arr[2] == 'to' 
                && $currency_converter->currencyType(strtoupper($str_arr[3])) != null){
              $val = $currency_converter->changeCurrency($str_arr[1], strtoupper($str_arr[3]));
              return $val.' '.strtoupper($str_arr[3]);
            }
          break;
          case 5:
            if( $currency_converter->currencyType(strtoupper($str_arr[2])) != null 
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
        return 'I don\'t understand, sorry :-(';
      }
    
      public function change(User $user, $str_arr = []){
        $valid = ['currency', 'name', 'password'];
        $funds = $user->funds;
        if(count($str_arr) == 4 && $str_arr[2] == 'to' && in_array($str_arr[1], $valid)){
          try {
            if($str_arr[1]  == 'currency'){
              $currency_converter = new Currency();
              if ($currency_converter->currencyType(strtoupper($str_arr[3])) == null) {
                return 'Currency '.$str_arr[3].' do not exists! Try again! :-(';
              }else{
                $funds = $currency_converter->changeCurrency($funds, $str_arr[3], $user->currency);
              }
            }
            if($str_arr[1]  == 'name' && (preg_match("/^[a-zA-Z'-]+$/", $str_arr[3]) == 0 || strlen($str_arr[3]) > 20)){
              return 'The name given is not valid! Try again! :-(';
            }
            $user->update([
              'funds' => $funds,
              $str_arr[1] => $str_arr[3]
            ]);
            $user->save();
            return 'Your '.$str_arr[1].' changed to '.$str_arr[3];
          } catch (\Throwable $th) {
            dd($th);
            return 'I don\'t understand, sorry :-(';
          }
        }
        return 'I don\'t understand, sorry :-(';
      }
      private function manage_req($request){
        $question = $request['question'];
        $messages = [
                'name.required'     => 'Sorry!, your name is required :-(',
                'name.unique'       => 'Sorry that name is taken! :-(',
                'name.exists'       => 'Sorry that name is do not exists, register first! :-(',
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
        if($this->auth->current_action == 'register'){
          $validated_data = Validator::make($request->all(), [
            'name'     =>  ['required', 'unique:users', 'max:45'],
            'password' =>  'required'
          ], $messages);
        }else{
          $validated_data = Validator::make($request->all(), [
            'name'     =>  ['required', 'exists:users', 'max:45'],
            'password' =>  'required'
          ], $messages);
        }
        if ($validated_data->errors()) {
            if ($validated_data->errors()->get('name')) {
                $this->auth = new TemporalAuth('', '', $this->auth->current_action);
                return $validated_data->errors()->get('name')[0];
            }
            else if ($request->name == $question) {
                $this->auth = new TemporalAuth($request->name, $request->password, $this->auth->current_action);
                return 'Nice name!, now you password!';
            }
            else if ($validated_data->errors()->get('password')) {
                $this->auth = new TemporalAuth($request->name, '', $this->auth->current_action);
                return $validated_data->errors()->get('password')[0];
            }
        }
        $this->auth = new TemporalAuth();
        if($request->current_action == 'register'){
            $user = $this->register($request);
            Auth::attempt([ 'name' => $request->name, 'password' => $request->password], true);
            session()->put('guest_chats', []);
            return 'Welcome '.$request->name.' thanks for signing up to our page! Enjoy! :-)';
        }else if($request->current_action == 'login'){
            $logged = Auth::attempt([ 'name' => $request->name, 'password' => $request->password], true);
            if ($logged) {
              session()->put('guest_chats', []);
              return 'Welcome again '.$request->name.'!';
            }else{
              return 'Your password must be worng '.$request->name.'!, Try again!';
            }
        }
      }
      public function register($request){
        User::create([
            'name' => $request->name, 
            'password' => Hash::make($request->password)
        ]);
      }
      public function cleanRequest($request){
        $request->request->set('name', Crypt::encryptString(''));
        $request->request->set('password', Crypt::encryptString(''));
        $request->request->set('current_action', Crypt::encryptString(''));
        return $request;
      }
      public function accountTransactions(User $user, $str_arr = []){
        $response = 'I don\'t understand, sorry :-(';
        $currency_converter = new Currency();
        if (isset($str_arr[2]) && (is_double($str_arr[2]) || is_numeric($str_arr[2]))) {
          switch ($str_arr[1]) {
            case 'deposit':
              if (isset($str_arr[3]) && $currency_converter->currencyType(strtoupper($str_arr[3])) != null) {
                $user->funds = $user->funds + $currency_converter->changeCurrency($str_arr[2], $user->currency , $str_arr[3]);
              }else if(isset($str_arr[3]) && $currency_converter->currencyType(strtoupper($str_arr[3])) == null){
                $response = 'Currency '.$str_arr[3].' do not exists! Try again! :-(';
                break;
              }else{
                $user->funds = $user->funds + $str_arr[2]; 
              }
              $user->save();
              $user->transactions()->create([
                'type' => $str_arr[1], 
                'amount' => $str_arr[2], 
                'currency' => isset($str_arr[3]) ? $str_arr[3] : $user->currency, 
                'balance' => $user->funds
              ]);
              $response = 'You deposit '.$str_arr[2].(isset($str_arr[3]) ? ' '.$str_arr[3]: '').
                ' to your account. Now you have '.$user->funds.' '.$user->currency;
              break;
            case 'withdraw':
              if (isset($str_arr[3]) && $currency_converter->currencyType(strtoupper($str_arr[3])) != null) {
                $user->funds = $user->funds - $currency_converter->changeCurrency($str_arr[2], $user->currency , $str_arr[3]);              
              }else if(isset($str_arr[3]) && $currency_converter->currencyType(strtoupper($str_arr[3])) == null){
                $response = 'Currency '.$str_arr[3].' do not exists! Try again! :-(';
                break;
              }else{
                $user->funds = $user->funds - $str_arr[2]; 
              }
              if ($user->funds > 0) {
                $user->save();
                $user->transactions()->create([
                  'type' => $str_arr[1], 
                  'amount' => $str_arr[2], 
                  'currency' => isset($str_arr[3]) ? $str_arr[3] : $user->currency, 
                  'balance' => $user->funds
                ]);
                $response = 'You withdraw '.$str_arr[2].(isset($str_arr[3]) ? ' '.$str_arr[3]: '').
                ' from your account. Now you have '.$user->funds.' '.$user->currency;
              }else{
                $response = 'You don\'t have enought money in your account ';
              }
              break;
            default:
              break;
          }
        }else if($str_arr[1] == 'balance'){
          $response = 'You account has '.$user->funds.' '.$user->currency;
        }
        return $response;
      }
}
