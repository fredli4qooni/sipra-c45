<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class C45Rule extends Model
{
    use HasFactory;

    protected $table = 'c45_rules';

    protected $fillable = [
        'model_id',
        'rule_code',
        'rule_text',
        'conditions_json',
        'decision',
        'confidence',
        'support_samples',
    ];

    protected function casts(): array
    {
        return [
            'conditions_json' => 'array',
            'confidence' => 'decimal:2',
            'support_samples' => 'integer',
        ];
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(C45Model::class, 'model_id');
    }

    public function prediksis(): HasMany
    {
        return $this->hasMany(Prediksi::class, 'rule_id');
    }
}
