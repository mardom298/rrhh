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
            'code' => 'GB001',
            'ruc_group' => '20123456789',
            'description' => 'Grupo empresarial líder en múltiples sectores',
            'settings' => [
                'timezone' => 'America/Lima',
                'currency' => 'PEN',
                'fiscal_year_start' => '01-01',
                'consolidation_method' => 'full'
            ],
            'active' => true
        ]);
    }
}
