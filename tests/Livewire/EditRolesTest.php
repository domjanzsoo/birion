<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Roles\Edit as EditRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;

class EditRolesTest extends TestCase
{
    use RefreshDatabase;

    private $userWithEditRoleAccess;
    private $userWithAdminRole;
    private $userWithNoPermission;
    private $roleToUpdate;

    const INITIAL_ROLE_NAME = 'edit_me';
    const NEW_ROLE_NAME = 'name_updated';

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleToUpdate = Role::create(['name' => self::INITIAL_ROLE_NAME]);

        $this->userWithEditRoleAccess = User::factory()->create();
        $editPermission = Permission::create(['name' => 'edit_role']);
        $this->userWithEditRoleAccess->permissions()->attach($editPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->permissions()->attach($editPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();
    }

    /** @test */
    public function renders_sucessfully(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);
        
        Livewire::test(EditRole::class)
            ->assertSeeHtml('test-id="edit_roles" wire:submit="save"')
            ->assertStatus(200);
    }

    /** @test */
    public function edits_role_with_edit_role_permission_granted(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);

        Livewire::test(EditRole::class)
            ->call('handleEditModalData', $this->roleToUpdate->id, 'role')
            ->set('state.role_name', self::NEW_ROLE_NAME)
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'Role'])
            ])
            ->assertDispatched('role-edited');

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::NEW_ROLE_NAME);
    }

    /** @test */
    public function handle_permissions_of_role(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);

        $permissions = [
            1 => [
                'name' => 'test1',
                'selected' => true
            ],
            2 => [
                'name' => 'test2',
                'selected' => false
            ],
            3 => [
                'name' => 'test3',
                'selected' => true
            ]
        ];

        Livewire::test(EditRole::class)
            ->call('handlePermissions', $permissions)
            ->assertSet('state.permission_update', [1, 3]);
    }

    /** @test */
    public function handle_edit_modal(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);

        Livewire::test(EditRole::class)
            ->call('handleEditModalData', $this->roleToUpdate->id, 'role')
            ->assertSet('state.role_name', $this->roleToUpdate->name)
            ->assertSet('state.id', $this->roleToUpdate->id);
    }

     /** @test */
     public function edits_role_and_updates_roles_permissions_with_edit_role_permission_granted(): void
     {
         $this->actingAs($this->userWithEditRoleAccess);
 
         $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);

         $permission1 = Permission::create(['name' => 'test1']);
         $permission2 = Permission::create(['name' => 'test2']);
 
         Livewire::test(EditRole::class)
             ->set('state', ['role_name' => self::NEW_ROLE_NAME, 'permission_update' => [$permission1->id, $permission2->id], 'selected_permissions' => [], 'id' => $this->roleToUpdate->id])
             ->call('save')
             ->assertDispatched('toastr',
             [
                 'type' => 'confirm',
                 'message' => trans('notifications.successfull_update', ['entity' => 'Role'])
             ])
             ->assertDispatched('role-edited');
 
         $this->assertEquals(self::NEW_ROLE_NAME, Role::find($this->roleToUpdate->id)->name);
         $this->assertEquals(2, Role::find($this->roleToUpdate->id)->permissions()->count());
     }

    /** @test */
    public function edit_role_with_edit_role_permission_granted_through_role(): void
    {
        $this->actingAs($this->userWithAdminRole);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);
    
        Livewire::test(EditRole::class)
            ->call('handleEditModalData', $this->roleToUpdate->id, 'role')
            ->set('state.role_name', self::NEW_ROLE_NAME)
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'Role'])
            ])
            ->assertDispatched('role-edited');

        $this->assertEquals(self::NEW_ROLE_NAME, Role::find($this->roleToUpdate->id)->name);
    }

    /** @test */
    public function edit_role_with_no_permission_fails(): void
    {
        $this->actingAs($this->userWithNoPermission);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);

        Livewire::test(EditRole::class)
            ->call('handleEditModalData', $this->roleToUpdate->id, 'role')
            ->set('state.role_name', self::NEW_ROLE_NAME)
            ->call('save')
            ->assertForbidden();

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);
    }

    /** @test */
    public function fails_with_missing_role_name(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);

        Livewire::test(EditRole::class)
            ->call('handleEditModalData', $this->roleToUpdate->id, 'role')
            ->set('state.role_name', '')
            ->call('save')
            ->assertHasErrors(['state.role_name' => 'required']);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);
    }

    /** @test */
    public function fails_with_already_existing_role_name(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);

        Livewire::test(EditRole::class)
            ->call('handleEditModalData', $this->roleToUpdate->id, 'role')
            ->set('state.role_name', 'admin')
            ->call('save')
            ->assertHasErrors(['state.role_name' => 'unique']);

        $this->assertEquals(Role::find($this->roleToUpdate->id)->name, self::INITIAL_ROLE_NAME);
    }
}
