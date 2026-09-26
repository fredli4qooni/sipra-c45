<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class C45Model extends Model
{
    use HasFactory;

    protected $table = 'c45_models';

    protected $fillable = [
        'nama_model',
        'deskripsi',
        'train_date',
        'split_ratio',
        'random_seed',
        'total_training_samples',
        'total_testing_samples',
        'target_attribute',
        'features_used',
        'accuracy',
        'precision',
        'recall',
        'specificity',
        'f1_score',
        'confusion_matrix_data',
        'entropy_gain_calculations',
        'tree_structure_json',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'train_date' => 'datetime',
            'features_used' => 'array',
            'confusion_matrix_data' => 'array',
            'entropy_gain_calculations' => 'array',
            'accuracy' => 'decimal:2',
            'precision' => 'decimal:2',
            'recall' => 'decimal:2',
            'specificity' => 'decimal:2',
            'f1_score' => 'decimal:2',
            'is_active' => 'boolean',
            'random_seed' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(C45Rule::class, 'model_id');
    }

    public function prediksis(): HasMany
    {
        return $this->hasMany(Prediksi::class, 'model_id');
    }

    /**
     * Scope to get the currently active model
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
