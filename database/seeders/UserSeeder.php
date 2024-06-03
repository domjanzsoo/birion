<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Admin User',
                'email'     => 'admin.user@email.com',
                'password'  => Hash::make('adminpassword')
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'name'      => $user['name'],
                'email'     => $user['email'],
                'password'  => $user['password']
            ]);
        }
    }
}
