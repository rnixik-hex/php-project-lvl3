<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

        <style>
            body {
                font-family: 'Nunito';
            }
        </style>
    </head>
    <body class="d-flex flex-column">
        <header>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <a class="navbar-brand" href="{{ route('home') }}">Analyzer</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ route('urls.index') }}">Urls</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="flex-grow-1">
            @if(Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @elseif(Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @elseif(Session::has('info'))
                <div class="alert alert-info">{{ Session::get('info') }}</div>
            @endif
            @yield('content')
        </main>

        <footer class="border-top py-3 mt-5">
            <div class="container-lg">
                <div class="text-center">
                    Template was copied from <a href="https://php-l3-page-analyzer.herokuapp.com/">Hexlet demo project</a>.
                </div>
                <div class="text-center">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </footer>
    </body>
</html>
