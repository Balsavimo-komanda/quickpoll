@extends('layouts.app')

@section('title', 'Nauja apklausa')

@section('content')
<div class="container py-5" style="max-width: 650px;">

    <h1 class="mb-2">Nauja apklausa</h1>
    <p class="text-muted mb-4">Sukurkite naują apklausą.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('polls.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Klausimas</label>
            <input
                type="text"
                name="title"
                class="form-control"
                value="{{ old('title') }}"
                placeholder="Įveskite klausimą"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Atsakymo variantai</label>

            <div id="options">
                @foreach (old('options', ['', '']) as $option)
                    <input
                        type="text"
                        name="options[]"
                        class="form-control mb-2"
                        value="{{ $option }}"
                        placeholder="Atsakymo variantas"
                        required
                    >
                @endforeach
            </div>
        </div>

        <button type="button" class="btn btn-outline-secondary mb-3"
                onclick="addOption()">
            + Pridėti variantą
        </button>

        <div>
            <button type="submit" class="btn btn-primary">
                Sukurti apklausą
            </button>

            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                Atšaukti
            </a>
        </div>
    </form>

</div>

<script>
    function addOption() {
        const input = document.createElement('input');

        input.type = 'text';
        input.name = 'options[]';
        input.className = 'form-control mb-2';
        input.placeholder = 'Atsakymo variantas';
        input.required = true;

        document.getElementById('options').appendChild(input);
    }
</script>
@endsection