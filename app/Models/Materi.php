<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'prodi_id', 'matakuliah_id', 'pertemuan_ke', 'judul', 'slug',
        'capaian', 'pokok_bahasan', 'pendahuluan', 'tinjauan_pustaka',
    ];

    protected $casts = [
        'capaian' => 'array',
        'pokok_bahasan' => 'array',
        'tinjauan_pustaka' => 'array',
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
        return $this->hasMany(MateriProgress::class);
    }

    public function isSelesaiUntuk(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->progresses()
            ->where('user_id', $user->id)
            ->whereNotNull('selesai_at')
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
