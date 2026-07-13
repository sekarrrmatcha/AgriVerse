<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AgriVerse')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,900&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @include('partials.styles')
    @stack('styles')
</head>
<body>
<div class="app">
    @include('partials.sidebar')

    <div class="main">
        <header class="topbar">
            <div class="breadcrumb">@yield('breadcrumb')</div>
            <div class="topbar__progress">
                <span class="topbar__progress-label">Progres Belajar</span>
                <span class="pct-pill">{{ auth()->user()->progresPersen() }}%</span>
            </div>
        </header>

        <main class="view">
            @if(session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
