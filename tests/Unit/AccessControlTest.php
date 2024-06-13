<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AccessControlService;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private $accessControlService;

    private $permissions = ['view_users', 'add_user', 'edit_user', 'delete_user'];
    private $roles = [
        'adminUser'                                     => ['view_users', 'add_user', 'edit_user', 'delete_user'],
        'userWithViewUserPermission'                    => ['view_users'],
        'userWithMultipleUserPermissionsButNoDelete'    => ['view_users', 'add_user', 'edit_user'],
        'userWithDeleteAndEditPermissionOnly'           => ['delete_user', 'edit_user'],
    ];
    private $users = [
        'adminUser'                                         => null,
        'adminUserInRole'                                   => null,
        'userWithViewUserPermission'                        => null,
        'userWithViewUserPermissionInRole'                  => null,
        'userWithMultipleUserPermissionsButNoDelete'        => null,
        'userWithMultipleUserPermissionsButNoDeleteInRole'  => null,
        'userWithDeleteAndEditPermissionOnly'               => null,
        'userWithDeleteAndTransferPermissionOnlyInRole'     => null,
        'userWithDeletePermissionButNoDeleteRole'           => null
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessControlService = AccessControlService::getInstance();

        $permissions = [];

        foreach ($this->permissions as $permission) {
            $permissions[$permission] = Permission::create(['name' => $permission]);
        }

        $this->users['userWithDeletePermissionButNoDeleteRole'] = User::factory()->create();
        $this->users['userWithDeletePermissionButNoDeleteRole']->permissions()->attach($permissions['delete_user']);

        foreach ($this->roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $this->users[$roleName] = User::factory()->create();
            $this->users[$roleName . 'InRole'] = User::factory()->create();

            foreach ($rolePermissions as $permission) {
                $role->permissions()->attach($permissions[$permission]);

                $this->users[$roleName]->permissions()->attach($permissions[$permission]);
            }

            if ($roleName === 'userWithMultipleUserPermissionsButNoDelete') {
                $role->users()->attach($this->users['userWithDeletePermissionButNoDeleteRole']);
            }

            $this->users[$roleName . 'InRole']->roles()->attach($role);
        }
    }

    /** @test */
    public function service_instance_is_an_object(): void
    {
        $this->assertIsObject(AccessControlService::getInstance());
    }

    /** @test */
    public function admin_user_can_view(): void
    {
        $user = $this->users['adminUser'];

        $this->assertTrue($this->accessControlService->canAccess($user, 'view_users'));
    }

    /** @test */
    public function admin_user_can_add(): void
    {
        $user = $this->users['adminUser'];

        $this->assertTrue($this->accessControlService->canAccess($user, 'add_user'));
    }

    /** @test */
    public function admin_user_can_edit(): void
    {
        $user = $this->users['adminUser'];

        $this->assertTrue($this->accessControlService->canAccess($user, 'edit_user'));
    }

    /** @test */
    public function admin_user_can_delete(): void
    {
        $user = $this->users['adminUser'];

        $this->assertTrue($this->accessControlService->canAccess($user, 'delete_user'));
    }

    /** @test */
    public function admin_user_cannot_non_existent_permission(): void
    {
        $user = $this->users['adminUser'];

        $this->assertFalse($this->accessControlService->canAccess($user, 'punch_user'));
    }

    /** @test */
    public function admin_user_with_role_can_view(): void
    {
        $user = $this->users['adminUserInRole'];

        $this->assertTrue($this->accessControlService->canAccess($user, 'view_users'));
    }

      /** @test */
      public function admin_user_with_role_can_add(): void
      {
          $user = $this->users['adminUserInRole'];
  
          $this->assertTrue($this->accessControlService->canAccess($user, 'add_user'));
      }

        /** @test */
        public function admin_user_with_role_can_edit(): void
        {
            $user = $this->users['adminUserInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'edit_user'));
        }

        /** @test */
        public function admin_user_with_role_can_delete(): void
        {
            $user = $this->users['adminUserInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'delete_user'));
        }

        /** @test */
        public function admin_user_with_role_cannot_access_nonexistent_permission(): void
        {
            $user = $this->users['adminUserInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'punch_user'));
        }

        /** @test */
        public function admin_user_with_role_can_access_role(): void
        {
            $user = $this->users['adminUserInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, [], 'adminUser'));
        }

        /** @test */
        public function admin_user_with_role_cannot_access_different_role(): void
        {
            $user = $this->users['adminUserInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, [], 'userWithViewUserPermission'));
        }

        /** @test */
        public function admin_user_with_role_cannot_access_nonexistent_role(): void
        {
            $user = $this->users['adminUserInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, [], 'projectManager'));
        }

        /** @test */
        public function user_with_view_user_permission_can_view(): void
        {
            $user = $this->users['userWithViewUserPermission'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'view_users'));
        }

        /** @test */
        public function user_with_view_user_permission_cannot_add(): void
        {
            $user = $this->users['userWithViewUserPermission'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'add_user'));
        }

        /** @test */
        public function user_with_view_user_permission_cannot_edit(): void
        {
            $user = $this->users['userWithViewUserPermission'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'edit_user'));
        }

        /** @test */
        public function user_with_view_user_permission_cannot_delete(): void
        {
            $user = $this->users['userWithViewUserPermission'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'delete_user'));
        }

        /** @test */
        public function user_with_view_user_permission_cannot_access_nonexistent_permission(): void
        {
            $user = $this->users['userWithViewUserPermission'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'punch_user'));
        }

        /** @test */
        public function user_with_view_user_role_can_view(): void
        {
            $user = $this->users['userWithViewUserPermissionInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'view_users'));
        }

        /** @test */
        public function user_with_view_user_role_cannot_add(): void
        {
            $user = $this->users['userWithViewUserPermissionInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'add_user'));
        }

        /** @test */
        public function user_with_view_user_role_cannot_edit(): void
        {
            $user = $this->users['userWithViewUserPermissionInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'edit_user'));
        }

       /** @test */
       public function user_with_view_user_role_cannot_delete(): void
       {
           $user = $this->users['userWithViewUserPermissionInRole'];

           $this->assertFalse($this->accessControlService->canAccess($user, 'delete_user'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_cannot_delete()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDelete'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'delete_user'));
       }

        /** @test */
        public function user_with_all_permission_but_no_delete_can_view()
        {
            $user = $this->users['userWithMultipleUserPermissionsButNoDelete'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'view_users'));
        }

       /** @test */
       public function user_with_all_permission_but_no_delete_can_add()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDelete'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'add_user'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_can_edit()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDelete'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'edit_user'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_in_role_cannot_delete()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDeleteInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'delete_user'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_in_role_can_view()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDeleteInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'view_users'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_in_role_can_add()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDeleteInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'add_user'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_in_role_can_edit()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDeleteInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'edit_user'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_in_role_can_access_by_role_name()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDeleteInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, '', 'userWithMultipleUserPermissionsButNoDelete'));
       }

       /** @test */
       public function user_with_all_permission_but_no_delete_in_role_can_access_by_any_other_role_name()
       {
            $user = $this->users['userWithMultipleUserPermissionsButNoDeleteInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, '', 'madeUpRole'));
       }
       
       /** @test */
       public function user_with_delete_and_edit_permissions_can_delete()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnly'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'delete_user'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_can_edit()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnly'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'edit_user'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_cannot_view()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnly'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'view_users'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_cannot_add()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnly'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'add_users'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_in_role_can_delete()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnlyInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'delete_user'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_in_role_can_edit()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnlyInRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'edit_user'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_in_role_cannot_add()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnlyInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'add_user'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_in_role_cannot_view()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnlyInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, 'view_users'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_in_role_cannot_access_any_other_role()
       {
            $user = $this->users['userWithDeleteAndEditPermissionOnlyInRole'];

            $this->assertFalse($this->accessControlService->canAccess($user, [], 'userWithViewUserPermission'));
       }

       /** @test */
       public function user_with_delete_and_edit_permissions_can_delete_even_if_its_role_cannot_delete()
       {
            $user = $this->users['userWithDeletePermissionButNoDeleteRole'];

            $this->assertTrue($this->accessControlService->canAccess($user, 'delete_user', 'userWithMultipleUserPermissionsButNoDelete'));
       }
}
