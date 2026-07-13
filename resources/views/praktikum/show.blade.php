@extends('layouts.app')

@section('title', $praktikum->judul.' — AgriVerse')

@section('breadcrumb')
    AgriVerse &nbsp;/&nbsp;
    <a href="{{ route('dashboard') }}">Beranda</a> &nbsp;/&nbsp;
    @if($semester)<a href="{{ route('semester.show', $semester) }}">{{ $semester->nama }}</a> &nbsp;/&nbsp;@endif
    @if($matakuliah)<a href="{{ route('matakuliah.show', $matakuliah) }}">{{ $matakuliah->nama }}</a> &nbsp;/&nbsp;@endif
    <b>{{ $praktikum->kode }}</b>
@endsection

@section('content')

@if($matakuliah)
<a href="{{ route('matakuliah.show', $matakuliah) }}" class="back-link">{!! $icons['arrowLeft'] !!} Kembali ke {{ $matakuliah->nama }}</a>
@else
<a href="{{ route('dashboard') }}" class="back-link">{!! $icons['arrowLeft'] !!} Kembali ke beranda</a>
@endif

<div class="detail-card" style="--accent: {{ $prodi->accent_color }}">
    <div class="detail-card__eyebrow">{{ $praktikum->kode }} · {{ $praktikum->tingkat }} · {{ $praktikum->durasi }}</div>
    <div class="detail-card__title">{{ $praktikum->judul }}</div>

    <div class="detail-block">
        <h4>Tujuan</h4>
        <p>{{ $praktikum->tujuan }}</p>
    </div>

    @if(!empty($praktikum->alat))
    <div class="detail-block">
        <h4>Alat</h4>
        <ul class="tag-list">
            @foreach(($praktikum->alat ?? []) as $a)
                <li>{{ $a }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!empty($praktikum->bahan))
    <div class="detail-block">
        <h4>Bahan</h4>
        <ul class="tag-list">
            @foreach(($praktikum->bahan ?? []) as $b)
                <li>{{ $b }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(!empty($praktikum->langkah))
    <div class="detail-block">
        <h4>Langkah Kerja</h4>
        <form method="POST" action="{{ route('praktikum.langkah', $praktikum) }}">
            @csrf
            <ul class="step-list">
                @foreach(($praktikum->langkah ?? []) as $i => $l)
                    @php $isChecked = in_array($i, $langkahSelesai); @endphp
                    <li class="step-item">
                        <label class="step-item__box {{ $isChecked ? 'checked' : '' }}" style="cursor:pointer;">
                            <input type="checkbox" name="langkah_selesai[]" value="{{ $i }}" {{ $isChecked ? 'checked' : '' }} style="position:absolute;opacity:0;width:22px;height:22px;cursor:pointer;">
                            {!! $icons['check'] !!}
                        </label>
                        <span class="step-item__text {{ $isChecked ? 'done' : '' }}">{{ $i + 1 }}. {{ $l }}</span>
                    </li>
                @endforeach
            </ul>
            <button type="submit" class="toggle-btn" style="margin-top:14px;">Simpan Progres Langkah</button>
        </form>
    </div>
    @endif

    <form method="POST" action="{{ route('praktikum.toggle', $praktikum) }}">
        @csrf
        <button type="submit" class="toggle-btn {{ $selesai ? 'done' : '' }}">
            {!! $icons['check'] !!}
            <span>{{ $selesai ? 'Praktikum selesai' : 'Tandai praktikum selesai' }}</span>
        </button>
    </form>

    @if(!empty($praktikum->kuis))
        <div class="detail-block" style="margin-top:28px;">
            <h4>Uji Pemahaman <span id="quizProgress" style="font-weight:400;font-size:.8rem;color:var(--c-ink-soft);"></span></h4>
            <div class="quiz-box" id="quizBox">
                <div class="quiz-box__q" id="quizQuestion"></div>
                <div id="quizOptions"></div>
                <button type="button" id="quizSubmit" class="toggle-btn" disabled>Periksa Jawaban</button>
                <div id="quizFeedback"></div>
                <button type="button" id="quizNext" class="toggle-btn" style="display:none;margin-top:10px;">Soal Berikutnya</button>
            </div>
            <div id="quizScore" style="margin-top:14px;font-weight:600;display:none;"></div>
        </div>

        <script id="quizData" type="application/json">{!! json_encode($praktikum->kuis, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}</script>
        <script>
        (function () {
            const soal = JSON.parse(document.getElementById('quizData').textContent);
            const qEl = document.getElementById('quizQuestion');
            const optWrap = document.getElementById('quizOptions');
            const submitBtn = document.getElementById('quizSubmit');
            const nextBtn = document.getElementById('quizNext');
            const feedback = document.getElementById('quizFeedback');
            const progress = document.getElementById('quizProgress');
            const scoreEl = document.getElementById('quizScore');

            let current = 0;
            let selected = null;
            let submitted = false;
            let benar = 0;

            function renderQuestion() {
                const s = soal[current];
                progress.textContent = 'Soal ' + (current + 1) + ' dari ' + soal.length;
                qEl.textContent = s.pertanyaan;
                optWrap.innerHTML = '';
                selected = null;
                submitted = false;
                submitBtn.disabled = true;
                submitBtn.style.display = 'inline-flex';
                nextBtn.style.display = 'none';
                feedback.innerHTML = '';

                s.opsi.forEach((op, i) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.textContent = op;
                    b.className = 'quiz-opt-static';
                    b.style.cssText = 'text-align:left;cursor:pointer;';
                    b.onclick = () => {
                        if (submitted) return;
                        selected = i;
                        submitBtn.disabled = false;
                        renderOptions(s);
                    };
                    optWrap.appendChild(b);
                });
            }

            function renderOptions(s) {
                Array.from(optWrap.children).forEach((b, i) => {
                    b.disabled = submitted;
                    b.classList.remove('correct');
                    b.style.borderColor = '';
                    if (submitted && i === s.jawaban) b.classList.add('correct');
                    if (!submitted && i === selected) b.style.borderColor = 'var(--accent)';
                });
            }

            submitBtn.addEventListener('click', () => {
                if (selected === null) return;
                const s = soal[current];
                submitted = true;
                const ok = selected === s.jawaban;
                if (ok) benar++;
                feedback.innerHTML = '<div class="quiz-answer">' + (ok ? 'Benar! ' : 'Belum tepat. ') + (s.penjelasan ?? '') + '</div>';
                renderOptions(s);
                submitBtn.style.display = 'none';

                if (current < soal.length - 1) {
                    nextBtn.style.display = 'inline-flex';
                } else {
                    scoreEl.style.display = 'block';
                    scoreEl.textContent = 'Skor akhir: ' + benar + ' / ' + soal.length;
                }
            });

            nextBtn.addEventListener('click', () => {
                current++;
                renderQuestion();
            });

            renderQuestion();
        })();
        </script>
    @endif
</div>
@endsection
