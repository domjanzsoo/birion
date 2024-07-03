<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Users\Add as CreateUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\UploadedFile;

class CreateUsersTest extends TestCase
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
            ->assertSee(trans('users.add_full'))
            ->assertStatus(200);
    }

    /** @test */
    public function creates_user_with_add_user_permission_granted()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'test_user',
                'email' => 'test33.user@email.com',
                'password' => 'Password!',
                'password_confirmation' => 'Password!',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'User'])
            ])
            ->assertDispatched('user-permissions-cleared')
            ->assertDispatched('user-roles-submitted');

        $this->assertEquals(4, User::count());
    }

    /** @test */
    public function creates_user_with_add_user_permission_granted_through_role()
    {
        $this->actingAs($this->userWithAdminRole);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'test_user',
                'email' => 'test33.user@email.com',
                'password' => 'Password!',
                'password_confirmation' => 'Password!',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'User'])
            ])
            ->assertDispatched('user-permissions-cleared')
            ->assertDispatched('user-roles-submitted');

        $this->assertEquals(4, User::count());
    }

    /** @test */
    public function creates_user_with_no_permission_fails()
    {
        $this->actingAs($this->userWithNoPermission);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'test_user',
                'email' => 'test33.user@email.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertForbidden();

        $this->assertEquals(3, User::count());
    }

    /** @test */
    public function fails_with_missing_full_name()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => null,
                'email' => 'test33.user@email.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertHasErrors(['state.full_name' => 'required']);

        $this->assertEquals(3, User::count());
    }

    /** @test */
    public function fails_with_missing_email()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'Joe Doe',
                'email' => null,
                'password' => 'password',
                'password_confirmation' => 'password',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertHasErrors(['state.email' => 'required']);

        $this->assertEquals(3, User::count());
    }

    /** @test */
    public function fails_with_wrong_formatted_email()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'Joe Doe',
                'email' => $this->userWithAdminRole->email,
                'password' => 'password',
                'password_confirmation' => 'password',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertHasErrors(['state.email' => 'unique']);

        $this->assertEquals(3, User::count());
    }

    /** @test */
    public function fails_with_too_short_password()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'Joe Doe',
                'email' => 'test33.user@email.com',
                'password' => 'passw',
                'password_confirmation' => 'passw',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertHasErrors();

        $this->assertEquals(3, User::count());
    }

      /** @test */
      public function fails_with_invalid_password_on_missing_camel_case()
      {
          $this->actingAs($this->userWithAddPermissionAccess);
  
          $this->assertEquals(3, User::count());
  
          Livewire::test(CreateUser::class)
              ->set('state', [
                  'full_name' => 'Joe Doe',
                  'email' => 'test33.user@email.com',
                  'password' => 'password',
                  'password_confirmation' => 'password',
                  'profile_picture' => null,
                  'permissions' => [],
                  'roles' => []
              ])
              ->call('addUser')
              ->assertHasErrors();
  
          $this->assertEquals(3, User::count());
      }

    /** @test */
    public function fails_with_missing_password()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'Joe Doe',
                'email' => 'test33.user@email.com',
                'password' => null,
                'password_confirmation' => 'password',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertHasErrors(['state.password' => 'required']);

        $this->assertEquals(3, User::count());
    }

    /** @test */
    public function fails_with_unmatching_password_confirmation()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => null,
                'email' => 'test33.user@email.com',
                'password' => 'Password!',
                'password_confirmation' => 'password-different',
                'profile_picture' => null,
                'permissions' => [],
                'roles' => []
            ])
            ->call('addUser')
            ->assertHasErrors(['state.password' => 'confirmed']);

        $this->assertEquals(3, User::count());
    }

    // TO-DO NEEDS FIXING
    // public function fails_with_oversized_profile_image()
    // {
    //     $this->actingAs($this->userWithAddPermissionAccess);

    //     $this->assertEquals(3, User::count());

    //     Storage::fake('public');

    //     $profileImage = UploadedFile::fake()->create('blaa.jpg', 145412);
        
    //     Livewire::test(CreateUser::class)
    //         ->set('state', [
    //             'full_name' => 'Joe Doe',
    //             'email' => 'test33.user@email.com',
    //             'password' => 'password',
    //             'password_confirmation' => 'password',
    //             'profile_picture' => $profileImage,
    //             'permissions' => [],
    //             'roles' => []
    //         ])
    //         ->call('addUser')
    //         ->assertHasErrors(['state.profile_picture' => 'max']);

    //     $this->assertEquals(3, User::count());
    // }

    /** @test */
    public function create_user_with_permissions()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $permission1 = Permission::create(['name' => 'test1']);
        $permission2 = Permission::create(['name' => 'test2']);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'test_user',
                'email' => 'test33.user@email.com',
                'password' => 'Password!',
                'password_confirmation' => 'Password!',
                'profile_picture' => null,
                'permissions' => [$permission1->id, $permission2->id],
                'roles' => []
            ])
            ->call('addUser')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'User'])
            ])
            ->assertDispatched('user-permissions-cleared')
            ->assertDispatched('user-roles-submitted');

        $newUser = User::where(['name' => 'test_user'])->first();

        $this->assertEquals(2, $newUser->permissions->count());
        $this->assertContains($permission1->id, $newUser->permissions->pluck('id')->toArray());
        $this->assertContains($permission2->id, $newUser->permissions->pluck('id')->toArray());

        $this->assertEquals(4, User::count());
    }

    /** @test */
    public function create_user_with_roles()
    {
        $this->actingAs($this->userWithAddPermissionAccess);

        $role1 = Role::create(['name' => 'test1']);
        $role2 = Role::create(['name' => 'test2']);

        $this->assertEquals(3, User::count());

        Livewire::test(CreateUser::class)
            ->set('state', [
                'full_name' => 'test_user',
                'email' => 'test33.user@email.com',
                'password' => 'Password!',
                'password_confirmation' => 'Password!',
                'profile_picture' => null,
                'roles' => [$role1->id, $role2->id],
                'permissions' => []
            ])
            ->call('addUser')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_creation', ['entity' => 'User'])
            ])
            ->assertDispatched('user-permissions-cleared')
            ->assertDispatched('user-roles-submitted');

        $newUser = User::where(['name' => 'test_user'])->first();

        $this->assertEquals(2, $newUser->roles->count());
        $this->assertContains($role1->id, $newUser->roles()->pluck('roles.id')->toArray());
        $this->assertContains($role2->id, $newUser->roles()->pluck('roles.id')->toArray());

        $this->assertEquals(4, User::count());
    }
}