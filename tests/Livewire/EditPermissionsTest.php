<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Permissions\Edit as EditPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;

class EditPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private $userWithEditPermissionAccess;
    private $userWithAdminRole;
    private $userWithNoPermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWithEditPermissionAccess = User::factory()->create();
        $editPermission = new Permission(['name' => 'edit_permission']);
        $editPermission->save();

        $this->userWithEditPermissionAccess->permissions()->attach($editPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = new Role(['name' => 'admin']);
        $adminRole->save();

        $adminRole->permissions()->attach($editPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();
    }

    /** @test */
    public function renders_sucessfully()
    {
        // $this->actingAs($this->userWithEditPermissionAccess);
        // Livewire::test(EditPermission::class)
        //     ->assertSeeHtml('<x-input id="permission_name" type="text" class="pt-2 block w-full" wire:model="state.permission_name" value="{{ $state["permission_name"] }}"/>')
        //     ->assertStatus(200);
    }

    /** @test */
    public function edits_permission_with_add_permission_permission_granted()
    {
        // $this->actingAs($this->userWithEditPermissionAccess);


        // Livewire::test(EditPermission::class)
        //     ->set('state', ['permission_name' => 'test_permissions'])
        //     ->call('addPermission')
        //     ->assertDispatched('toastr',
        //     [
        //         'type' => 'confirm',
        //         'message' => trans('notifications.successfull_creation', ['entity' => 'Permission'])
        //     ])
        //     ->assertDispatched('permission-added');
    }
}
