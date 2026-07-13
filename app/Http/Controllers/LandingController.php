<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Prodi;

class LandingController extends Controller
{
    /**
     * Tampilan awal (landing page) AgriVerse — bisa diakses tanpa login.
     */
    public function index()
    {
        $prodis = Prodi::withCount(['materis', 'praktikums'])->get();

        $totalMateri = Materi::count();
        $totalPraktikum = Praktikum::count();

        return view('welcome', [
            'prodis' => $prodis,
            'totalMateri' => $totalMateri,
            'totalPraktikum' => $totalPraktikum,
        ]);
    }
}
