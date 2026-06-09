<?php

namespace App\Models;

use Database\Factories\PriceRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRecord extends Model
{
    /** @use HasFactory<PriceRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'region_id',
        'price',
        'recorded_date',
        'source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'recorded_date' => 'date',
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
}
