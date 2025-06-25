<?php

namespace Database\Seeders;

use App\Models\BusinessGroup;
use Illuminate\Database\Seeder;

class BusinessGroupSeeder extends Seeder
{
    public function run(): void
    {
        BusinessGroup::create([
            'name' => 'Grupo Ballesteros',
            'ruc' => '20123456789',
            'description' => 'Grupo empresarial líder en múltiples sectores',
            'legal_representative' => 'Carlos Ballesteros',
            'address' => 'Av. Principal 123, Lima, Perú',
            'phone' => '+51 1 234-5678',
            'email' => 'info@ballesteros.com',
            'website' => 'https://ballesteros.com',
            'status' => 'active',
            'settings' => [
                'timezone' => 'America/Lima',
                'currency' => 'PEN',
                'fiscal_year_start' => '01-01'
            ]
        ]);
    }
}
