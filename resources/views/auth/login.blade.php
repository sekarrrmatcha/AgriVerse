@extends('layouts.guest')

@section('title', 'Masuk — AgriVerse')

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

        <h1>Masuk ke akun Anda</h1>
        <p class="sub">Lanjutkan materi dan praktikum yang sedang Anda kerjakan.</p>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="field">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
            </div>

            <label class="field-check">
                <input type="checkbox" name="remember"> Ingat saya
            </label>

            <button type="submit" class="btn-primary">Masuk</button>
        </form>

        <p class="auth-alt">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
        <p class="auth-alt" style="margin-top:6px;">
            <a href="{{ route('landing') }}">&larr; Kembali ke beranda</a>
        </p>

        <p class="auth-alt" style="margin-top:18px;font-family:var(--font-mono);font-size:.72rem;">
            Demo: mahasiswa@agriverse.test / password
        </p>
    </div>
</div>
@endsection
