@extends('layout')

@section('title', 'Games')

@section('content')
    <div class="row">
        @foreach($games as $game)
            @if($game->id == 4)
    </div>
    <div class="row">
            @endif
            <div class="col-md text-center">
                <a href="/game/{{ $game->id }}/generate">
                    <div class="card mx-auto" style="width: 18rem; margin-bottom: 1em;">
                        <img class="card-img-top"
                             src="{{ asset('img/' . $game->game_logo) }}" class="img-card"
                             alt="{{ $game->game_name }}"/>
                        <div class="card-body">
                            <p class="btn btn-primary">Generate Numbers</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
