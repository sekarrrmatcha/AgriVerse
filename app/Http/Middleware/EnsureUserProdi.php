<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserProdi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->prodi_id) {
            return redirect()->route('dashboard')
                ->with('status', 'Silakan lengkapi program studi Anda terlebih dahulu.');
        }

        $prodi = $request->route('prodi');
        if ($prodi && $prodi->id !== $user->prodi_id) {
            abort(403, 'Anda tidak memiliki akses ke program studi ini.');
        }

        $semester = $request->route('semester');
        if ($semester && $semester->prodi_id !== $user->prodi_id) {
            abort(403, 'Anda tidak memiliki akses ke semester ini.');
        }

        $matakuliah = $request->route('matakuliah');
        if ($matakuliah) {
            $matakuliah->loadMissing('semester');
            if ($matakuliah->semester->prodi_id !== $user->prodi_id) {
                abort(403, 'Anda tidak memiliki akses ke mata kuliah ini.');
            }
        }

        $materi = $request->route('materi');
        if ($materi) {
            $materi->loadMissing('matakuliah.semester');
            if ($materi->matakuliah && $materi->matakuliah->semester->prodi_id !== $user->prodi_id) {
                abort(403, 'Anda tidak memiliki akses ke materi ini.');
            }
        }

        $praktikum = $request->route('praktikum');
        if ($praktikum) {
            $praktikum->loadMissing('matakuliah.semester');
            if ($praktikum->matakuliah && $praktikum->matakuliah->semester->prodi_id !== $user->prodi_id) {
                abort(403, 'Anda tidak memiliki akses ke praktikum ini.');
            }
        }

        return $next($request);
    }
}
