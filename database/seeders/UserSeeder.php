<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear un Super Admin
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear un usuario de Control Escolar
        DB::table('users')->insert([
            'name' => 'Control Escolar',
            'email' => 'control@example.com',
            'password' => Hash::make('password'),
            'role' => 'control_escolar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
