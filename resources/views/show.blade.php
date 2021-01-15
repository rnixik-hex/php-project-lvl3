<?php
/** @var \App\Entities\Domain $domain */
?>
@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <h1 class="mt-5 mb-3">Site: {{ $domain->name }}</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tr>
                        <td>id</td>
                        <td>{{ $domain->id }}</td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>{{ $domain->name }}</td>
                    </tr>
                    <tr>
                        <td>created_at</td>
                        <td>{{ $domain->created_at }}</td>
                    </tr>
                    <tr>
                        <td>updated_at</td>
                        <td>{{ $domain->updated_at }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
