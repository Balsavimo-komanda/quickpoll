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

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Slaptažodis</label>
            <input id="password" type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="current-password">

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox"
                   name="remember" id="remember"
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                Prisiminti mane
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            Prisijungti
        </button>

        @if (Route::has('password.request'))
            <a class="btn btn-outline-secondary"
               href="{{ route('password.request') }}">
                Pamiršau slaptažodį
            </a>
        @endif
    </form>

</div>
@endsection
