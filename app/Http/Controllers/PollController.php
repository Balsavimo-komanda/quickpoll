<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'options'   => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $poll = Poll::create(['title' => $data['title']]);

        foreach ($data['options'] as $text) {
            $poll->options()->create(['text' => $text]);
        }

        return redirect()->route('home')->with('status', 'Apklausa sukurta!');
    }
}