@extends('layouts.app')

@section('title', 'Rezultatai: ' . $poll->title)

@section('content')
<div class="container py-5" style="max-width: 650px;">

    <h1 class="mb-2">{{ $poll->title }}</h1>
    <p class="text-muted mb-4">Apklausos rezultatai</p>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <p class="mb-4">
        Iš viso balsų: <strong>{{ $total }}</strong>
    </p>

    @foreach ($options as $option)
        @php
            $percent = $total > 0
                ? round($option->votes_count / $total * 100)
                : 0;
        @endphp

        <div class="mb-4">
            <div class="d-flex justify-content-between mb-1">
                <strong>{{ $option->text }}</strong>

                <span class="text-muted">
                    {{ $percent }}%
                </span>
            </div>

            @if (isset($selectedOptionId) && $option->id == $selectedOptionId)
                <small class="text-primary">Tavo balsas</small>
            @endif

            <div class="progress mt-2" style="height: 12px;">
                <div
                    class="progress-bar"
                    role="progressbar"
                    style="width: {{ $percent }}%"
                    aria-valuenow="{{ $percent }}"
                    aria-valuemin="0"
                    aria-valuemax="100">
                </div>
            </div>

            <small class="text-muted">
                {{ $option->votes_count }} balsai
            </small>
        </div>
    @endforeach

    <p><a href="{{ route('home') }}">← Grįžti į pradžią</a></p>
</div>
@endsection
