<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name'          => 'admin',
                'permissions'   => ['all'],
                'user'          => 'admin.user@email.com'
            ]
        ];

        $permissions = DB::table('permissions')->select(['id', 'name'])->get();

        foreach ($roles as $role) {
            $newRoleId = DB::table('roles')->insertGetId([
                'name'      => $role['name']
            ]);

            foreach ($permissions as $permission) {
                if ($role['permissions'][0] === 'all' || in_array($permission->name, $role['permissions'])) {
                    DB::table('permission_role')->insert(['role_id' => $newRoleId, 'permission_id' => $permission->id]);
                }
            }

            if (isset($role['user'])) {
                $user = DB::table('users')->select(['id', 'email'])->where(['email' => $role['user']])->first();

                DB::table('role_user')->insert(['role_id' => $newRoleId, 'user_id' => $user->id]);
            }
        }
    }
}
