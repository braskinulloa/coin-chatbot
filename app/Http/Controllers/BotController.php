<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Gateways\Bot;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{
    public function hear($text = '')
    {
        $title = 'welcome';
        $bot = new Bot();
        $bot->ask();
        $user = User::find(1);
        // $user_chats = Chat::all();

        return view('welcome', compact('user'));
    }
}
