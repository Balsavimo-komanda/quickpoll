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
                $request->session()->put('poll_visitors.'.$poll->id, $request->visitor_id);

                return redirect()->route('polls.results', $poll)->with('error', 'Jūs jau balsavote šioje apklausoje!');
            }

            throw $e;
        }

        $request->session()->put('poll_visitors.'.$poll->id, $request->visitor_id);
        $request->session()->put('poll_votes.'.$poll->id, $request->poll_option_id);

        return redirect()->route('polls.results', $poll);
    }

    public function results(Request $request, Poll $poll): View|RedirectResponse
    {
        $visitorId = $request->session()->get('poll_visitors.'.$poll->id);

        $hasVoted = $visitorId && Vote::where('poll_id', $poll->id)
            ->where('visitor_id', $visitorId)
            ->exists();

        if (!$hasVoted) {
            return redirect()->route('polls.show', $poll)
                ->with('error', 'Rezultatus galėsite matyti po balsavimo.');
        }

        $options = PollOption::where('poll_id', $poll->id)->withCount('votes')->get();
        $total = $options->sum('votes_count');
        $selectedOptionId = $request->session()->get('poll_votes.'.$poll->id);

        return view('polls.results', compact('poll', 'options', 'total', 'selectedOptionId'));
    }

    public function joinForm(): View
    {
        return view('polls.join');
    }

    public function join(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|string|max:6|alpha_num',
        ]);

        $poll = Poll::where('code', strtoupper(trim($data['code'])))->first();

        if (!$poll) {
            return back()->withInput()->with('error', 'Apklausa su tokiu kodu nerasta.');
        }

        return redirect()->route('polls.show', $poll);
    }

    public function joinByCode(string $code): RedirectResponse
    {
        $poll = Poll::where('code', strtoupper($code))->firstOrFail();

        return redirect()->route('polls.show', $poll);
    }
}
