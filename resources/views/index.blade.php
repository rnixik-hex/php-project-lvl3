<?php
/** @var \App\Entities\Domain[] $domains */
/** @var \App\Entities\DomainCheck[] $latestDomainChecks */
?>
@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <h1 class="mt-5 mb-3">Domains</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Latest check date</th>
                        <th>Latest check code</th>
                    </tr>
                    @foreach($domains as $domain)
                        <tr>
                            <td>{{ $domain->id }}</td>
                            <td>
                                <a href="{{ route('domains.show', ['domain' => $domain->id]) }}">{{ $domain->name }}</a>
                            </td>
                            <td>{{ $latestDomainChecks[$domain->id]->createdAt ?? '-' }}</td>
                            <td>{{ $latestDomainChecks[$domain->id]->statusCode ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
