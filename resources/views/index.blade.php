<?php
/** @var \App\Entities\Url[] $urls */
/** @var \App\Entities\Url[] $latestUrlChecks */
?>
@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <h1 class="mt-5 mb-3">Urls</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Latest check date</th>
                        <th>Latest check code</th>
                    </tr>
                    @foreach($urls as $url)
                        <tr>
                            <td>{{ $url->id }}</td>
                            <td>
                                <a href="{{ route('urls.show', ['url' => $url->id]) }}">{{ $url->name }}</a>
                            </td>
                            <td>{{ $latestUrlChecks[$url->id]->createdAt ?? '-' }}</td>
                            <td>{{ $latestUrlChecks[$url->id]->statusCode ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
