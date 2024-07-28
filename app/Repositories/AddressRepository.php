<?php

namespace App\Repositories;

use App\Contract\AddressRepositoryInterface as ContractAddressRepositoryInterface;
use App\Models\Address;

class AddressRepository extends BaseRepository implements ContractAddressRepositoryInterface
{
   public function __construct(Address $address)
   {
        parent::__construct($address);
   }
}