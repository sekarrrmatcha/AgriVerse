@include('partials.icons')
<aside class="sidebar">
    <div class="brand">
        <div class="brand__mark">{!! $icons['leaf'] !!}</div>
        <div>
            <div class="brand__name">AgriVerse</div>
            <div class="brand__tag">Virtual Learning</div>
        </div>
    </div>

    <div class="sidebar__user">
        <div class="sidebar__user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div>
            <div class="sidebar__user-name">{{ auth()->user()->name }}</div>
            @if(auth()->user()->nim)
                <div class="sidebar__user-nim">{{ auth()->user()->nim }}</div>
            @endif
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        {!! $icons['home'] !!}<span>Beranda</span>
    </a>

    <a href="{{ route('forum.index') }}" class="nav-item {{ request()->routeIs('forum.*') ? 'active' : '' }}">
        {!! $icons['chat'] !!}<span>Forum</span>
    </a>

    <div class="nav-section-label">Program Studi Anda</div>

    @php($prodiUser = auth()->user()->prodi)
    @if($prodiUser)
        <div class="prodi-block" style="--dot: {{ $prodiUser->accent_color }}">
            <div class="prodi-block__head">
                <span class="prodi-block__dot"></span>
                <span class="prodi-block__kode">{{ $prodiUser->kode }} · {{ $prodiUser->plot_label }}</span>
            </div>
            <div class="prodi-block__nama">{{ $prodiUser->nama }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('logout') }}" style="margin-top:auto;">
        @csrf
        <button type="submit" class="sidebar__logout">{!! $icons['logout'] !!} Keluar</button>
    </form>

    <div class="sidebar__foot">JURUSAN TEKNOLOGI PERTANIAN</div>
</aside>
