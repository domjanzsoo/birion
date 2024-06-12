<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Permissions\Add as CreatePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;

class CreatePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private $userWithAddPermissionAccess;
    private $userWithAdminRole;
    private $userWithNoPermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWithAddPermissionAccess = User::factory()->create();
        $addPermission = new Permission(['name' => 'add_permission']);
        $addPermission->save();

        $this->userWithAddPermissionAccess->permissions()->attach($addPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = new Role(['name' => 'admin']);
        $adminRole->save();

        $adminRole->permissions()->attach($addPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();
    }

    /** @test */
    public function renders_sucessfully()
    {
        $this->actingAs($this->userWithAddPermissionAccess);
        Livewire::test(CreatePermission::class)
            ->assertSee(trans('permissions.add'))
            ->assertSee(trans('permissions.add_full'))
            ->assertStatus(200);
    }

    /** @test */
    public function creates_permission_with_add_permission_permission_granted()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(1, Permission::count());

        Livewire::test(CreatePermission::class)
            ->set('state', ['permission_name' => 'test_permissions'])
            ->call('addPermission')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'Permission'])
            ])
            ->assertDispatched('permission-added');

        $this->assertEquals(2, Permission::count());
    }

    /** @test */
    public function creates_permission_with_add_permission_permission_granted_through_role()
    {
        $this->actingAs($this->userWithAdminRole);

        $this->assertEquals(1, Permission::count());

        Livewire::test(CreatePermission::class)
            ->set('state', ['permission_name' => 'test_permissions'])
            ->call('addPermission')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'Permission'])
            ])
            ->assertDispatched('permission-added');

        $this->assertEquals(2, Permission::count());
    }

    /** @test */
    public function creates_permission_with_no_permission_fails()
    {
        $this->actingAs($this->userWithNoPermission);

        $this->assertEquals(1, Permission::count());

        Livewire::test(CreatePermission::class)
            ->set('state', ['permission_name' => 'test_permissions'])
            ->call('addPermission')
            ->assertForbidden();

        $this->assertEquals(1, Permission::count());
    }

    /** @test */
    public function fails_with_missing_permission_name()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(1, Permission::count());

        Livewire::test(CreatePermission::class)
            ->set('state', ['permission_name' => ''])
            ->call('addPermission')
            ->assertHasErrors(['state.permission_name' => 'required']);

        $this->assertEquals(1, Permission::count());
    }

        /** @test */
        public function fails_with_permission_already_existing()
        {
            $this->actingAs($this->userWithAddPermissionAccess);

            $existingPermission = new Permission(['name' => 'already_existing']);
            $existingPermission->save();

            $this->assertEquals(2, Permission::count());
    
            Livewire::test(CreatePermission::class)
                ->set('state', ['permission_name' => $existingPermission->name])
                ->call('addPermission')
                ->assertHasErrors(['state.permission_name' => 'unique']);
    
            $this->assertEquals(2, Permission::count());
        }
}
