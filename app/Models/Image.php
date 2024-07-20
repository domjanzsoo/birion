<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'imageable_type', 'file_route'];

    public function imagable()
    {
        return $this->morphTo();
    }
}
