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
            $table->string('employee_code_company')->nullable();
            $table->decimal('salary', 10, 2);
            $table->enum('contract_type', ['Full-time', 'Part-time', 'Contract', 'Internship']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_primary_company')->default(false);
            $table->enum('status', ['Active', 'Inactive', 'Suspended', 'Terminated'])->default('Active');
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_companies');
    }
};
