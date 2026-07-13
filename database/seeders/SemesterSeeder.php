<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Prodi::all() as $prodi) {
            for ($i = 1; $i <= 4; $i++) {
                Semester::updateOrCreate(
                    ['prodi_id' => $prodi->id, 'nomor' => $i],
                    [
                        'nama' => 'Semester '.$i,
                        'slug' => strtolower($prodi->kode).'-semester-'.$i,
                    ]
                );
            }
        }
    }
}
