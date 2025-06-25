<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Ballesteros Construcción',
                'ruc' => '20111111111',
                'business_name' => 'Ballesteros Construcción S.A.C.',
            ],
            [
                'name' => 'Ballesteros Logística',
                'ruc' => '20222222222',
                'business_name' => 'Ballesteros Logística S.R.L.',
            ],
            [
                'name' => 'Ballesteros Tecnología',
                'ruc' => '20333333333',
                'business_name' => 'Ballesteros Tecnología E.I.R.L.',
            ],
        ];

        foreach ($companies as $company) {
            Company::create([
                'business_group_id' => 1,
                'name' => $company['name'],
                'ruc' => $company['ruc'],
                'business_name' => $company['business_name'],
                'description' => 'Empresa del Grupo Ballesteros',
                'address' => 'Av. Principal 123, Lima, Perú',
                'phone' => '+51 1 234-5678',
                'email' => strtolower(str_replace(' ', '', $company['name'])) . '@grupoballesteros.com',
                'status' => 'active'
            ]);
        }
    }
}
