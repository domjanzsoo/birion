<?php

namespace Tests\Livewire;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;

class MainListTestCase extends TestCase
{
    use RefreshDatabase;

    protected $entityModel;
    
    protected $userWithViewAccess;
    protected $userWithEditAccess;
    protected $userWithAddAccess;
    protected $userWithDeleteAccess;
    protected $userWithViewAccessInRole;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->entityModel) {
            for ($i = 0; $i < count($this->entityModel['fields']); $i++ ) {
                $this->entityModel['className']::create($this->entityModel['fields'][$i]);
            }
        }

        $entity = $this->entityModel ? $this->entityModel['entity'] : 'entity';

        $permissionToView = Permission::create(['name' => 'view_' . $entity . 's']);
        $permissionToAdd = Permission::create(['name' => 'add_' . $entity]);
        $permissionToEdit = Permission::create(['name' => 'edit_' . $entity]);
        $permissionToDelete = Permission::create(['name' => 'delete_' . $entity]);

        $this->userWithDeleteAccess = User::factory()->create();
        $this->userWithDeleteAccess->permissions()->attach($permissionToView);
        $this->userWithDeleteAccess->permissions()->attach($permissionToDelete);

        $this->userWithViewAccess = User::factory()->create();
        $this->userWithViewAccess->permissions()->attach($permissionToView);

        $this->userWithAddAccess = User::factory()->create();
        $this->userWithAddAccess->permissions()->attach($permissionToAdd);

        $this->userWithEditAccess = User::factory()->create();
        $this->userWithEditAccess->permissions()->attach($permissionToEdit);

        $role = Role::create(['name' => 'test']);
        $role->permissions()->attach($permissionToView);
        
        $this->userWithViewAccessInRole = User::factory()->create();
        $this->userWithViewAccessInRole->roles()->attach($role);
    }
}