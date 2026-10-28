<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rezultatai: {{ $poll->title }}</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; }
        .option { margin-bottom: 16px; }
        .bar-bg { background: #e5e7eb; border-radius: 6px; overflow: hidden; height: 14px; }
        .bar { background: #4f46e5; height: 100%; }
        .meta { color: #555; font-size: 14px; }
    </style>
</head>
<body>
    <h1>{{ $poll->title }}</h1>

    @if (session('error'))
        <p style="color:#b91c1c">{{ session('error') }}</p>
    @endif

    <p class="meta">Iš viso balsų: {{ $total }}</p>

    @foreach ($options as $option)
        @php
            $percent = $total > 0 ? round($option->votes_count / $total * 100) : 0;
        @endphp

        <div class="option">
            <div>
                <strong>{{ $option->text }}</strong>
                @if (isset($selectedOptionId) && $option->id == $selectedOptionId)
                    <span class="meta">(tavo balsas)</span>
                @endif
            </div>
            <div class="bar-bg">
                <div class="bar" style="width: {{ $percent }}%"></div>
            </div>
            <div class="meta">{{ $option->votes_count }} balsai ({{ $percent }}%)</div>
        </div>
    @endforeach

    <p><a href="{{ route('polls.show', $poll) }}">← Grįžti prie apklausos</a></p>
</body>
</html>