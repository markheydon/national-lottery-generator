@extends('layout')

@section('title', $gameName)

@section('content')
    <div class="row align-items-center">
        <div class="col-sm">
            <div class="card" style="width: 18rem; margin-bottom: 1em;">
                <img class="card-img-top"
                     src="{{ asset('img/' . $gameLogo) }}" class="img-card"
                     alt="{{ $gameName }}"/>
                <div class="card-body">
                    <p class="card-text"><strong>Latest Draw</strong>: {{ $latestDrawDate }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg">
            <h3>Suggested Lines</h3>
            <div class="row">
                <div class="col-sm-6">
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
    <h3>Other Suggestions</h3>
    <div class="row">
        <div class="col-sm-6">
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