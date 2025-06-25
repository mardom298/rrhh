<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Empresa Demo RRHH',
            'ruc' => '20123456789',
            'address' => 'Av. Javier Prado Este 123, San Isidro, Lima',
            'phone' => '01-234-5678',
            'email' => 'contacto@empresademo.com',
            'settings' => [
                'timezone' => 'America/Lima',
                'currency' => 'PEN',
                'work_hours_per_day' => 8,
                'work_days_per_week' => 5
            ],
            'active' => true
        ]);
    }
}
