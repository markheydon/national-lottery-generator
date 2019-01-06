@extends('layout')

@section('content')
    @foreach($games as $game)
        <p>
            <a href="/game/{{ $game->id }}/generate">
                <img src="{{ asset('img/' . $game->game_logo) }}" alt="{{ $game->game_name }}"/>
            </a>
        </p>
    @endforeach
@endsection
