<?php

namespace App\Repositories;

use App\Contract\PermissionRepositoryInterface;
use App\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
   public function __construct(Permission $permission)
   {
        parent::__construct($permission);
   }
}