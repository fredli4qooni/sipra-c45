<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'angkatan',
        'jenis_kelamin',
        'jalur_masuk',
        'email',
        'no_hp',
        'alamat',
        'tinggal_dengan',
        'status_mahasiswa',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dataAkademiks(): HasMany
    {
        return $this->hasMany(DataAkademik::class, 'mahasiswa_id')->orderBy('semester', 'asc');
    }

    public function latestAkademik(): HasOne
    {
        return $this->hasOne(DataAkademik::class, 'mahasiswa_id')->latestOfMany('semester');
    }

    public function prediksis(): HasMany
    {
        return $this->hasMany(Prediksi::class, 'mahasiswa_id')->latest();
    }

    public function latestPrediksi(): HasOne
    {
        return $this->hasOne(Prediksi::class, 'mahasiswa_id')->latestOfMany();
    }
}
