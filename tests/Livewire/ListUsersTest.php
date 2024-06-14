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
}