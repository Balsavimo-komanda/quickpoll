<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $poll->title }} – QuickPoll</title>
    <style>
        body { margin: 0; background: #f3f6fa; color: #172234; font: 1rem/1.5 system-ui, sans-serif; }
        main { max-width: 640px; margin: 48px auto; padding: 28px; background: white; border-radius: 16px; }
        h1 { line-height: 1.25; overflow-wrap: anywhere; }
        fieldset { border: 0; padding: 0; margin: 24px 0; }
        legend { margin-bottom: 12px; }
        .option { display: flex; align-items: center; gap: 12px; padding: 16px; margin: 10px 0; border: 2px solid #cbd5e1; border-radius: 10px; cursor: pointer; overflow-wrap: anywhere; }
        .option:has(input:checked) { border-color: #1d4ed8; background: #eff6ff; font-weight: 600; }
        .option:focus-within { outline: 2px solid #1d4ed8; outline-offset: 3px; }
        input { accent-color: #1d4ed8; flex-shrink: 0; }
        button { background: #1d4ed8; color: white; padding: 12px 24px; border: 0; border-radius: 8px; font: inherit; cursor: pointer; }
        button:disabled { background: #64748b; cursor: not-allowed; }
        .status { padding: 12px; background: #ecfdf5; color: #166534; border-radius: 8px; }
        .error { color: #b91c1c; }
        @media (max-width: 700px) { main { margin: 16px; padding: 20px; } }
    </style>
</head>
<body>
<main>
    <a href="{{ route('home') }}">← Visos apklausos</a>
    <h1>{{ $poll->title }}</h1>

    @if (session('status'))
        <p class="status" role="status">{{ session('status') }}</p>
    @endif

    @if ($selectedOption = $poll->options->firstWhere('id', $selectedOptionId))
        <p>Jūsų pateiktas pasirinkimas: <strong>{{ $selectedOption->text }}</strong></p>
    @endif

    <form method="POST" action="{{ route('polls.vote', $poll) }}">
        @csrf
        <fieldset @if ($errors->has('option_id')) aria-describedby="option-error" @endif>
            <legend>Pasirinkite vieną atsakymo variantą</legend>
            @foreach ($poll->options as $option)
                <label class="option">
                    <input type="radio" name="option_id" value="{{ $option->id }}"
                           @checked(is_scalar(old('option_id', $selectedOptionId)) && (string) old('option_id', $selectedOptionId) === (string) $option->id) required>
                    <span>{{ $option->text }}</span>
                </label>
            @endforeach
        </fieldset>
        @error('option_id')
        <p class="error" id="option-error" role="alert">{{ $message }}</p>
        @enderror
        <button type="submit" @disabled($poll->options->isEmpty())>Pateikti balsą</button>
    </form>
</main>
</body>
</html>
