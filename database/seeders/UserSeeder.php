<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(
            ['email' => 'admin@sysmonitor.net'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'observador@sysmonitor.net'],
            [
                'name' => 'Usuario Observador',
                'password' => Hash::make('Observer123!'),
                'role' => 'observer',
            ]
        );
    }
}
