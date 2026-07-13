@extends('layouts.guest')

@section('title', 'AgriVerse — Praktikum Digital Teknologi Pertanian')

@section('content')
<nav class="site-nav">
    <div style="display:flex;align-items:center;gap:10px;">
        <div class="brand__mark">{!! $icons['leaf'] !!}</div>
        <div>
            <div class="brand__name">AgriVerse</div>
            <div class="brand__tag">Virtual Learning</div>
        </div>
    </div>
    <div class="site-nav__links">
        <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
        <a href="{{ route('register') }}" class="btn-solid">Daftar</a>
    </div>
</nav>

<div class="view" style="margin:0 auto;">
    <div class="hero">
        <div class="hero__eyebrow">Praktikum Digital · Virtual Learning</div>
        <h1 class="hero__title">Satu ladang percobaan digital untuk tiga program studi Teknologi Pertanian.</h1>
        <p class="hero__desc">AgriVerse menghimpun materi dan praktikum virtual dari tiga program studi ke dalam satu ruang belajar. Daftar atau masuk untuk mulai menjelajahi materi perkuliahan dan mengerjakan modul praktikum secara interaktif.</p>
        <div style="margin-top:24px;position:relative;">
            <a href="{{ route('register') }}" class="btn-solid" style="padding:12px 22px;">Mulai Belajar</a>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-card"><div class="stat-card__num">3</div><div class="stat-card__label">Program studi</div></div>
        <div class="stat-card"><div class="stat-card__num">{{ $totalMateri }}</div><div class="stat-card__label">Pertemuan materi</div></div>
        <div class="stat-card"><div class="stat-card__num">{{ $totalPraktikum }}</div><div class="stat-card__label">Modul praktikum</div></div>
        <div class="stat-card"><div class="stat-card__num">100%</div><div class="stat-card__label">Virtual &amp; gratis</div></div>
    </div>

    <div class="section-heading">
        <h2>Peta Program Studi</h2>
        <span class="section-heading__note">3 blok · 1 lahan AgriVerse</span>
    </div>
    <div class="plot-grid">
        @foreach($prodis as $p)
            <div class="plot-card" style="--accent: {{ $p->accent_color }}">
                <div class="plot-card__bar"></div>
                <div class="plot-card__label">{{ $p->plot_label }}</div>
                <div class="plot-card__nama">{{ $p->nama }}</div>
                <div class="plot-card__desc">{{ $p->deskripsi }}</div>
                <div class="plot-card__links">
                    <a href="{{ route('register') }}">{{ $p->materis_count }} Materi</a>
                    <a href="{{ route('register') }}">{{ $p->praktikums_count }} Praktikum</a>
                </div>
            </div>
        @endforeach
    </div>

    <p style="text-align:center;font-family:var(--font-mono);font-size:.75rem;color:var(--c-ink-soft);margin-top:30px;">
         shannara
    </p>
</div>
@endsection
