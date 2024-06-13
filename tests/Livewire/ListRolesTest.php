<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Roles\All as ListRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\Permission;

class ListRolesTest extends TestCase
{
    use RefreshDatabase;

    private $userWithViewRolesAccess;
    private $userWithEditRoleAccess;
    private $userWithAddRoleAccess;
    private $userWithDeleteRoleAccess;
    private $userWithViewRoleInRole;
    private $roleCount;

    protected function setUp(): void
    {
        parent::setUp();

        for ($i = 0; $i < 10; $i++) {
            Role::create(['name' => 'test-' . $i]);
        }

        $permissionToView = Permission::create(['name' => 'view_roles']);

        $permissionToAdd = Permission::create(['name' => 'add_role']);

        $permissionToEdit = Permission::create(['name' => 'edit_role']);

        $permissionToDelete = Permission::create(['name' => 'delete_role']);

        $this->userWithDeleteRoleAccess = User::factory()->create();
        $this->userWithDeleteRoleAccess->permissions()->attach($permissionToView);
        $this->userWithDeleteRoleAccess->permissions()->attach($permissionToDelete);


        $this->userWithViewRolesAccess = User::factory()->create();
        $this->userWithViewRolesAccess->permissions()->attach($permissionToView);

        $this->userWithAddRoleAccess = User::factory()->create();
        $this->userWithAddRoleAccess->permissions()->attach($permissionToAdd);

        $this->userWithEditRoleAccess = User::factory()->create();
        $this->userWithEditRoleAccess->permissions()->attach($permissionToEdit);

        $role = Role::create(['name' => 'view_role']);
        $role->permissions()->attach($permissionToView);
        
        $this->userWithViewRoleInRole = User::factory()->create();
        $this->userWithViewRoleInRole->roles()->attach($role);

        $this->roleCount = Role::count();
    }

    /** @test */
    public function renders_sucessfully_with_view_permission_granted(): void
    {
        $this->actingAs($this->userWithViewRolesAccess);

        $pageCount = ceil($this->roleCount / 5);

        Livewire::test(ListRoles::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_add_role_permission_granted(): void
    { 
        $this->actingAs($this->userWithAddRoleAccess);

        $pageCount = ceil(Role::count() / 5);

        Livewire::test(ListRoles::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_edit_role_permission_granted(): void
    { 
        $this->actingAs($this->userWithEditRoleAccess);

        $pageCount = ceil($this->roleCount / 5);

        Livewire::test(ListRoles::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_view_role_permission_granted_by_role(): void
    { 
        $this->actingAs($this->userWithViewRoleInRole);

        $pageCount = ceil($this->roleCount / 5);

        Livewire::test(ListRoles::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function fails_to_select_roles_checked_with_view_role_permission_only(): void
    {
        $this->actingAs($this->userWithViewRolesAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_roles_checked_with_add_roles_permission_only(): void
    {
        $this->actingAs($this->userWithAddRoleAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_roles_checked_with_edit_role_permission_only(): void
    {
        $this->actingAs($this->userWithEditRoleAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function selects_permissions_checked_with_delete_role_permission(): void
    {
        $this->actingAs($this->userWithDeleteRoleAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertSet('deleteButtonAccess', true)
            ->assertSet('rolesToDelete', ['1' => true]);
    }

    /** @test */
    public function deselects_roles_unchecked(): void
    {
        $this->actingAs($this->userWithDeleteRoleAccess);

        Livewire::test(ListRoles::class)
            ->set('deleteButtonAccess', true)
            ->call('processRoleCheck', 'role', ['1' => false])
            ->assertSet('deleteButtonAccess', false)
            ->assertSet('rolesToDelete', ['1' => false]);
    }

    /** @test */
    public function fails_to_delete_checked_roels_with_view_roles_permission_only(): void
    {
        $this->actingAs($this->userWithViewRolesAccess);

        Livewire::test(ListRoles::class)
            ->call('deleteRoles')
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_delete_checked_roles_if_no_role_is_passed(): void
    {
        $this->actingAs($this->userWithDeleteRoleAccess);

        Livewire::test(ListRoles::class)
            ->set('rolesToDelete', [])
            ->call('deleteRoles')
            ->assertDispatched('toastr',
            [
                'type' => 'error',
                'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'Role', 'action' => 'delete'])
            ]);

        $this->assertEquals($this->roleCount, Role::count());
    }

    /** @test */
    public function successfully_deletes_checked_roles(): void
    {
        $this->actingAs($this->userWithDeleteRoleAccess);

        $firstRoleId= Role::first()->id;

        Livewire::test(ListRoles::class)
            ->set('rolesToDelete', [ $firstRoleId => true, $firstRoleId + 1 => true])
            ->call('deleteRoles')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_deletion', ['entity' => 'Role'])
            ])
            ->assertSet('rolesToDelete', []);

        $this->assertEquals($this->roleCount - 2, Role::count());
    }
}