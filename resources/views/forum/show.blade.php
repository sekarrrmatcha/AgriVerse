@extends('layouts.app')

@section('title', $thread->title.' — Forum AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; <a href="{{ route('forum.index') }}">Forum</a> &nbsp;/&nbsp; <b>{{ $thread->title }}</b>
@endsection

@section('content')

<div class="item-card" style="flex-direction:column;align-items:stretch;">
    <div class="item-card__title" style="font-size:1.3rem;margin-bottom:8px;">{{ $thread->title }}</div>
    <div class="item-card__meta" style="margin-bottom:14px;">
        <span>{{ $thread->user->name }}</span>
        <span>&middot;</span>
        <span>{{ $thread->created_at->diffForHumans() }}</span>
    </div>
    <p style="white-space:pre-line;line-height:1.6;margin:0;">{{ $thread->body }}</p>
</div>

<h3 style="margin:22px 0 12px;font-family:var(--font-display);">Balasan ({{ $thread->replies->count() }})</h3>

@forelse($thread->replies as $reply)
    <div class="item-card" style="flex-direction:column;align-items:stretch;">
        <div class="item-card__meta" style="margin-bottom:6px;">
            <span style="font-weight:600;color:var(--c-ink);">{{ $reply->user->name }}</span>
            <span>&middot;</span>
            <span>{{ $reply->created_at->diffForHumans() }}</span>
        </div>
        <p style="white-space:pre-line;margin:0;">{{ $reply->body }}</p>
    </div>
@empty
    <p style="color:var(--c-ink-soft);font-size:.86rem;">Belum ada balasan. Jadilah yang pertama membalas.</p>
@endforelse

<form method="POST" action="{{ route('forum.reply', $thread) }}" style="margin-top:16px;">
    @csrf
    <textarea name="body" rows="4" placeholder="Tulis balasan..."
              style="width:100%;padding:10px 12px;border:1px solid var(--c-border);border-radius:8px;font-family:inherit;margin-bottom:10px;"></textarea>
    <button type="submit" class="btn-solid">Kirim Balasan</button>
</form>

@endsection
