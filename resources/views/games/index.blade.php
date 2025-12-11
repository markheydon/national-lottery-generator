@extends('layout')

@section('title', 'Games')

@section('content')
    <div class="row">
        @foreach($games as $index => $game)
            @if($index == 3)
    </div>
    <div class="row">
            @endif
            <div class="col-md text-center">
                <a href="/game/{{ $game->getSlug() }}/generate">
                    <div class="card mx-auto" style="width: 18rem; margin-bottom: 1em;">
                        <img class="card-img-top"
                             src="{{ asset('img/' . $game->getGameLogo()) }}" class="img-card"
                             alt="{{ $game->getGameName() }}"/>
                        <div class="card-body">
                            <p class="btn btn-primary">Generate Numbers</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
