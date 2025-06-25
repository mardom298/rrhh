<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'business_group_id' => 1,
            'global_employee_id' => 'EMP-001',
            'dni' => '12345678',
            'first_name' => 'Carlos',
            'last_name' => 'Ballesteros',
            'email' => 'admin@ballesteros.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 123 456',
            'birth_date' => '1980-01-15',
            'gender' => 'male',
            'address' => 'Av. Principal 123, Lima, Perú',
            'employee_type' => 'permanent'
        ]);

        User::create([
            'business_group_id' => 1,
            'global_employee_id' => 'EMP-002',
            'dni' => '87654321',
            'first_name' => 'María',
            'last_name' => 'García',
            'email' => 'maria.garcia@ballesteros.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 654 321',
            'birth_date' => '1985-03-20',
            'gender' => 'female',
            'address' => 'Av. Secundaria 456, Lima, Perú',
            'employee_type' => 'permanent'
        ]);

        User::create([
            'business_group_id' => 1,
            'global_employee_id' => 'EMP-003',
            'dni' => '11223344',
            'first_name' => 'Luis',
            'last_name' => 'Rodríguez',
            'email' => 'luis.rodriguez@ballesteros.com',
            'password' => Hash::make('password'),
            'phone' => '+51 999 111 222',
            'birth_date' => '1990-07-10',
            'gender' => 'male',
            'address' => 'Av. Tercera 789, Lima, Perú',
            'employee_type' => 'consultant'
        ]);
    }
}
