@extends('layouts.app')

@section('title', 'El. pašto patvirtinimas')

@section('content')
<div class="container py-5" style="max-width: 550px;">

    <h1 class="mb-2">Patvirtinkite el. paštą</h1>

    <p class="text-muted mb-4">
        Prieš tęsdami patikrinkite savo el. paštą ir paspauskite patvirtinimo nuorodą.
    </p>

    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            Nauja patvirtinimo nuoroda išsiųsta į jūsų el. paštą.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf

        <button type="submit" class="btn btn-primary">
            Siųsti nuorodą dar kartą
        </button>
    </form>

</div>
@endsection
