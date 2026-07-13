<?php

namespace App\Http\Controllers;

use App\Models\Matakuliah;
use Illuminate\Http\Request;

class MatakuliahController extends Controller
{
    /**
     * Halaman mata kuliah: modul materi dan praktikum/kuis.
     */
    public function show(Request $request, Matakuliah $matakuliah)
    {
        $matakuliah->load([
            'semester.prodi',
            'materis',
            'praktikums',
        ]);

        $user = $request->user();

        $materiSelesaiIds = $user
            ? $user->materiProgresses()->whereNotNull('selesai_at')->pluck('materi_id')->all()
            : [];

        $praktikumSelesaiIds = $user
            ? $user->praktikumProgresses()->whereNotNull('selesai_at')->pluck('praktikum_id')->all()
            : [];

        return view('matakuliah.show', [
            'matakuliah' => $matakuliah,
            'semester' => $matakuliah->semester,
            'prodi' => $matakuliah->semester->prodi,
            'materiSelesaiIds' => $materiSelesaiIds,
            'praktikumSelesaiIds' => $praktikumSelesaiIds,
        ]);
    }
}
