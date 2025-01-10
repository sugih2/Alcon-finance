<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // //create user
        // $user = User::create([
        //     'username'      => 'Administrators',
        //     'email'     => 'admins@gmail.com',
        //     'role_id' => 1,
        //     'password'  => bcrypt('password'),
        // ]);

        // //get all permissions
        // $permissions = Permission::all();

        // //get role admin
        // $role = Role::find(1);

        // //assign permission to role
        // $role->syncPermissions($permissions);

        // //assign role to user
        // $user->assignRole($role);
        $user = User::create([
            'username' => 'sugih',
            'email' => 'admin@gmail.com',
            'role_id' => 0,
            'password' => bcrypt('123456')
        ]);

        $role = Role::create(['name' => 'Admin']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
