<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PredictionBatch extends Model
{
    protected $fillable = [
        'status',
        'total_pairs',
        'processed_pairs',
        'total_predictions',
        'ai_insight',
        'ai_insight_generated_at',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'ai_insight_generated_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'prediction_batch_id');
    }
}
