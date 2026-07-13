<?php

namespace App\Http\Controllers;

use App\Models\MateriProgress;
use App\Models\Materi;
use App\Models\Prodi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Daftar materi untuk satu program studi.
     */
    public function index(Request $request, Prodi $prodi)
    {
        $materis = $prodi->materis;
        $user = $request->user();

        $selesaiIds = $user
            ? $user->materiProgresses()->whereNotNull('selesai_at')->pluck('materi_id')->all()
            : [];

        return view('materi.index', [
            'prodi' => $prodi,
            'materis' => $materis,
            'selesaiIds' => $selesaiIds,
        ]);
    }

    /**
     * Detail satu pertemuan materi.
     */
    public function show(Request $request, Materi $materi)
    {
        $materi->load(['prodi', 'matakuliah.semester']);
        $selesai = $materi->isSelesaiUntuk($request->user());

        return view('materi.show', [
            'materi' => $materi,
            'prodi' => $materi->prodi,
            'matakuliah' => $materi->matakuliah,
            'semester' => $materi->matakuliah?->semester,
            'selesai' => $selesai,
        ]);
    }

    /**
     * Tandai / batalkan tanda selesai untuk materi tertentu.
     */
    public function toggle(Request $request, Materi $materi): RedirectResponse
    {
        $user = $request->user();

        $progress = MateriProgress::firstOrNew([
            'user_id' => $user->id,
            'materi_id' => $materi->id,
        ]);

        $progress->selesai_at = $progress->selesai_at ? null : now();
        $progress->save();

        return back();
    }
}
