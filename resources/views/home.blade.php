@extends('layouts.app')

@section('content')
    @forelse ($polls as $poll)
        @php
            $total = $poll->options->sum('votes');
            $hasVoted = in_array($poll->id, session('voted_polls', []));
        @endphp

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $poll->title }}</h5>

                @if ($hasVoted)
                    @foreach ($poll->options as $option)
                        @php $percent = $total ? round($option->votes / $total * 100) : 0; @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>{{ $option->text }}</span>
                                <span>{{ $option->votes }} ({{ $percent }}%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <small class="text-muted">Iš viso balsų: {{ $total }}</small>
                @else
                    <form method="POST" action="{{ route('polls.vote', $poll) }}">
                        @csrf
                        @foreach ($poll->options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option_id"
                                       id="option{{ $option->id }}" value="{{ $option->id }}" required>
                                <label class="form-check-label" for="option{{ $option->id }}">
                                    {{ $option->text }}
                                </label>
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary mt-3">Balsuoti</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Apklausų dar nėra.</p>
    @endforelse
@endsection