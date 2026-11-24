@extends('layouts.app')

@section('title', 'Prisijungti')

@section('content')
<div class="container py-5" style="max-width: 450px;">

    <h1 class="mb-2">Prisijungti</h1>
    <p class="text-muted mb-4">Prisijunkite prie savo paskyros.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">El. paštas</label>
            <input id="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}"
                   required autocomplete="email" autofocus>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold">Slaptažodis</label>
            <input id="password" type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="current-password">

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Prisijungti
        </button>
    </form>

</div>
@endsection
