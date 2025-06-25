<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Ballesteros Construcción',
                'ruc' => '20111111111',
                'business_name' => 'Ballesteros Construcción S.A.C.',
                'description' => 'Empresa líder en construcción y obras civiles'
            ],
            [
                'name' => 'Ballesteros Logística',
                'ruc' => '20222222222',
                'business_name' => 'Ballesteros Logística S.R.L.',
                'description' => 'Servicios de transporte y logística integral'
            ],
            [
                'name' => 'Ballesteros Tecnología',
                'ruc' => '20333333333',
                'business_name' => 'Ballesteros Tech S.A.C.',
                'description' => 'Soluciones tecnológicas y desarrollo de software'
            ]
        ];

        foreach ($companies as $company) {
            Company::create([
                'business_group_id' => 1,
                'name' => $company['name'],
                'ruc' => $company['ruc'],
                'business_name' => $company['business_name'],
                'description' => $company['description'],
                'address' => 'Av. Principal 123, Lima, Perú',
                'phone' => '+51 1 234-5678',
                'email' => strtolower(str_replace(' ', '', $company['name'])) . '@ballesteros.com',
                'status' => 'active'
            ]);
        }
    }
}
