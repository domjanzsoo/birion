<?php

namespace Tests\Livewire;

use Livewire\Livewire;
use App\Livewire\Permissions\All as ListPermissions;
use App\Models\Permission;

class ListPermissionsTest extends MainListTestCase
{
    protected $entityModel = [
        'entity'    => 'permission',
        'className' => Permission::class,
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

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function renders_sucessfully_with_view_permission_granted(): void
    {
        $this->actingAs($this->userWithViewAccess);

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
        $this->actingAs($this->userWithAddAccess);

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
        $this->actingAs($this->userWithEditAccess);

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
        $this->actingAs($this->userWithViewAccessInRole);

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
        $this->actingAs($this->userWithViewAccess);

        Livewire::test(ListPermissions::class)
            ->call('processItemCheck', 'permission', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_permissions_checked_with_add_permission(): void
    {
        $this->actingAs($this->userWithAddAccess);

        Livewire::test(ListPermissions::class)
            ->call('processItemCheck', 'permission', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_permissions_checked_with_edit_permission(): void
    {
        $this->actingAs($this->userWithEditAccess);

        Livewire::test(ListPermissions::class)
            ->call('processItemCheck', 'permission', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function selects_permissions_checked_with_delete_permission(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListPermissions::class)
            ->call('processItemCheck', 'permission', ['1' => true])
            ->assertSet('deleteButtonAccess', true)
            ->assertSet('permissionsToDelete', ['1' => true]);
    }

    /** @test */
    public function deselects_permissions_unchecked(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListPermissions::class)
            ->set('deleteButtonAccess', true)
            ->call('processItemCheck', 'permission', ['1' => false])
            ->assertSet('deleteButtonAccess', false)
            ->assertSet('permissionsToDelete', ['1' => false]);
    }

    /** @test */
    public function fails_to_delete_checked_permissions_with_view_permission(): void
    {
        $this->actingAs($this->userWithViewAccess);

        Livewire::test(ListPermissions::class)
            ->call('deletePermissions')
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_delete_checked_permissions_if_no_permission_is_passed(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

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
        $this->actingAs($this->userWithDeleteAccess);

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