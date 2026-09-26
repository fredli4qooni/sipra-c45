<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediksi extends Model
{
    use HasFactory;

    protected $table = 'prediksis';

    protected $fillable = [
        'mahasiswa_id',
        'model_id',
        'rule_id',
        'batch_id',
        'nim',
        'nama_mahasiswa',
        'semester',
        'input_params_json',
        'hasil_klasifikasi',
        'status_do',
        'confidence_score',
        'rekomendasi_akademik',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'input_params_json' => 'array',
            'confidence_score' => 'decimal:2',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(C45Model::class, 'model_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(C45Rule::class, 'rule_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PrediksiBatch::class, 'batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
