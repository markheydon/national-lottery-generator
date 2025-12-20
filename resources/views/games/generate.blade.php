@extends('layout')

@section('title', $gameName)

@section('navigation')
<nav class="game-nav mt-3 mt-md-0">
    <div class="dropdown">
        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="gameMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid-3x3-gap-fill" viewBox="0 0 16 16" style="margin-right: 4px; margin-top: -2px;">
              <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zM1 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zM1 12a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
            </svg>
            Other Games
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="gameMenuDropdown">
            <a class="dropdown-item" href="/">← All Games</a>
            <div class="dropdown-divider"></div>
            @foreach($allGames as $game)
                @if($game->getSlug() !== $currentSlug)
                    <a class="dropdown-item" href="/game/{{ $game->getSlug() }}/generate">{{ $game->getGameName() }}</a>
                @endif
            @endforeach
        </div>
    </div>
</nav>
@endsection

@section('content')
    <div class="alert alert-danger" role="alert">
        <p class="lead mb-0">
            <em>
                While the numbers generated using this just-for-fun tool are not random, they are highly unlikely to actually win you any money!  The National Lottery IS totally random!
                Use at your own risk.  I am in no way affiliated with the National Lottery.
            </em>
        </p>
    </div>

    <div class="row align-items-center">
        <div class="col-sm-12 col-md-4 mb-4">
            <div class="card mx-auto" style="max-width: 18rem;">
                <img class="card-img-top"
                     src="{{ asset('img/' . $gameLogo) }}"
                     alt="{{ $gameName }}"/>
                <div class="card-body">
                    <p class="card-text mb-0"><strong>Latest Draw</strong>: {{ $latestDrawDate }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <h3 class="mb-3">Suggested Lines</h3>
            <div class="row">
                <div class="col-12 col-lg-10">
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Suggested Numbers</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($suggested as $lineNum => $line)
                            <tr>
                                <th scope="row" class="text-center">{{ $lineNum + 1 }}</th>
                                <td class="text-nowrap">{{ $line }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <hr/>
    <h3 class="mb-3">Other Suggestions</h3>
    <div class="row">
        <div class="col-12 col-lg-6">
            <table class="table table-bordered table-sm">
                <thead>
                <tr>
                    <th scope="col" class="text-center">#</th>
                    <th scope="col">Suggested Numbers</th>
                </tr>
                </thead>
                <tbody>
                @foreach($others as $lineNum => $line)
                    <tr>
                        <th scope="row" class="text-center">{{ $lineNum + 1 }}</th>
                        <td>{{ $line }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection