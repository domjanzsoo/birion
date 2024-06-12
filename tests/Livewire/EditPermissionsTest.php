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
    private $permissionToUpdate;

    const INITIAL_PERMISSION_NAME = 'edit_me';
    const NEW_PERMISSION_NAME = 'name_updated';

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionToUpdate = Permission::create(['name' => self::INITIAL_PERMISSION_NAME]);

        $this->userWithEditPermissionAccess = User::factory()->create();
        $editPermission = Permission::create(['name' => 'edit_permission']);
        $this->userWithEditPermissionAccess->permissions()->attach($editPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);

        $adminRole->permissions()->attach($editPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();
    }

    /** @test */
    public function renders_sucessfully(): void
    {
        $this->actingAs($this->userWithEditPermissionAccess);
        
        Livewire::test(EditPermission::class)
            ->assertSeeHtml('test-id="edit_permissions" wire:submit="save"')
            ->assertStatus(200);
    }

    /** @test */
    public function edits_permission_with_edit_permission_permission_granted(): void
    {
        $this->actingAs($this->userWithEditPermissionAccess);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);

        Livewire::test(EditPermission::class)
            ->call('handleEditModalData', $this->permissionToUpdate->id, 'permission')
            ->set("state.permission_name", self::NEW_PERMISSION_NAME)
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'Permission'])
            ])
            ->assertDispatched('permission-edited');

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::NEW_PERMISSION_NAME);
    }

    /** @test */
    public function edit_permission_with_edit_permission_permission_granted_through_role(): void
    {
        $this->actingAs($this->userWithAdminRole);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);
    
        Livewire::test(EditPermission::class)
            ->call('handleEditModalData', $this->permissionToUpdate->id, 'permission')
            ->set("state.permission_name", self::NEW_PERMISSION_NAME)
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'Permission'])
            ])
            ->assertDispatched('permission-edited');

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::NEW_PERMISSION_NAME);
    }

    /** @test */
    public function edit_permission_with_no_permission_fails(): void
    {
        $this->actingAs($this->userWithNoPermission);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);

        Livewire::test(EditPermission::class)
            ->call('handleEditModalData', $this->permissionToUpdate->id, 'permission')
            ->set("state.permission_name", self::NEW_PERMISSION_NAME)
            ->call('save')
            ->assertForbidden();

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);
    }

    /** @test */
    public function fails_with_missing_permission_name(): void
    {
        $this->actingAs($this->userWithEditPermissionAccess);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);

        Livewire::test(EditPermission::class)
            ->call('handleEditModalData', $this->permissionToUpdate->id, 'permission')
            ->set("state.permission_name", '')
            ->call('save')
            ->assertHasErrors(['state.permission_name' => 'required']);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);
    }

    /** @test */
    public function fails_with_already_existing_permission_name(): void
    {
        $this->actingAs($this->userWithEditPermissionAccess);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);

        Livewire::test(EditPermission::class)
            ->call('handleEditModalData', $this->permissionToUpdate->id, 'permission')
            ->set("state.permission_name", 'edit_permission')
            ->call('save')
            ->assertHasErrors(['state.permission_name' => 'unique']);

        $this->assertEquals(Permission::find($this->permissionToUpdate->id)->name, self::INITIAL_PERMISSION_NAME);
    }
}
