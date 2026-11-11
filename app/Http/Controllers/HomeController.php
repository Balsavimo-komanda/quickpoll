<?php

namespace App\Http\Controllers;

use App\Models\Poll;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('home', ['polls' => Poll::latest()->get()]);
    }
}
