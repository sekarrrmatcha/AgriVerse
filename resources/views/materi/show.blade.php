@extends('layouts.app')

@section('title', $materi->judul.' — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp;
    <a href="{{ route('dashboard') }}">Beranda</a> &nbsp;/&nbsp;
    @if($semester)<a href="{{ route('semester.show', $semester) }}">{{ $semester->nama }}</a> &nbsp;/&nbsp;@endif
    @if($matakuliah)<a href="{{ route('matakuliah.show', $matakuliah) }}">{{ $matakuliah->nama }}</a> &nbsp;/&nbsp;@endif
    <b>Pertemuan 0{{ $materi->pertemuan_ke }}</b>
@endsection

@section('content')

@if($matakuliah)
<a href="{{ route('matakuliah.show', $matakuliah) }}" class="back-link">{!! $icons['arrowLeft'] !!} Kembali ke {{ $matakuliah->nama }}</a>
@else
<a href="{{ route('dashboard') }}" class="back-link">{!! $icons['arrowLeft'] !!} Kembali ke beranda</a>
@endif

<div class="detail-card" style="--accent: {{ $prodi->accent_color }}">
    <div class="detail-card__eyebrow">Pertemuan 0{{ $materi->pertemuan_ke }} · {{ $prodi->nama }}</div>
    <div class="detail-card__title">{{ $materi->judul }}</div>

    @if(!empty($materi->capaian))
    <div class="detail-block">
        <h4>Capaian Pembelajaran</h4>
        <ul class="outcome-list">
            @foreach(($materi->capaian ?? []) as $c)
                <li>{{ $c }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!empty($materi->pokok_bahasan))
    <div class="detail-block">
        <h4>Pokok Bahasan</h4>
        <ul class="tag-list">
            @foreach(($materi->pokok_bahasan ?? []) as $pb)
                <li>{{ $pb }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!empty($materi->pendahuluan))
    <div class="detail-block">
        <h4>Pendahuluan</h4>
        <p style="white-space:pre-line;">{{ $materi->pendahuluan }}</p>
    </div>
    @endif

    @if(!empty($materi->tinjauan_pustaka))
    <div class="detail-block">
        <h4>Tinjauan Pustaka</h4>
        @foreach(($materi->tinjauan_pustaka ?? []) as $tp)
            <div style="margin-bottom:16px;">
                <h5 style="margin-bottom:4px;">{{ $tp['judul'] ?? '' }}</h5>
                <p style="white-space:pre-line;">{{ $tp['isi'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
    @endif

    @if($matakuliah && $matakuliah->format_laporan)
    <div class="detail-block">
        <h4>Format Penulisan Laporan Praktikum</h4>
        <p style="white-space:pre-line;">{{ $matakuliah->format_laporan }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('materi.toggle', $materi) }}">
        @csrf
        <button type="submit" class="toggle-btn {{ $selesai ? 'done' : '' }}">
            {!! $icons['check'] !!}
            <span>{{ $selesai ? 'Selesai dipelajari' : 'Tandai selesai' }}</span>
        </button>
    </form>
</div>
@endsection
