<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PollController extends Controller
{
    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $poll = Poll::create(['title' => $data['title']]);

        foreach ($data['options'] as $text) {
            $poll->options()->create(['text' => $text]);
        }

        return redirect()->route('polls.show', $poll)->with('status', 'Apklausa sukurta!');
    }

    public function show(Request $request, Poll $poll): View
    {
        $poll->load('options');

        return view('polls.show', [
            'poll' => $poll,
            'selectedOptionId' => $request->session()->get('poll_votes.'.$poll->id),
        ]);
    }

    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        $data = $request->validate([
            'option_id' => [
                'required',
                'integer',
                Rule::exists('poll_options', 'id')->where('poll_id', $poll->id),
            ],
        ], [
            'option_id.required' => 'Pasirinkite vieną atsakymo variantą.',
            'option_id.integer' => 'Pasirinkite vieną galiojantį atsakymo variantą.',
            'option_id.exists' => 'Pasirinktas atsakymo variantas nepriklauso šiai apklausai.',
        ]);

        $poll->options()->findOrFail($data['option_id'])->increment('votes');
        $request->session()->put('poll_votes.'.$poll->id, (int) $data['option_id']);

        return redirect()->route('polls.show', $poll)
            ->with('status', 'Jūsų balsas sėkmingai pateiktas!');
    }
}
