@extends('layouts.app')

@section('title', 'Buat Thread — Forum AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp; <a href="{{ route('forum.index') }}">Forum</a> &nbsp;/&nbsp; <b>Buat Thread</b>
@endsection

@section('content')

<div class="list-header">
    <div>
        <div class="list-header__title">Forum Diskusi</div>
        <h1>Buat Thread Baru</h1>
    </div>
</div>

@if($errors->any())
    <div class="alert-error">
        <ul style="margin:0;padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('forum.store') }}" class="item-card" style="flex-direction:column;align-items:stretch;gap:14px;">
    @csrf
    <div>
        <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Judul</label>
        <input type="text" name="title" value="{{ old('title') }}"
               style="width:100%;padding:10px 12px;border:1px solid var(--c-border);border-radius:8px;font-family:inherit;">
    </div>
    <div>
        <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px;">Isi Diskusi</label>
        <textarea name="body" rows="6"
                  style="width:100%;padding:10px 12px;border:1px solid var(--c-border);border-radius:8px;font-family:inherit;">{{ old('body') }}</textarea>
    </div>
    <button type="submit" class="btn-solid" style="align-self:flex-start;">Posting Thread</button>
</form>

@endsection
