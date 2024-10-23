<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'Super Admin'
        ]);
        DB::table('users')->insert([
            'username' => 'admin',
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'email' => 'admin@alfabanguncipta.com',
            'email_verified_at' => now(),
            'password' => bcrypt('Admin1234%'),
            'role_id' => 1,
            'status' => 'active',
            'address' => 'Jl. Contoh No. 1',
            'city' => 'Kota Bandung',
            'country' => 'Indonesia',
            'postal' => '12345',
            'about' => 'Administrator of the system',
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
