<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = [
        'title',
        'url',
        'source',
        'published_at',
        'summary',
        'commodity_tags',
        'is_relevant',
    ];

    protected function casts(): array
    {
        return [
            'published_at'   => 'datetime',
            'commodity_tags' => 'array',
            'is_relevant'    => 'boolean',
        ];
    }
}
