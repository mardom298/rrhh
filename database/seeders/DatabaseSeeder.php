<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BusinessGroupSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeCompanySeeder::class,
        ]);
    }
}
