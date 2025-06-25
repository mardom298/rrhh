<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessGroup;

class BusinessGroupSeeder extends Seeder
{
    public function run(): void
    {
        BusinessGroup::create([
            'name' => 'Grupo Ballesteros',
            'ruc' => '20123456789',
            'description' => 'Grupo empresarial líder en múltiples sectores',
            'address' => 'Av. Principal 123, Lima, Perú',
            'phone' => '+51 1 234-5678',
            'email' => 'contacto@grupoballesteros.com',
            'website' => 'https://grupoballesteros.com',
            'status' => 'active'
        ]);
    }
}
