@extends('layout')

@section('title', 'Games')

@section('content')
    <div class="row">
        @foreach($games as $index => $game)
            @if($index > 0 && $index % 3 == 0)
    </div>
    <div class="row">
            @endif
            <div class="col-md text-center">
                <a href="/game/{{ $game->getSlug() }}/generate">
                    <div class="card mx-auto mb-4" style="max-width: 18rem;">
                        <img class="card-img-top"
                             src="{{ asset('img/' . $game->getGameLogo()) }}"
                             alt="{{ $game->getGameName() }}"/>
                        <div class="card-body">
                            <p class="btn btn-primary mb-0">Generate Numbers</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
