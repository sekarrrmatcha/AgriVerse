@extends('layouts.app')

@section('title', $semester->nama.' — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; <a href="{{ route('dashboard') }}">Beranda</a> &nbsp;/&nbsp; <b>{{ $semester->nama }}</b>
@endsection

@section('content')

<a href="{{ route('dashboard') }}" class="back-link">{!! $icons['arrowLeft'] !!} Kembali ke beranda</a>

<div class="list-header">
    <div>
        <div class="list-header__title">{{ $prodi->kode }} · {{ $prodi->plot_label }}</div>
        <h1>{{ $semester->nama }}</h1>
        <p class="list-header__desc">Pilih mata kuliah pada {{ strtolower($semester->nama) }} untuk mengakses modul materi dan praktikum.</p>
    </div>
</div>

@forelse($matakuliahs as $mk)
    <a href="{{ route('matakuliah.show', $mk) }}" class="item-card" style="--accent: {{ $prodi->accent_color }}">
        <div class="item-card__num">{{ $mk->urutan }}</div>
        <div class="item-card__body">
            <div class="item-card__title">{{ $mk->nama }}</div>
            <div class="item-card__meta">
                @if($mk->kode)<span>{{ $mk->kode }}</span>@endif
                @if($mk->sks)<span>{{ $mk->sks }} SKS</span>@endif
                <span>{{ $mk->materis_count }} Materi</span>
                <span>{{ $mk->praktikums_count }} Praktikum</span>
            </div>
            @if($mk->deskripsi)
                <div class="item-card__desc">{{ $mk->deskripsi }}</div>
            @endif
        </div>
        <div class="item-card__arrow">→</div>
    </a>
@empty
    <div class="alert-error">Belum ada mata kuliah pada semester ini.</div>
@endforelse
@endsection
