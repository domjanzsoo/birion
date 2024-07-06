<?php

namespace Tests\Livewire;

use Livewire\Livewire;
use App\Livewire\Roles\All as ListRoles;
use App\Models\Role;

class ListRolesTest extends MainListTestCase
{
    protected $entityModel = [
        'entity'    => 'role',
        'className' => Role::class,
        'fields'    => [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
            ['name' => 'test5'],
            ['name' => 'test6'],
            ['name' => 'test7'],
            ['name' => 'test8'],
            ['name' => 'test9'],
            ['name' => 'test10'],
        ]
    ];

    protected $roleCount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleCount = Role::count();
    }

    /** @test */
    public function renders_sucessfully_with_view_permission_granted(): void
    {
        $this->actingAs($this->userWithViewAccess);

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
        $this->actingAs($this->userWithAddAccess);

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
        $this->actingAs($this->userWithEditAccess);

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
        $this->actingAs($this->userWithViewAccessInRole);

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
        $this->actingAs($this->userWithViewAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_roles_checked_with_add_roles_permission_only(): void
    {
        $this->actingAs($this->userWithAddAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_roles_checked_with_edit_role_permission_only(): void
    {
        $this->actingAs($this->userWithEditAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function selects_permissions_checked_with_delete_role_permission(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListRoles::class)
            ->call('processRoleCheck', 'role', ['1' => true])
            ->assertSet('deleteButtonAccess', true)
            ->assertSet('rolesToDelete', ['1' => true]);
    }

    /** @test */
    public function deselects_roles_unchecked(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListRoles::class)
            ->set('deleteButtonAccess', true)
            ->call('processRoleCheck', 'role', ['1' => false])
            ->assertSet('deleteButtonAccess', false)
            ->assertSet('rolesToDelete', ['1' => false]);
    }

    /** @test */
    public function fails_to_delete_checked_roels_with_view_roles_permission_only(): void
    {
        $this->actingAs($this->userWithViewAccess);

        Livewire::test(ListRoles::class)
            ->call('deleteRoles')
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_delete_checked_roles_if_no_role_is_passed(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

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
        $this->actingAs($this->userWithDeleteAccess);

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