<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Daftar mata kuliah pada semester yang dipilih.
     */
    public function show(Request $request, Semester $semester)
    {
        $semester->load(['prodi', 'matakuliahs' => fn ($q) => $q->withCount(['materis', 'praktikums'])]);

        return view('semester.show', [
            'semester' => $semester,
            'prodi' => $semester->prodi,
            'matakuliahs' => $semester->matakuliahs,
        ]);
    }
}
