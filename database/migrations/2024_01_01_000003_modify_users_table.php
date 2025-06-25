<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover campos que ahora van en employee_companies
            $table->dropForeign(['company_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropForeign(['manager_id']);
            
            $table->dropColumn([
                'company_id',
                'employee_code',
                'department_id',
                'position_id',
                'manager_id',
                'hire_date',
                'termination_date',
                'status',
                'salary'
            ]);
            
            // Agregar campos del grupo empresarial
            $table->foreignId('business_group_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('global_employee_id')->unique()->after('business_group_id'); // ID único en todo el grupo
            $table->enum('employee_type', ['permanent', 'temporary', 'consultant', 'intern'])->default('permanent');
            $table->json('certifications')->nullable(); // Certificaciones del empleado
            $table->json('skills')->nullable(); // Habilidades
            $table->string('tax_id')->nullable(); // Para efectos tributarios
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_group_id']);
            $table->dropColumn([
                'business_group_id',
                'global_employee_id',
                'employee_type',
                'certifications',
                'skills',
                'tax_id'
            ]);
            
            // Restaurar campos originales (esto sería complejo en producción)
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('employee_code');
            // ... resto de campos
        });
    }
};
