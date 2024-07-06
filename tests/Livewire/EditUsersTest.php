<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Users\Edit as EditUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\Permission;

class EditUsersTest extends TestCase
{
    use RefreshDatabase;

    private $userWithEditUserAccess;
    private $userWithAdminRole;
    private $userWithNoPermission;
    private $userToUpdate;

    const INITIAL_USER_DATA = [
        'name' => 'Existing User',
        'email' => 'existing.user@email.com',
        'password' => 'Password!',
        'permissions' => [],
        'roles' => [],
        'profile_image_path' => null
    ];

    const UPDATED_USER_DATA = [
        'name' => 'New User',
        'email' => 'updated.user@email.com',
        'password' => 'NewPassword!',
        'permissions' => [],
        'roles' => [],
        'profile_image_path' => null
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->userToUpdate = User::create(self::INITIAL_USER_DATA);

        $this->userWithEditUserAccess = User::factory()->create();
        $editPermission = Permission::create(['name' => 'edit_user']);
        $this->userWithEditUserAccess->permissions()->attach($editPermission);

        $this->userWithAdminRole = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->permissions()->attach($editPermission);
        $this->userWithAdminRole->roles()->attach($adminRole);

        $this->userWithNoPermission = User::factory()->create();
    }

    /** @test */
    public function renders_sucessfully(): void
    {
        $this->actingAs($this->userWithEditUserAccess);
        
        Livewire::test(EditUser::class)
            ->assertSeeHtml('test-id="edit_users" wire:submit="save"')
            ->assertStatus(200);
    }

    /** @test */
    public function edits_user_with_edit_user_permission_granted(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'User'])
            ])
            ->assertDispatched('user-edited');

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::UPDATED_USER_DATA['name']);
    }

    /** @test */
    public function handle_permissions_of_user(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

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

        Livewire::test(EditUser::class)
            ->call('handlePermissions', $permissions)
            ->assertSet('state.permission_update', [1, 3]);
    }

    /** @test */
    public function handle_edit_modal(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->assertSet('state.full_name', $this->userToUpdate->name)
            ->assertSet('state.email', $this->userToUpdate->email)
            ->assertSet('state.permission_update', [])
            ->assertSet('state.role_update', [])
            ->assertSet('state.profile_picture', null)
            ->assertSet('state.id', $this->userToUpdate->id);
    }

    /** @test */
    public function edits_user_and_updates_user_permissions_with_edit_user_permission_granted(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        $permissions = [
            Permission::create(['name' => 'test1']), 
            Permission::create(['name' => 'test2']) 
        ];

        Livewire::test(EditUser::class)
            ->set('user', $this->userToUpdate)
            ->set('state', [
                'full_name' => self::UPDATED_USER_DATA['name'],
                'email' => self::UPDATED_USER_DATA['email'],
                'permission_update' => array_map(function($permission) { return $permission->id; }, $permissions),
                'role_update' => [],
                'selected_permissions' => [],
                'selected_roles' => [],
                'profile_picture' => null,
                'password' => self::UPDATED_USER_DATA['password'],
                'password_confirmation' => self::UPDATED_USER_DATA['password'],
                'id' => $this->userToUpdate->id
                ]
            )
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'User'])
            ])
            ->assertDispatched('user-edited');

        $this->assertEquals(self::UPDATED_USER_DATA['name'], User::find($this->userToUpdate->id)->name);
        $this->assertEquals(count($permissions), User::find($this->userToUpdate->id)->permissions()->count());
    }

    /** @test */
    public function edit_user_with_edit_user_permission_granted_through_role(): void
    {
        $this->actingAs($this->userWithAdminRole);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    
        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->call('save')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => trans('notifications.successfull_update', ['entity' => 'User'])
            ])
            ->assertDispatched('user-edited');

        $this->assertEquals(self::UPDATED_USER_DATA['name'], User::find($this->userToUpdate->id)->name);
    }

    /** @test */
    public function edit_user_with_no_permission_fails(): void
    {
        $this->actingAs($this->userWithNoPermission);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->call('save')
            ->assertForbidden();

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }

    /** @test */
    public function fails_with_missing_name(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', '')
            ->set('state.email', self::UPDATED_USER_DATA['email'])
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', self::UPDATED_USER_DATA['password'])
            ->set('state.password_confirmation', self::UPDATED_USER_DATA['password'])
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors(['state.full_name' => 'required']);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }


    /** @test */
    public function fails_with_missing_email(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->set('state.email', '')
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', self::UPDATED_USER_DATA['password'])
            ->set('state.password_confirmation', self::UPDATED_USER_DATA['password'])
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors(['state.email' => 'required']);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }

    /** @test */
    public function fails_with_invalid_email_address(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->set('state.email', 'bad.address.com')
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', self::UPDATED_USER_DATA['password'])
            ->set('state.password_confirmation', self::UPDATED_USER_DATA['password'])
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors(['state.email' => 'email']);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }

    /** @test */
    public function fails_with_unique_email_address(): void
    {
        $additionalUserData = [
            'name' => 'Additional User',
            'password' => 'Password!',
            'email' => 'additional.email@test.com'
        ];

        User::create($additionalUserData);

        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->set('state.email', $additionalUserData['email'])
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', self::UPDATED_USER_DATA['password'])
            ->set('state.password_confirmation', self::UPDATED_USER_DATA['password'])
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors(['state.email' => 'unique']);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }

    /** @test */
    public function fails_with_too_short_password(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->set('state.email', self::UPDATED_USER_DATA['email'])
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', 'pass')
            ->set('state.password_confirmation', 'pass')
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors();

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }

    /** @test */
    public function fails_with_password_without_special_character(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->set('state.email', self::UPDATED_USER_DATA['email'])
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', 'password')
            ->set('state.password_confirmation', 'password')
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors();

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }

    /** @test */
    public function fails_with_password_not_confirmed(): void
    {
        $this->actingAs($this->userWithEditUserAccess);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);

        Livewire::test(EditUser::class)
            ->call('handleEditModalData', $this->userToUpdate->id, 'user')
            ->set('state.full_name', self::UPDATED_USER_DATA['name'])
            ->set('state.email', self::UPDATED_USER_DATA['email'])
            ->set('state.permission_update', [])
            ->set('state.role_update', [])
            ->set('state.selected_permissions', [])
            ->set('state.selected_roles', [])
            ->set('state.profile_picture', null)
            ->set('state.password', self::UPDATED_USER_DATA['password'])
            ->set('state.password_confirmation', 'different')
            ->set('state.profile_picture', null)
            ->call('save')
            ->assertHasErrors(['state.password' => 'confirmed']);

        $this->assertEquals(User::find($this->userToUpdate->id)->name, self::INITIAL_USER_DATA['name']);
    }
}
