<?php
/** @var \App\Entities\Url $url */
/** @var \App\Entities\Url[] $urlChecks */
?>
@extends('layout')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Site: {{ $url->name }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    <td>id</td>
                    <td>{{ $url->id }}</td>
                </tr>
                <tr>
                    <td>name</td>
                    <td>{{ $url->name }}</td>
                </tr>
                <tr>
                    <td>created_at</td>
                    <td>{{ $url->createdAt }}</td>
                </tr>
                <tr>
                    <td>updated_at</td>
                    <td>{{ $url->updatedAt }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="container-lg">
        <h1 class="mt-5 mb-3">Проверки</h1>
        <div class="mb-3">
            <form method="post" action="{{ route('urls.storeCheck', ['url' => $url->id]) }}">
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
                <?php foreach ($urlChecks as $urlCheck) : ?>
                    <tr>
                        <td><?= $urlCheck->id ?></td>
                        <td><?= $urlCheck->statusCode ?></td>
                        <td><?= $urlCheck->h1 ?></td>
                        <td><?= $urlCheck->keywords ?></td>
                        <td><?= $urlCheck->description ?></td>
                        <td><?= $urlCheck->createdAt ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
@endsection
