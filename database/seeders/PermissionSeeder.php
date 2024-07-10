<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // permisssions
            [
                'name'  => 'view_permissions'
            ],
            [
                'name'  => 'edit_permission'
            ],
            [
                'name'  => 'delete_permission'
            ],
            [
                'name'  => 'add_permission'
            ],
            // roles
            [
                'name'  => 'view_roles'
            ],
            [
                'name'  => 'add_role'
            ],
            [
                'name'  => 'edit_role'
            ],
            [
                'name'  => 'delete_role'
            ],
            // users
            [
                'name'  => 'view_users'
            ],
            [
                'name'  => 'add_user'
            ],
            [
                'name'  => 'edit_user'
            ],
            [
                'name'  => 'delete_user'
            ],
            // properties
            [
                'name'  => 'view_properties'
            ],
            [
                'name'  => 'add_property'
            ],
            [
                'name'  => 'edit_property'
            ],
            [
                'name'  => 'delete_property'
            ]
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name'      => $permission['name']
            ]);
        }
    }
}
