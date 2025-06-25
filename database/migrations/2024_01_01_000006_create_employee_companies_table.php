<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->unique();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->decimal('base_salary', 10, 2);
            $table->decimal('total_salary', 10, 2);
            $table->json('benefits')->nullable();
            $table->boolean('is_primary_company')->default(false);
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_companies');
    }
};
