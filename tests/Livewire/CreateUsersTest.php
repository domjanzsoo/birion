<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Users\Add as CreateUser;
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
        $addPermission = Permission::create(['name' => 'add_user']);

        $this->userWithAddPermissionAccess->permissions()->attach($addPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);

        $adminRole->permissions()->attach($addPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();
    }

    /** @test */
    public function renders_sucessfully()
    {
        $this->actingAs($this->userWithAddPermissionAccess);
        Livewire::test(CreateUser::class)
            ->assertSee(trans('users.add'))
            ->assertSee(trans('user.add_full'))
            ->assertStatus(200);
    }
}
