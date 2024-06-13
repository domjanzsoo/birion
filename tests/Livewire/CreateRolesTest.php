<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Roles\Add as CreateRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;

class CreateRolesTest extends TestCase
{
    use RefreshDatabase;

    private $userWithAddRoleAccess;
    private $userWithViewRoleAccess;
    private $userWithAdminRole;
    private $userWithNoPermission;

    public $roleCount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWithViewRoleAccess = User::factory()->create();
        $viewPermission = Permission::create(['name' => 'view_role']);
        $this->userWithViewRoleAccess->permissions()->attach($viewPermission);

        $this->userWithAddRoleAccess = User::factory()->create();
        $addPermission = Permission::create(['name' => 'add_role']);
        $this->userWithAddRoleAccess->permissions()->attach($addPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->permissions()->attach($addPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();

        $this->roleCount = Role::count();
    }

    /** @test */
    public function renders_sucessfully()
    {
        $this->actingAs($this->userWithAddRoleAccess);
        Livewire::test(CreateRole::class)
            ->assertSee(trans('roles.add'))
            ->assertSee(trans('roles.add_full'))
            ->assertStatus(200);
    }

    /** @test */
    public function creates_role_with_add_role_permission_granted()
    {
        $this->actingAs($this->userWithAddRoleAccess);

        $this->assertEquals($this->roleCount, Role::Count());

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => 'test_role', 'permissions' => []])
            ->call('addRole')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'Role'])
            ])
            ->assertDispatched('role-added');

        $this->assertEquals($this->roleCount + 1, Role::count());
    }

    /** @test */
    public function creates_role_with_add_permission_permission_granted_through_role()
    {
        $this->actingAs($this->userWithAdminRole);

        $this->assertEquals($this->roleCount, Role::count());

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => 'test_role', 'permissions' => []])
            ->call('addRole')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'Role'])
            ])
            ->assertDispatched('role-added');

        $this->assertEquals($this->roleCount + 1, Role::count());
    }

    /** @test */
    public function creates_role_with_no_permission_fails()
    {
        $this->actingAs($this->userWithNoPermission);

        $this->assertEquals($this->roleCount, Role::count());

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => 'test_role'])
            ->call('addRole')
            ->assertForbidden();

        $this->assertEquals($this->roleCount, Role::count());
    }

    /** @test */
    public function creates_role_with_view_permission_only_fails()
    {
        $this->actingAs($this->userWithViewRoleAccess);

        $this->assertEquals($this->roleCount, Role::count());

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => 'test_role'])
            ->call('addRole')
            ->assertForbidden();

        $this->assertEquals($this->roleCount, Role::count());
    }

    /** @test */
    public function fails_with_missing_role_name()
    {
        $this->actingAs($this->userWithAddRoleAccess);

        $this->assertEquals($this->roleCount, Role::count());

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => '', 'permissions' => []])
            ->call('addRole')
            ->assertHasErrors(['state.role_name' => 'required']);

        $this->assertEquals($this->roleCount, Role::count());
    }

    /** @test */
    public function fails_with_role_already_existing()
    {
        $this->actingAs($this->userWithAddRoleAccess);

        $this->assertEquals($this->roleCount, Role::count());

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => $this->userWithAdminRole->roles()->first()->name, 'permissions' => []])
            ->call('addRole')
            ->assertHasErrors(['state.role_name' => 'unique']);

        $this->assertEquals($this->roleCount, Role::count());
    }

    /** @test */
    public function handle_permissions_of_role()
    {
        $this->actingAs($this->userWithAddRoleAccess);

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

        Livewire::test(CreateRole::class)
            ->call('handlePermissions', $permissions)
            ->assertSet('state.permissions', [1, 3]);
    }

    /** @test */
    public function create_role_with_permissions()
    {
        $this->actingAs($this->userWithAddRoleAccess);

        $permission1 = Permission::create(['name' => 'test1']);
        $permission2 = Permission::create(['name' => 'test2']);

        Livewire::test(CreateRole::class)
            ->set('state', ['role_name' => 'test_role', 'permissions' => [$permission1->id, $permission2->id]])
            ->call('addRole')
            ->assertDispatched('role-added');

        $this->assertEquals(['test1', 'test2'], Role::where('name', 'test_role')->first()->permissions()->pluck('name')->toArray());
    }
}
