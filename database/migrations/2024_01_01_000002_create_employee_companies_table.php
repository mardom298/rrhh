<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('employee_code'); // Código específico por empresa
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated', 'suspended'])->default('active');
            $table->decimal('base_salary', 10, 2);
            $table->json('salary_components')->nullable(); // Bonos, comisiones específicas por empresa
            $table->enum('contract_type', ['indefinido', 'plazo_fijo', 'part_time', 'practicas', 'consultor']);
            $table->enum('payroll_frequency', ['monthly', 'biweekly', 'weekly'])->default('monthly');
            $table->string('cost_center')->nullable();
            $table->boolean('is_primary_company')->default(false); // Empresa principal del empleado
            $table->json('benefits')->nullable(); // Beneficios específicos por empresa
            $table->timestamps();
            
            $table->unique(['user_id', 'company_id']);
            $table->unique(['company_id', 'employee_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_companies');
    }
};
