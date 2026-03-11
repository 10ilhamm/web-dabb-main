<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualBookPage extends Model
{
    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'content',
        'content_en',
        'image',
        'image_height',
        'page_number',
        'is_cover',
        'is_back_cover',
        'order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'is_back_cover' => 'boolean',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
