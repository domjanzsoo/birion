<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Users\All as ListUsers;

class ListUsersTest extends MainListTestCase
{
    protected $entityModel = [
        'entity'    => 'user',
        'className' => User::class,
        'fields'    => [
            ['name' => 'test1', 'email' => 'test1@email.com', 'password' => 'pass'],
            ['name' => 'test2', 'email' => 'test2@email.com', 'password' => 'pass'],
            ['name' => 'test3', 'email' => 'test3@email.com', 'password' => 'pass'],
            ['name' => 'test4', 'email' => 'test4@email.com', 'password' => 'pass'],
            ['name' => 'test5', 'email' => 'test5@email.com', 'password' => 'pass'],
            ['name' => 'test6', 'email' => 'test6@email.com', 'password' => 'pass'],
            ['name' => 'test7', 'email' => 'test7@email.com', 'password' => 'pass'],
            ['name' => 'test8', 'email' => 'test8@email.com', 'password' => 'pass'],
            ['name' => 'test9', 'email' => 'tes9@email.com', 'password' => 'pass'],
            ['name' => 'test10', 'email' => 'test10@email.com', 'password' => 'pass'],
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

        $pageCount = ceil(User::count() / 5);

        Livewire::test(ListUsers::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_add_permission_granted(): void
    { 
        $this->actingAs($this->userWithAddAccess);

        $pageCount = ceil(User::count() / 5);

        Livewire::test(ListUsers::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function renders_sucessfully_with_edit_edit_granted_by_role(): void
    { 
        $this->actingAs($this->userWithViewAccessInRole);

        $pageCount = ceil(USer::count() / 5);

        Livewire::test(ListUsers::class)
            ->assertSeeHtml('<span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            ' . $pageCount . '
          </span>')
            ->assertStatus(200);
    }

    /** @test */
    public function fails_to_select_users_checked_with_view_permission(): void
    {
        $this->actingAs($this->userWithViewAccess);

        Livewire::test(ListUsers::class)
            ->call('processItemCheck', 'user', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_users_checked_with_add_permission(): void
    {
        $this->actingAs($this->userWithAddAccess);

        Livewire::test(ListUsers::class)
            ->call('processItemCheck', 'user', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_select_users_checked_with_edit_permission(): void
    {
        $this->actingAs($this->userWithEditAccess);

        Livewire::test(ListUsers::class)
            ->call('processItemCheck', 'user', ['1' => true])
            ->assertForbidden();
    }

    /** @test */
    public function selects_users_checked_with_delete_permission(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListUsers::class)
            ->call('processItemCheck', 'user', ['1' => true])
            ->assertSet('deleteButtonAccess', true)
            ->assertSet('usersToDelete', ['1' => true]);
    }

    /** @test */
    public function deselects_users_unchecked(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListUsers::class)
            ->set('deleteButtonAccess', true)
            ->call('processItemCheck', 'user', ['1' => false])
            ->assertSet('deleteButtonAccess', false)
            ->assertSet('usersToDelete', ['1' => false]);
    }

    /** @test */
    public function fails_to_delete_checked_users_with_view_permission(): void
    {
        $this->actingAs($this->userWithViewAccess);

        Livewire::test(ListUsers::class)
            ->call('deleteUsers')
            ->assertForbidden();
    }

    /** @test */
    public function fails_to_delete_checked_users_if_no_permission_is_passed(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        Livewire::test(ListUsers::class)
            ->set('usersToDelete', [])
            ->call('deleteUsers')
            ->assertDispatched('toastr',
            [
                'type' => 'error',
                'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'User', 'action' => 'delete'])
            ]);

        $this->assertEquals(15, User::count());
    }

    /** @test */
    public function deletes_checked_users(): void
    {
        $this->actingAs($this->userWithDeleteAccess);

        $firstUserId = User::first()->id;

        Livewire::test(ListUsers::class)
            ->set('usersToDelete', [ $firstUserId => true, ++$firstUserId => true])
            ->call('deleteUsers')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_deletion', ['entity' => 'User'])
            ])
            ->assertSet('usersToDelete', []);

        $this->assertEquals(13, User::count());
    }
}