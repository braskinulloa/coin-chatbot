<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoricalController extends Controller
{
    public function index()
    {
        return view('historical');
    }
}
