@extends('layouts.app')

@section('title', $matakuliah->nama.' — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp;
    <a href="{{ route('dashboard') }}">Beranda</a> &nbsp;/&nbsp;
    <a href="{{ route('semester.show', $semester) }}">{{ $semester->nama }}</a> &nbsp;/&nbsp;
    <b>{{ $matakuliah->nama }}</b>
@endsection

@section('content')

<a href="{{ route('semester.show', $semester) }}" class="back-link">{!! $icons['arrowLeft'] !!} Kembali ke {{ $semester->nama }}</a>

<div class="list-header">
    <div>
        <div class="list-header__title">{{ $prodi->kode }} · {{ $semester->nama }}</div>
        <h1>{{ $matakuliah->nama }}</h1>
        @if($matakuliah->deskripsi)
            <p class="list-header__desc">{{ $matakuliah->deskripsi }}</p>
        @else
            <p class="list-header__desc">Akses modul materi dan praktikum virtual untuk mata kuliah ini.</p>
        @endif
    </div>
</div>

@if($matakuliah->materis->isNotEmpty())
<div class="section-heading" style="margin-top:8px;">
    <h2>Modul Materi</h2>
    <span class="section-heading__note">{{ $matakuliah->materis->count() }} pertemuan</span>
</div>

@foreach($matakuliah->materis as $m)
    @php $done = in_array($m->id, $materiSelesaiIds); @endphp
    <a href="{{ route('materi.show', $m) }}" class="item-card" style="--accent: {{ $prodi->accent_color }}">
        <div class="item-card__num">PERT. 0{{ $m->pertemuan_ke }}</div>
        <div class="item-card__body">
            <div class="item-card__title">{{ $m->judul }}</div>
            <div class="item-card__meta"><span>{{ $m->capaian[0] ?? '' }}</span></div>
        </div>
        <div class="item-card__check {{ $done ? 'done' : '' }}">{!! $icons['check'] !!}</div>
    </a>
@endforeach
@endif

@if($matakuliah->praktikums->isNotEmpty())
<div class="section-heading" style="margin-top:24px;">
    <h2>Praktikum &amp; Kuis</h2>
    <span class="section-heading__note">{{ $matakuliah->praktikums->count() }} modul</span>
</div>

@foreach($matakuliah->praktikums as $p)
    @php $done = in_array($p->id, $praktikumSelesaiIds); @endphp
    <a href="{{ route('praktikum.show', $p) }}" class="item-card" style="--accent: {{ $prodi->accent_color }}">
        <div class="item-card__num">{{ $p->kode }}</div>
        <div class="item-card__body">
            <div class="item-card__title">{{ $p->judul }}</div>
            <div class="item-card__meta">
                <span class="badge badge--{{ strtolower($p->tingkat) }}">{{ $p->tingkat }}</span>
                <span>{!! $icons['clock'] !!} {{ $p->durasi }}</span>
            </div>
        </div>
        <div class="item-card__check {{ $done ? 'done' : '' }}">{!! $icons['check'] !!}</div>
    </a>
@endforeach
@endif

@if($matakuliah->materis->isEmpty() && $matakuliah->praktikums->isEmpty())
    <div class="alert-error">Belum ada materi atau praktikum untuk mata kuliah ini.</div>
@endif
@endsection
