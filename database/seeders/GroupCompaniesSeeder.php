<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class GroupCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Ballesteros Construcción SAC',
                'company_code' => 'BALL-CONST',
                'ruc' => '20111111111',
                'company_type' => 'principal',
                'economic_activity' => 'Construcción y obras civiles'
            ],
            [
                'name' => 'Ballesteros Logística EIRL',
                'company_code' => 'BALL-LOG',
                'ruc' => '20222222222',
                'company_type' => 'subsidiary',
                'economic_activity' => 'Transporte y logística'
            ],
            [
                'name' => 'Ballesteros Servicios Generales SAC',
                'company_code' => 'BALL-SERV',
                'ruc' => '20333333333',
                'company_type' => 'subsidiary',
                'economic_activity' => 'Servicios generales y mantenimiento'
            ],
            [
                'name' => 'Ballesteros Inmobiliaria SAC',
                'company_code' => 'BALL-INMOB',
                'ruc' => '20444444444',
                'company_type' => 'subsidiary',
                'economic_activity' => 'Desarrollo inmobiliario'
            ],
            [
                'name' => 'Ballesteros Tecnología SRL',
                'company_code' => 'BALL-TECH',
                'ruc' => '20555555555',
                'company_type' => 'subsidiary',
                'economic_activity' => 'Desarrollo de software y tecnología'
            ]
        ];

        foreach ($companies as $company) {
            Company::create([
                'business_group_id' => 1,
                'name' => $company['name'],
                'company_code' => $company['company_code'],
                'ruc' => $company['ruc'],
                'company_type' => $company['company_type'],
                'address' => 'Av. Javier Prado Este 123, San Isidro, Lima',
                'phone' => '01-234-567' . rand(0, 9),
                'email' => strtolower(str_replace(' ', '', $company['company_code'])) . '@ballesteros.com',
                'legal_representative' => 'Carlos Ballesteros Mendoza',
                'economic_activity' => $company['economic_activity'],
                'settings' => [
                    'timezone' => 'America/Lima',
                    'currency' => 'PEN',
                    'work_hours_per_day' => 8,
                    'work_days_per_week' => 5
                ],
                'tax_settings' => [
                    'tax_regime' => 'general',
                    'igv_rate' => 18,
                    'retention_agent' => false
                ],
                'active' => true
            ]);
        }
    }
}
