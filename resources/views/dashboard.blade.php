@extends('layouts.app')

@section('title', 'Beranda — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; <b>Beranda</b>
@endsection

@section('content')
<div class="hero">
    <div class="hero__eyebrow">Selamat datang kembali</div>
    <h1 class="hero__title">Halo, {{ strtok($user->name ?? 'pengguna', ' ') }} 👋</h1>
    <p class="hero__desc">
        @if(! $prodi)
            Program studi Anda belum diatur. Hubungi admin untuk mengatur program studi akun Anda.
        @elseif($progresPersen >= 100)
            Semua materi dan praktikum sudah Anda selesaikan. Mantap!
        @else
            Progres belajar Anda saat ini {{ $progresPersen }}%. Pilih semester untuk melanjutkan mata kuliah Anda.
        @endif
    </p>
</div>

@if($prodi)
    <div class="detail-card" style="--accent: {{ $prodi->accent_color ?? '#7d9c5e' }}; margin-bottom:24px;">
        <div class="detail-card__eyebrow">{{ $prodi->kode }} · {{ $prodi->plot_label }}</div>
        <div class="detail-card__title">{{ $prodi->nama }}</div>
        @if($prodi->deskripsi)
            <p class="list-header__desc" style="margin:0;">{{ $prodi->deskripsi }}</p>
        @endif
    </div>
@endif

<div class="stats-row">
    <div class="stat-card"><div class="stat-card__num">{{ $stats['semester'] }}</div><div class="stat-card__label">Semester</div></div>
    <div class="stat-card"><div class="stat-card__num">{{ $stats['matakuliah'] }}</div><div class="stat-card__label">Mata kuliah</div></div>
    <div class="stat-card"><div class="stat-card__num">{{ $stats['materi'] }}</div><div class="stat-card__label">Pertemuan materi</div></div>
    <div class="stat-card"><div class="stat-card__num">{{ $stats['praktikum'] }}</div><div class="stat-card__label">Modul praktikum</div></div>
</div>

<div class="section-heading">
    <h2>Pilih Semester</h2>
    <span class="section-heading__note">{{ $prodi->kode ?? '-' }} · {{ $prodi->nama ?? '' }}</span>
</div>
@forelse($semesters as $semester)
    @if($semester->matakuliahs_count === 0)
        <div class="item-card item-card--kosong" style="--accent: {{ $prodi->accent_color ?? '#7d9c5e' }}" aria-disabled="true">
            <div class="item-card__num">{{ $semester->nomor }}</div>
            <div class="item-card__body">
                <div class="item-card__title">{{ $semester->nama }}</div>
                <div class="item-card__meta">
                    <span class="item-card__meta--muted">Nantikan saja dimasa depan</span>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('semester.show', $semester) }}" class="item-card" style="--accent: {{ $prodi->accent_color ?? '#7d9c5e' }}">
            <div class="item-card__num">{{ $semester->nomor }}</div>
            <div class="item-card__body">
                <div class="item-card__title">{{ $semester->nama }}</div>
                <div class="item-card__meta">
                    <span>{{ $semester->matakuliahs_count }} Mata kuliah</span>
                </div>
            </div>
        </a>
    @endif
@empty
    <div class="alert-error">Belum ada semester untuk program studi Anda.</div>
@endforelse
@endsection
