<?php

namespace App\Contract;

use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

interface PropertyRepositoryInterface extends BaseRepositoryInterface
{
    public function createProperty(array $attributes, Address $address): Model;
}
