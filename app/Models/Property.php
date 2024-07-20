<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $attributes = [
        'offerType' => 'sale'
    ];

    protected $fillable = [
        'address', 'roomNumber', 'heating', 'description', 'size', 'price', 'offerType'
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
