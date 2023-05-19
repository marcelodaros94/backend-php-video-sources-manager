<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    //one to many inverse
    public function Video()
    {
        return $this->belongsTo(Video::class);
    }
}
