<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturePage extends Model
{
    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'order',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function sections()
    {
        return $this->hasMany(FeaturePageSection::class)->orderBy('order');
    }
}
