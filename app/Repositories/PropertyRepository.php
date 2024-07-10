<?php

namespace App\Repositories;

use App\Contract\PropertyRepositoryInterface;
use App\Models\Property;

class PropertyRepository extends BaseRepository implements PropertyRepositoryInterface
{
   public function __construct(Property $property)
   {
        parent::__construct($property);
   }
}