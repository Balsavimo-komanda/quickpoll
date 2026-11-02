@extends('layouts.app')

@section('title', $poll->title . ' – QuickPoll')

@section('content')
<div class="container py-5" style="max-width: 650px;">

    <a href="{{ route('home') }}" class="text-decoration-none">
        ← Visos apklausos
    </a>

    <h1 class="mt-4 mb-3">{{ $poll->title }}</h1>

    <div class="border rounded p-3 mb-4">
        <p class="mb-2">
            Kodas: <strong id="poll-code">{{ $poll->code }}</strong>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary ms-2"
                onclick="copyText(@js($poll->code), this)">
                Kopijuoti
            </button>
        </p>

        <p class="mb-0">
            Nuoroda:
            <a href="{{ route('polls.join.code', $poll->code) }}">
                {{ route('polls.join.code', $poll->code) }}
            </a>

            <button
                type="button"
                class="btn btn-sm btn-outline-secondary ms-2"
                onclick="copyText(@js(route('polls.join.code', $poll->code)), this)">
                Kopijuoti
            </button>
        </p>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('polls.vote', $poll) }}" id="vote-form">
        @csrf

        <input type="hidden" name="visitor_id" id="visitor_id">

        <fieldset class="mb-4"
            @if ($errors->has('poll_option_id'))
                aria-describedby="option-error"
            @endif>

            <legend class="h5 mb-3">
                Pasirinkite vieną atsakymo variantą
            </legend>

            @foreach ($poll->options as $option)
                <label class="d-flex align-items-center gap-2 border rounded p-3 mb-2">
                    <input
                        type="radio"
                        name="poll_option_id"
                        value="{{ $option->id }}"
                        @checked((string) old('poll_option_id', $selectedOptionId ?? '') === (string) $option->id)
                        required
                    >

                    <span>{{ $option->text }}</span>
                </label>
            @endforeach

        </fieldset>

        @error('poll_option_id')
            <div class="alert alert-danger" id="option-error" role="alert">
                {{ $message }}
            </div>
        @enderror

        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
            Kraunama saugos patikra...
        </button>
    </form>

    <div class="mt-4">
        <a href="{{ route('polls.results', $poll) }}">
            Peržiūrėti rezultatus
        </a>
    </div>

</div>

<script>
    function copyText(text, btn) {
        const done = () => {
            const old = btn.textContent;
            btn.textContent = 'Nukopijuota!';
            setTimeout(() => btn.textContent = old, 1500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done);
        } else {
            const input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            done();
        }
    }

    const fpPromise = import('https://openfpcdn.io/fingerprintjs/v4')
        .then(FingerprintJS => FingerprintJS.load());

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
@endsection