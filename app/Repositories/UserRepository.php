<?php

namespace App\Repositories;

use App\Contract\UserRepositoryInterface as ContractUserRepositoryInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements ContractUserRepositoryInterface
{
   public function __construct(User $user)
   {
        parent::__construct($user);
   }
}