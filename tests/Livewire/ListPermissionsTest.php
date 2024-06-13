<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Permissions\All as ListPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;

class ListPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private $userWithViewPermissionAccess;
    private $userWithEditPermissionAccess;
    private $userWithAddPermissionAccess;
    private $userWithDeletePermissionAccess;
    private $userWithViewPermissionInRole;

    protected function setUp(): void
    {
        parent::setUp();

        for ($i = 0; $i < 10; $i++) {
            Permission::create(['name' => 'test-' . $i]);
        }

        $permissionToView = Permission::create(['name' => 'view_permissions']);
        $permissionToAdd = Permission::create(['name' => 'add_permission']);
        $permissionToEdit = Permission::create(['name' => 'edit_permission']);
        $permissionToDelete = Permission::create(['name' => 'delete_permission']);

        $this->userWithDeletePermissionAccess = User::factory()->create();
        $this->userWithDeletePermissionAccess->permissions()->attach($permissionToView);
        $this->userWithDeletePermissionAccess->permissions()->attach($permissionToDelete);

        $this->userWithViewPermissionAccess = User::factory()->create();
        $this->userWithViewPermissionAccess->permissions()->attach($permissionToView);

        $this->userWithAddPermissionAccess = User::factory()->create();
        $this->userWithAddPermissionAccess->permissions()->attach($permissionToAdd);

        $this->userWithEditPermissionAccess = User::factory()->create();
        $this->userWithEditPermissionAccess->permissions()->attach($permissionToEdit);

        $role = Role::create(['name' => 'test']);
        $role->permissions()->attach($permissionToView);
        
        $this->userWithViewPermissionInRole = User::factory()->create();
        $this->userWithViewPermissionInRole->roles()->attach($role);
    }

    /** @test */
    public function renders_sucessfully_with_view_permission_granted(): void
    {
        $this->actingAs($this->userWithViewPermissionAccess);

        $pageCount = ceil(Permission::count() / 5);

        Livewire::test(ListPermissions::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_add_permission_granted(): void
    { 
        $this->actingAs($this->userWithAddPermissionAccess);

        $pageCount = ceil(Permission::count() / 5);

        Livewire::test(ListPermissions::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_edit_permission_granted(): void
    { 
        $this->actingAs($this->userWithEditPermissionAccess);

        $pageCount = ceil(Permission::count() / 5);

        Livewire::test(ListPermissions::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_edit_permission_granted_by_role(): void
    { 
        $this->actingAs($this->userWithViewPermissionInRole);

        $pageCount = ceil(Permission::count() / 5);

        Livewire::test(ListPermissions::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function fails_to_select_permissions_checked_with_view_permission(): void
    {
        $this->actingAs($this->userWithViewPermissionAccess);

        Livewire::test(ListPermissions::class)
            ->call('processPermissionCheck', 'permission', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_permissions_checked_with_add_permission(): void
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        Livewire::test(ListPermissions::class)
            ->call('processPermissionCheck', 'permission', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_permissions_checked_with_edit_permission(): void
    {
        $this->actingAs($this->userWithEditPermissionAccess);

        Livewire::test(ListPermissions::class)
            ->call('processPermissionCheck', 'permission', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function selects_permissions_checked_with_delete_permission(): void
    {
        $this->actingAs($this->userWithDeletePermissionAccess);

        Livewire::test(ListPermissions::class)
            ->call('processPermissionCheck', 'permission', ['1' => true])
            ->assertSet('deleteButtonAccess', true)
            ->assertSet('permissionsToDelete', ['1' => true]);
    }

    /** @test */
    public function deselects_permissions_unchecked(): void
    {
        $this->actingAs($this->userWithDeletePermissionAccess);

        Livewire::test(ListPermissions::class)
            ->set('deleteButtonAccess', true)
            ->call('processPermissionCheck', 'permission', ['1' => false])
            ->assertSet('deleteButtonAccess', false)
            ->assertSet('permissionsToDelete', ['1' => false]);
    }

    /** @test */
    public function fails_to_delete_checked_permissions_with_view_permission(): void
    {
        $this->actingAs($this->userWithViewPermissionAccess);

        Livewire::test(ListPermissions::class)
            ->call('deletePermissions')
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_delete_checked_permissions_if_no_permission_is_passed(): void
    {
        $this->actingAs($this->userWithDeletePermissionAccess);

        Livewire::test(ListPermissions::class)
            ->set('permissionsToDelete', [])
            ->call('deletePermissions')
            ->assertDispatched('toastr',
            [
                'type' => 'error',
                'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'Permission', 'action' => 'delete'])
            ]);

        $this->assertEquals(14, Permission::count());
    }

    /** @test */
    public function deletes_checked_permissions(): void
    {
        $this->actingAs($this->userWithDeletePermissionAccess);

        $firstPermissionId = Permission::first()->id;

        Livewire::test(ListPermissions::class)
            ->set('permissionsToDelete', [ $firstPermissionId => true, $firstPermissionId + 1 => true])
            ->call('deletePermissions')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_deletion', ['entity' => 'Permission'])
            ])
            ->assertSet('permissionsToDelete', []);

        $this->assertEquals(12, Permission::count());
    }
}