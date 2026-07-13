<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode', 'nama', 'slug', 'plot_label', 'accent_color', 'deskripsi',
    ];

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class)->orderBy('nomor');
    }

    public function materis(): HasMany
    {
        return $this->hasMany(Materi::class)->orderBy('pertemuan_ke');
    }

    public function praktikums(): HasMany
    {
        return $this->hasMany(Praktikum::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
