@extends('layouts.app')

@section('title', 'Prisijungti prie balsavimo')

@section('content')
<div class="container py-5" style="max-width: 450px;">

    <h1 class="mb-2">Prisijungti prie balsavimo</h1>
    <p class="text-muted mb-4">Įveskite apklausos kodą.</p>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @error('code')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('polls.join') }}">
        @csrf

        <div class="mb-3">
            <input
                type="text"
                name="code"
                class="form-control text-uppercase"
                value="{{ old('code') }}"
                placeholder="Pvz. K7M2QX"
                maxlength="16"
                autofocus
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Prisijungti
        </button>

        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
            Grįžti
        </a>
    </form>

</div>
@endsection