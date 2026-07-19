<?php

namespace App\Models;

// Illuminate\Foundation\Auth\User as Authenticatable
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nim',
        'prodi_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function materiProgresses(): HasMany
    {
        return $this->hasMany(MateriProgress::class);
    }

    public function praktikumProgresses(): HasMany
    {
        return $this->hasMany(PraktikumProgress::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Persentase progres belajar (materi + praktikum) untuk prodi user.
     */
    public function progresPersen(): int
    {
        if (! $this->prodi_id) {
            return 0;
        }

        $matakuliahIds = Matakuliah::whereHas('semester', fn ($q) => $q->where('prodi_id', $this->prodi_id))
            ->pluck('id');

        $totalMateri = Materi::whereIn('matakuliah_id', $matakuliahIds)->count();
        $totalPraktikum = Praktikum::whereIn('matakuliah_id', $matakuliahIds)->count();
        $total = $totalMateri + $totalPraktikum;

        if ($total === 0) {
            return 0;
        }

        $materiIds = Materi::whereIn('matakuliah_id', $matakuliahIds)->pluck('id');
        $praktikumIds = Praktikum::whereIn('matakuliah_id', $matakuliahIds)->pluck('id');

        $selesai = $this->materiProgresses()->whereIn('materi_id', $materiIds)->whereNotNull('selesai_at')->count()
            + $this->praktikumProgresses()->whereIn('praktikum_id', $praktikumIds)->whereNotNull('selesai_at')->count();

        return (int) round(($selesai / $total) * 100);
    }
}
