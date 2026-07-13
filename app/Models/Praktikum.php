<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Praktikum extends Model
{
    use HasFactory;

    protected $fillable = [
        'prodi_id', 'matakuliah_id', 'kode', 'judul', 'slug', 'tingkat', 'durasi',
        'tujuan', 'alat', 'bahan', 'langkah', 'kuis',
    ];

    protected $casts = [
        'alat' => 'array',
        'bahan' => 'array',
        'langkah' => 'array',
        'kuis' => 'array',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(PraktikumProgress::class);
    }

    public function progressUntuk(?User $user): ?PraktikumProgress
    {
        if (! $user) {
            return null;
        }

        return $this->progresses()->where('user_id', $user->id)->first();
    }

    public function isSelesaiUntuk(?User $user): bool
    {
        $progress = $this->progressUntuk($user);

        return $progress && $progress->selesai_at !== null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
