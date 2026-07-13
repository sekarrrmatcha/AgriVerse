<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PraktikumProgress extends Model
{
    use HasFactory;

    protected $table = 'praktikum_progresses';

    protected $fillable = [
        'user_id', 'praktikum_id', 'langkah_selesai', 'kuis_benar', 'selesai_at',
    ];

    protected $casts = [
        'langkah_selesai' => 'array',
        'kuis_benar' => 'boolean',
        'selesai_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function praktikum(): BelongsTo
    {
        return $this->belongsTo(Praktikum::class);
    }
}
