<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'room_number', 'heating', 'description', 'size',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
