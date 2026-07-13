<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Beranda: tampilkan prodi user dan pilihan semester.
     */
    public function index(Request $request)
    {
        $user = $request->user()->load('prodi');
        $prodi = $user->prodi;

        $semesters = $prodi
            ? $prodi->semesters()->withCount('matakuliahs')->get()
            : collect();

        $stats = $this->statsUntukProdi($prodi);

        return view('dashboard', [
            'user' => $user,
            'prodi' => $prodi,
            'semesters' => $semesters,
            'stats' => $stats,
            'progresPersen' => $user->progresPersen(),
        ]);
    }

    private function statsUntukProdi($prodi): array
    {
        if (! $prodi) {
            return ['semester' => 0, 'matakuliah' => 0, 'materi' => 0, 'praktikum' => 0];
        }

        $matakuliahIds = \App\Models\Matakuliah::whereHas(
            'semester',
            fn ($q) => $q->where('prodi_id', $prodi->id)
        )->pluck('id');

        return [
            'semester' => $prodi->semesters()->count(),
            'matakuliah' => $matakuliahIds->count(),
            'materi' => \App\Models\Materi::whereIn('matakuliah_id', $matakuliahIds)->count(),
            'praktikum' => \App\Models\Praktikum::whereIn('matakuliah_id', $matakuliahIds)->count(),
        ];
    }
}
