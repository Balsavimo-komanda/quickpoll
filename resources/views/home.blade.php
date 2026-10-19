<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QuickPoll</title>
</head>
<body>
    <h1>QuickPoll</h1>
    <a href="{{ route('polls.create') }}">Nauja apklausa</a>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @forelse ($polls as $poll)
        @php
            $total = $poll->options->sum('votes');
            $hasVoted = in_array($poll->id, session('voted_polls', []));
        @endphp

        <div>
            <h2>{{ $poll->title }}</h2>

            @if ($hasVoted)
                @foreach ($poll->options as $option)
                    @php $percent = $total ? round($option->votes / $total * 100) : 0; @endphp
                    <p>{{ $option->text }}: {{ $option->votes }} ({{ $percent }}%)</p>
                @endforeach
                <small>Iš viso balsų: {{ $total }}</small>
            @else
                <form method="POST" action="{{ route('polls.vote', $poll) }}">
                    @csrf
                    @foreach ($poll->options as $option)
                        <label>
                            <input type="radio" name="option_id" value="{{ $option->id }}" required>
                            {{ $option->text }}
                        </label><br>
                    @endforeach
                    <button type="submit">Balsuoti</button>
                </form>
            @endif
        </div>
    @empty
        <p>Apklausų dar nėra.</p>
    @endforelse
</body>
</html>