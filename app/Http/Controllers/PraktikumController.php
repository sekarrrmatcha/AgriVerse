<?php

namespace App\Http\Controllers;

use App\Models\PraktikumProgress;
use App\Models\Praktikum;
use App\Models\Prodi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PraktikumController extends Controller
{
    /**
     * Daftar modul praktikum untuk satu program studi.
     */
    public function index(Request $request, Prodi $prodi)
    {
        $praktikums = $prodi->praktikums;
        $user = $request->user();

        $selesaiIds = $user
            ? $user->praktikumProgresses()->whereNotNull('selesai_at')->pluck('praktikum_id')->all()
            : [];

        return view('praktikum.index', [
            'prodi' => $prodi,
            'praktikums' => $praktikums,
            'selesaiIds' => $selesaiIds,
        ]);
    }

    /**
     * Detail satu modul praktikum, lengkap dengan status langkah & kuis.
     */
    public function show(Request $request, Praktikum $praktikum)
    {
        $praktikum->load(['prodi', 'matakuliah.semester']);
        $progress = $praktikum->progressUntuk($request->user());

        return view('praktikum.show', [
            'praktikum' => $praktikum,
            'prodi' => $praktikum->prodi,
            'matakuliah' => $praktikum->matakuliah,
            'semester' => $praktikum->matakuliah?->semester,
            'progress' => $progress,
            'langkahSelesai' => $progress?->langkah_selesai ?? [],
            'selesai' => (bool) ($progress?->selesai_at !== null),
        ]);
    }

    /**
     * Simpan progres langkah kerja yang sudah dicentang (dipanggil lewat form/JS).
     */
    public function updateLangkah(Request $request, Praktikum $praktikum): RedirectResponse
    {
        $data = $request->validate([
            'langkah_selesai' => ['array'],
            'langkah_selesai.*' => ['integer'],
        ]);

        $user = $request->user();

        $progress = PraktikumProgress::firstOrNew([
            'user_id' => $user->id,
            'praktikum_id' => $praktikum->id,
        ]);

        $progress->langkah_selesai = $data['langkah_selesai'] ?? [];
        $progress->save();

        return back();
    }

    /**
     * Tandai / batalkan modul praktikum sebagai selesai (semua langkah tuntas).
     */
    public function toggle(Request $request, Praktikum $praktikum): RedirectResponse
    {
        $user = $request->user();

        $progress = PraktikumProgress::firstOrNew([
            'user_id' => $user->id,
            'praktikum_id' => $praktikum->id,
        ]);

        $progress->selesai_at = $progress->selesai_at ? null : now();
        $progress->save();

        return back();
    }
}
