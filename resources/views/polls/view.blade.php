@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Apklausos</div>
                    <button href="{{ route('home') }}">Atgal</button>
                    <div class="card-body">
                        @if($user->id == $poll->user_id)
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:300px;"></th>
                                <th>Pavadinimas</th>
                                <th>Variantai</th>
                                <th>Rezultatai</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($polls as $poll)
                                <tr>
                                    <td>{{$poll->title}}</td>
                                    <td>
                                        @foreach ($poll->options as $option)
                                            <a>{{$option->text}}</a>
                                        @endforeach
                                    </td>
                                    <td href="{{ route('polls.results)}}">Rezultatai</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
