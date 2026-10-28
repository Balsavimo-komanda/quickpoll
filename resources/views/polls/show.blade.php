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
        input[type="radio"] { accent-color: #1d4ed8; flex-shrink: 0; }
        button { background: #1d4ed8; color: white; padding: 12px 24px; border: 0; border-radius: 8px; font: inherit; cursor: pointer; }
        button:disabled { background: #64748b; cursor: not-allowed; opacity: 0.7; }
        .status { padding: 12px; background: #ecfdf5; color: #166534; border-radius: 8px; margin-bottom: 16px; }
        .error-banner { padding: 12px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 8px; margin-bottom: 16px; }
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

    @if (session('error'))
        <p class="error-banner" role="alert">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('polls.vote', $poll) }}" id="vote-form">
        @csrf

        {{-- Hidden input to hold the anonymous browser fingerprint hash --}}
        <input type="hidden" name="visitor_id" id="visitor_id">

        <fieldset @if ($errors->has('poll_option_id')) aria-describedby="option-error" @endif>
            <legend>Pasirinkite vieną atsakymo variantą</legend>
            @foreach ($poll->options as $option)
                <label class="option">
                    {{-- Changed name from option_id to poll_option_id --}}
                    <input type="radio" name="poll_option_id" value="{{ $option->id }}"
                           @checked(is_scalar(old('poll_option_id', $selectedOptionId)) && (string) old('poll_option_id', $selectedOptionId) === (string) $option->id) required>
                    <span>{{ $option->text }}</span>
                </label>
            @endforeach
        </fieldset>

        @error('poll_option_id')
        <p class="error" id="option-error" role="alert">{{ $message }}</p>
        @enderror

        {{-- Submit button starts disabled until FingerprintJS loads --}}
        <button type="submit" id="submit-btn" disabled>Kraunama saugos patikra...</button>
    </form>
</main>

{{-- FingerprintJS --}}
<script>
    const fpPromise = import('https://openfpcdn.io/fingerprintjs/v4').then(FingerprintJS => FingerprintJS.load());

    async function initFingerprint() {
        try {
            const fp = await fpPromise;
            const result = await fp.get();

            document.getElementById('visitor_id').value = result.visitorId;

            const submitBtn = document.getElementById('submit-btn');

            submitBtn.disabled = false;
            submitBtn.textContent = 'Pateikti balsą';
        } catch (error) {
            console.error('Error loading security check:', error);
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.textContent = 'Klaida kraunant patikrą';
        }
    }

    initFingerprint();
</script>
</body>
</html>
