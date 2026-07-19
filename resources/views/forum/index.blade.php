@extends('layouts.app')

@section('title', 'Forum Diskusi — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; <b>Forum</b>
@endsection

@section('content')

<div class="list-header">
    <div>
        <div class="list-header__title">Forum Diskusi</div>
        <h1>Diskusi Mahasiswa</h1>
        <p class="list-header__desc">Tanya jawab, sharing, dan diskusi seputar materi & praktikum.</p>
    </div>
    <a href="{{ route('forum.create') }}" class="btn-solid">+ Buat Thread</a>
</div>

@forelse($threads as $thread)
    <a href="{{ route('forum.show', $thread) }}" class="item-card">
        <div class="item-card__body">
            <div class="item-card__title">{{ $thread->title }}</div>
            <div class="item-card__meta">
                <span>{{ $thread->user->name }}</span>
                <span>&middot;</span>
                <span>{{ $thread->created_at->diffForHumans() }}</span>
                <span>&middot;</span>
                <span>{{ $thread->replies_count }} balasan</span>
            </div>
        </div>
    </a>
@empty
    <div class="alert-error">Belum ada thread. Jadilah yang pertama memulai diskusi!</div>
@endforelse

<div style="margin-top: 16px;">
    {{ $threads->links() }}
</div>

@endsection
