<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Matakuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id', 'urutan', 'kode', 'nama', 'slug', 'sks', 'deskripsi', 'format_laporan',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function materis(): HasMany
    {
        return $this->hasMany(Materi::class)->orderBy('pertemuan_ke');
    }

    public function praktikums(): HasMany
    {
        return $this->hasMany(Praktikum::class);
    }

    public function prodi(): HasOneThrough
    {
        return $this->hasOneThrough(
            Prodi::class,
            Semester::class,
            'id',
            'id',
            'semester_id',
            'prodi_id'
        );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
