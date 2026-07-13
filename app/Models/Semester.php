<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'prodi_id', 'nomor', 'nama', 'slug',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function matakuliahs(): HasMany
    {
        return $this->hasMany(Matakuliah::class)->orderBy('urutan');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
