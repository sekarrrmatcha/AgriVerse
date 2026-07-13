<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriProgress extends Model
{
    use HasFactory;

    protected $table = 'materi_progresses';

    protected $fillable = ['user_id', 'materi_id', 'selesai_at'];

    protected $casts = [
        'selesai_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }
}
