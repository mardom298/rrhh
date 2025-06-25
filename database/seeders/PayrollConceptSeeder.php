<?php

namespace Database\Seeders;

use App\Models\PayrollConcept;
use Illuminate\Database\Seeder;

class PayrollConceptSeeder extends Seeder
{
    public function run(): void
    {
        $concepts = [
            // Ingresos
            [
                'code' => 'SUELDO_BASICO',
                'name' => 'Sueldo Básico',
                'description' => 'Remuneración básica mensual',
                'type' => 'income',
                'calculation_type' => 'fixed',
                'value' => 0,
                'order' => 1
            ],
            [
                'code' => 'ASIGNACION_FAMILIAR',
                'name' => 'Asignación Familiar',
                'description' => 'Asignación familiar por hijos menores',
                'type' => 'income',
                'calculation_type' => 'fixed',
                'value' => 102.50,
                'order' => 2
            ],
            [
                'code' => 'HORAS_EXTRAS',
                'name' => 'Horas Extras',
                'description' => 'Pago por horas extras trabajadas',
                'type' => 'income',
                'calculation_type' => 'formula',
                'value' => 1.25,
                'formula' => '{salary} / 240 * {value} * {quantity}',
                'order' => 3
            ],
            
            // Descuentos
            [
                'code' => 'ONP',
                'name' => 'ONP',
                'description' => 'Descuento ONP - 13%',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'value' => 13.00,
                'taxable' => false,
                'order' => 10
            ],
            [
                'code' => 'AFP_APORTE',
                'name' => 'AFP - Aporte',
                'description' => 'Aporte obligatorio AFP - 10%',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'value' => 10.00,
                'taxable' => false,
                'order' => 11
            ],
            [
                'code' => 'AFP_COMISION',
                'name' => 'AFP - Comisión',
                'description' => 'Comisión AFP sobre remuneración',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'value' => 1.69,
                'taxable' => false,
                'order' => 12
            ],
            [
                'code' => 'AFP_SEGURO',
                'name' => 'AFP - Seguro',
                'description' => 'Prima de seguro AFP',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'value' => 0.74,
                'taxable' => false,
                'order' => 13
            ],
            [
                'code' => 'QUINTA_CATEGORIA',
                'name' => 'Impuesto 5ta Categoría',
                'description' => 'Impuesto a la renta de quinta categoría',
                'type' => 'deduction',
                'calculation_type' => 'formula',
                'value' => 0,
                'formula' => 'calculateIncomeTax({salary})',
                'taxable' => false,
                'order' => 14
            ],
            
            // Aportes del empleador
            [
                'code' => 'ESSALUD',
                'name' => 'EsSalud',
                'description' => 'Aporte del empleador a EsSalud - 9%',
                'type' => 'contribution',
                'calculation_type' => 'percentage',
                'value' => 9.00,
                'taxable' => false,
                'order' => 20
            ]
        ];

        foreach ($concepts as $concept) {
            PayrollConcept::create([
                'company_id' => 1,
                'code' => $concept['code'],
                'name' => $concept['name'],
                'description' => $concept['description'],
                'type' => $concept['type'],
                'calculation_type' => $concept['calculation_type'],
                'value' => $concept['value'],
                'formula' => $concept['formula'] ?? null,
                'taxable' => $concept['taxable'] ?? true,
                'affects_cts' => $concept['affects_cts'] ?? true,
                'affects_gratification' => $concept['affects_gratification'] ?? true,
                'affects_vacation' => $concept['affects_vacation'] ?? true,
                'active' => true,
                'order' => $concept['order']
            ]);
        }
    }
}
