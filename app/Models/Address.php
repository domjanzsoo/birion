<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = [
        'municipality_sub_division', 'municipality_secondary_sub_division', 'municipality', 'country', 'post_code', 'lat', 'lon',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
