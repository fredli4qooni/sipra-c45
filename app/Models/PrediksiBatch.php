<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrediksiBatch extends Model
{
    use HasFactory;

    protected $table = 'prediksi_batches';

    protected $fillable = [
        'batch_code',
        'file_name',
        'model_id',
        'total_records',
        'total_rendah',
        'total_sedang',
        'total_tinggi',
        'created_by',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(C45Model::class, 'model_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prediksis(): HasMany
    {
        return $this->hasMany(Prediksi::class, 'batch_id');
    }
}
