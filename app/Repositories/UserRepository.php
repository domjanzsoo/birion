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

   public function createUser(array $fields, array $permissions = [], array $roles = []): User
   {
      $user = $this->create($fields);

      if (count($permissions) > 0) {
         $user->permissions()->attach($permissions);
      }

      if (count($roles) > 0) {
         $user->roles()->attach($roles);
      }

      $user->save();

      return $user;
   }
}