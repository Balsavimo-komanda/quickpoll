@extends('layouts.app')

@section('title', 'Nauja apklausa')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="h4 mb-3">Nauja apklausa</h2>

            <form method="POST" action="{{ route('polls.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Klausimas</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title') }}" required>
                </div>

                <label class="form-label">Atsakymo variantai</label>
                <div id="options" class="mb-2">
                    @foreach (old('options', ['', '']) as $option)
                        <input type="text" name="options[]" class="form-control mb-2"
                               value="{{ $option }}" placeholder="Variantas {{ $loop->iteration }}" required>
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-secondary" onclick="addOption()">+ Pridėti variantą</button>
                <button type="submit" class="btn btn-primary">Sukurti</button>
            </form>
        </div>
    </div>

    <script>
        function addOption() {
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'options[]';
            input.className = 'form-control mb-2';
            input.placeholder = 'Variantas';
            input.required = true;
            document.getElementById('options').appendChild(input);
        }
    </script>
@endsection