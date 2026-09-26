<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim_nip',
        'phone',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    // Role Helper Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProdi(): bool
    {
        return $this->role === 'prodi';
    }

    public function isDosenPa(): bool
    {
        return $this->role === 'dosen_pa';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'prodi' => 'Ketua Program Studi',
            'dosen_pa' => 'Dosen PA',
            'mahasiswa' => 'Mahasiswa',
            default => ucfirst($this->role ?? 'User'),
        };
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name ?? 'User'));
        $first = mb_substr($words[0] ?? 'U', 0, 1);
        $second = isset($words[1]) ? mb_substr($words[1], 0, 1) : '';
        return strtoupper($first . $second) ?: 'U';
    }

    // Relationships
    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    public function modelsCreated(): HasMany
    {
        return $this->hasMany(C45Model::class, 'created_by');
    }

    public function prediksis(): HasMany
    {
        return $this->hasMany(Prediksi::class, 'created_by');
    }
}
