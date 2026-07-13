@extends('layouts.guest')

@section('title', 'Daftar — AgriVerse')

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-card__brand">
            <div class="brand__mark">{!! $icons['leaf'] !!}</div>
            <div>
                <div class="brand__name">AgriVerse</div>
                <div class="brand__tag">Virtual Learning</div>
            </div>
        </div>

        <h1>Buat akun baru</h1>
        <p class="sub">Daftar untuk mulai mengakses materi dan praktikum virtual.</p>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="field">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="field">
                <label for="nim">NIM (opsional)</label>
                <input type="text" id="nim" name="nim" value="{{ old('nim') }}">
            </div>

            <div class="field">
                <label for="prodi_id">Program Studi</label>
                <select id="prodi_id" name="prodi_id">
                    <option value="">— Pilih program studi —</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ old('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->kode }} — {{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="field">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="field">
                <label for="password_confirmation">Ulangi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn-primary">Daftar</button>
        </form>

        <p class="auth-alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        <p class="auth-alt" style="margin-top:6px;">
            <a href="{{ route('landing') }}">&larr; Kembali ke beranda</a>
        </p>
    </div>
</div>
@endsection
