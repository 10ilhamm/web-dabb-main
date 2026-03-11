<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Virtual3dRoom extends Model
{
    protected $table = 'virtual3d_rooms';
    
    protected $fillable = [
        'feature_id',
        'name',
        'description',
        'thumbnail_path',
        'wall_color',
        'floor_color',
        'ceiling_color',
        'door_link_type',
        'door_target',
        'door_label',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function media()
    {
        return $this->hasMany(Virtual3dMedia::class, 'virtual3d_room_id');
    }
}
