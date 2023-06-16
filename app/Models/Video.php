<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    //one to many
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    //one to one
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
