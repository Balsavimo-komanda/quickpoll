@extends('layouts.app')

@section('title', 'QuickPoll')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold mb-5"> Sveiki atvykę į QuickPoll.</h1>

    <div class="mb-5">
        <p class="fw-normal"> 
            Kurkite apklausas ir balsuokite greitai.
        </p>

        <a href="{{ route('polls.create') }}" class="btn btn-primary me-2">
            Nauja apklausa
        </a>

        <a href="{{ route('polls.join.form') }}" class="btn btn-outline-secondary">
            Turiu kodą
        </a>
    </div>

    <h2 class="h4 mb-3">Apklausos</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Apklausos pavadinimas</th>
            <th>Veiksmai</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($polls as $poll)
            <tr>
                <td>{{ $poll->title }}</td>
                <td>
                    <a href="{{ route('polls.show', $poll) }}">
                        Peržiūrėti
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2">Apklausų dar nėra.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</div>
@endsection
