<?php

namespace App\Http\Controllers;

use App\Models\Poll;

class HomeController extends Controller
{
    public function index()
    {
        $polls = Poll::with('options')->latest()->get();
        return view('home', compact('polls'));
    }
}