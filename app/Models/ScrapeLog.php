<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapeLog extends Model
{
    protected $fillable = [
        'source',
        'status',
        'started_at',
        'completed_at',
        'records_imported',
        'records_skipped',
        'records_failed',
        'error_message',
        'scrape_date',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'scrape_date' => 'date',
            'records_imported' => 'integer',
            'records_skipped' => 'integer',
            'records_failed' => 'integer',
        ];
    }
}
