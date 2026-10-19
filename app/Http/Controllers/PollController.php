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
        ], [
        'title.required'     => 'Įveskite apklausos klausimą.',
        'options.required'   => 'Pridėkite atsakymo variantų.',
        'options.min'        => 'Reikia bent :min atsakymo variantų.',
        'options.*.required' => 'Atsakymo variantas negali būti tuščias.',
        'options.*.distinct' => 'Atsakymo variantai negali kartotis.',
    ]);

        $poll = Poll::create(['title' => $data['title']]);

        foreach ($data['options'] as $text) {
            $poll->options()->create(['text' => $text]);
        }

        return redirect()->route('home')->with('status', 'Apklausa sukurta!');
    }
    public function vote(Request $request, Poll $poll)
{
    $data = $request->validate([
        'option_id' => 'required|integer',
    ], [
        'option_id.required' => 'Pasirinkite atsakymą.',
    ]);

    // Only accept an option that belongs to this poll
    $option = $poll->options()->findOrFail($data['option_id']);

    // Simple "one vote per browser session" check
    $voted = session('voted_polls', []);
    if (in_array($poll->id, $voted)) {
        return back()->with('status', 'Jūs jau balsavote šioje apklausoje.');
    }

    $option->increment('votes');
    session(['voted_polls' => [...$voted, $poll->id]]);

    return back()->with('status', 'Ačiū už balsą!');
}
}