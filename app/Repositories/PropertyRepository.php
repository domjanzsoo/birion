<?php

namespace App\Repositories;

use App\Contract\PropertyRepositoryInterface;
use App\Models\Property;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

class PropertyRepository extends BaseRepository implements PropertyRepositoryInterface
{
   public function __construct(Property $property)
   {
        parent::__construct($property);
   }

   public function createProperty(array $attributes, Address $address): Model
   {
      $property = new Property($attributes);

      $property->address()->associate($address)->save();

      return $property;
   }
}