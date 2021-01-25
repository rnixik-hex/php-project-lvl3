<?php
/** @var \App\Entities\Domain $domain */
/** @var \App\Entities\DomainCheck[] $domainChecks */
?>
@extends('layout')

@section('content')
    <div class="container-lg">
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
                    <td>{{ $domain->createdAt }}</td>
                </tr>
                <tr>
                    <td>updated_at</td>
                    <td>{{ $domain->updatedAt }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="container-lg">
        <h1 class="mt-5 mb-3">Проверки</h1>
        <div class="mb-3">
            <form method="post" action="{{ route('domains.storeCheck', ['domain' => $domain->id]) }}">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-primary">Запросить проверку</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Код ответа</th>
                    <th>h1</th>
                    <th>keywords</th>
                    <th>description</th>
                    <th>Дата создания</th>
                </tr>
                <?php foreach ($domainChecks as $domainCheck) : ?>
                    <tr>
                        <td><?= $domainCheck->id ?></td>
                        <td><?= $domainCheck->statusCode ?></td>
                        <td><?= $domainCheck->h1 ?></td>
                        <td><?= $domainCheck->keywords ?></td>
                        <td><?= $domainCheck->description ?></td>
                        <td><?= $domainCheck->createdAt ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
@endsection
