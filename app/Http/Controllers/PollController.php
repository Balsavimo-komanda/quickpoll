<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;

class PollController extends Controller
{
    public function create(): View
    {
        return view('polls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255'
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

    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'poll_option_id' => 'required|exists:poll_options,id',
            'visitor_id' => 'required|string|max:255',
        ]);

        $option = PollOption::where('id', $request->poll_option_id)->where('poll_id', $poll->id)->first();

        if (!$option) {
            return back()->with('error', 'Pasirinkta netinkamas atsakymo variantas.');
        }

        try {
            Vote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $request->poll_option_id,
                'visitor_id' => $request->visitor_id,
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062 || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                return back()->with('error', 'Jūs jau balsavote šioje apklausoje!');
            }

            throw $e;
        }

        return redirect()->route('polls.show', $poll->id)->with('status', 'Jūsų balsas sėkmingai pateiktas!');
    }
}
