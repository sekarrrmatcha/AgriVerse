@extends('layouts.app')

@section('title', 'Materi '.$prodi->nama.' — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; {{ $prodi->kode }} &nbsp;/&nbsp; <b>Materi</b>
@endsection

@section('content')

<div class="list-header">
    <div>
        <div class="list-header__title">{{ $prodi->kode }} · {{ $prodi->plot_label }}</div>
        <h1>Materi {{ $prodi->nama }}</h1>
        <p class="list-header__desc">{{ $prodi->deskripsi }}</p>
    </div>
</div>

@forelse($materis as $m)
    @php $done = in_array($m->id, $selesaiIds); @endphp
    <a href="{{ route('materi.show', $m) }}" class="item-card" style="--accent: {{ $prodi->accent_color }}">
        <div class="item-card__num">PERT. 0{{ $m->pertemuan_ke }}</div>
        <div class="item-card__body">
            <div class="item-card__title">{{ $m->judul }}</div>
            <div class="item-card__meta"><span>{{ $m->capaian[0] ?? '' }}</span></div>
        </div>
        <div class="item-card__check {{ $done ? 'done' : '' }}">{!! $icons['check'] !!}</div>
    </a>
@empty
    <div class="alert-error">Belum ada materi untuk program studi ini.</div>
@endforelse
@endsection
