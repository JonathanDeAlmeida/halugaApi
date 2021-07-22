<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PlaceImage;

class Place extends Model
{
    use HasFactory;

    public function Images()
    {
        // return $this->hasOne(PlaceImage::class);
        // return $this->belongsTo(PlaceImage::class, 'place_id');
        // return $this->hasMany(PlaceImage::class);
    }
}
