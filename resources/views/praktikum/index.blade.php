@extends('layouts.app')

@section('title', 'Praktikum '.$prodi->nama.' — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; {{ $prodi->kode }} &nbsp;/&nbsp; <b>Praktikum</b>
@endsection

@section('content')

<div class="list-header">
    <div>
        <div class="list-header__title">{{ $prodi->kode }} · {{ $prodi->plot_label }}</div>
        <h1>Praktikum {{ $prodi->nama }}</h1>
        <p class="list-header__desc">Modul praktikum virtual — kerjakan langkah demi langkah dan uji pemahaman lewat kuis singkat.</p>
    </div>
</div>

@forelse($praktikums as $m)
    @php $done = in_array($m->id, $selesaiIds); @endphp
    <a href="{{ route('praktikum.show', $m) }}" class="item-card" style="--accent: {{ $prodi->accent_color }}">
        <div class="item-card__num">{{ $m->kode }}</div>
        <div class="item-card__body">
            <div class="item-card__title">{{ $m->judul }}</div>
            <div class="item-card__meta">
                <span class="badge badge--{{ strtolower($m->tingkat) }}">{{ $m->tingkat }}</span>
                <span>{!! $icons['clock'] !!} {{ $m->durasi }}</span>
            </div>
        </div>
        <div class="item-card__check {{ $done ? 'done' : '' }}">{!! $icons['check'] !!}</div>
    </a>
@empty
    <div class="alert-error">Belum ada praktikum untuk program studi ini.</div>
@endforelse
@endsection
