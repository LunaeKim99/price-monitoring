<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    protected $fillable = [
        'commodity_id',
        'region_id',
        'predicted_price',
        'predicted_date',
        'confidence',
        'model_name',
        'prediction_batch_id',
    ];

    protected function casts(): array
    {
        return [
            'predicted_date' => 'date',
        ];
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function predictionBatch(): BelongsTo
    {
        return $this->belongsTo(PredictionBatch::class);
    }
}
