<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'business_group_id' => 1,
            'global_employee_id' => 'GB-ADMIN-001',
            'dni' => '12345678',
            'first_name' => 'Carlos',
            'last_name' => 'Ballesteros',
            'email' => 'admin@ballesteros.com',
            'password' => Hash::make('password'),
            'phone' => '987654321',
            'birth_date' => '1980-01-15',
            'gender' => 'M',
            'address' => 'Av. Javier Prado Este 123, San Isidro',
            'emergency_contact_name' => 'María Ballesteros',
            'emergency_contact_phone' => '987654322',
            'employee_type' => 'permanent',
            'bank_account' => '123456789012345',
            'certifications' => ['MBA', 'PMP'],
            'skills' => ['Liderazgo', 'Gestión Empresarial', 'Finanzas'],
            'tax_id' => '12345678',
            'settings' => [
                'notifications' => true,
                'language' => 'es'
            ]
        ]);

        // Crear empleados adicionales
        $employees = [
            [
                'global_employee_id' => 'GB-EMP-002',
                'dni' => '23456789',
                'first_name' => 'Ana',
                'last_name' => 'García',
                'email' => 'ana.garcia@ballesteros.com',
                'employee_type' => 'permanent'
            ],
            [
                'global_employee_id' => 'GB-EMP-003',
                'dni' => '34567890',
                'first_name' => 'Luis',
                'last_name' => 'Rodríguez',
                'email' => 'luis.rodriguez@ballesteros.com',
                'employee_type' => 'permanent'
            ],
            [
                'global_employee_id' => 'GB-EMP-004',
                'dni' => '45678901',
                'first_name' => 'María',
                'last_name' => 'López',
                'email' => 'maria.lopez@ballesteros.com',
                'employee_type' => 'consultant'
            ],
            [
                'global_employee_id' => 'GB-EMP-005',
                'dni' => '56789012',
                'first_name' => 'Pedro',
                'last_name' => 'Martínez',
                'email' => 'pedro.martinez@ballesteros.com',
                'employee_type' => 'permanent'
            ]
        ];

        foreach ($employees as $employee) {
            User::create([
                'business_group_id' => 1,
                'global_employee_id' => $employee['global_employee_id'],
                'dni' => $employee['dni'],
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'email' => $employee['email'],
                'password' => Hash::make('password'),
                'phone' => '9876543' . rand(10, 99),
                'birth_date' => now()->subYears(rand(25, 50)),
                'gender' => ['M', 'F'][rand(0, 1)],
                'address' => 'Lima, Perú',
                'emergency_contact_name' => 'Contacto de Emergencia',
                'emergency_contact_phone' => '987654321',
                'employee_type' => $employee['employee_type'],
                'bank_account' => '12345678901234' . rand(0, 9),
                'certifications' => [],
                'skills' => [],
                'tax_id' => $employee['dni'],
                'settings' => [
                    'notifications' => true,
                    'language' => 'es'
                ]
            ]);
        }
    }
}
