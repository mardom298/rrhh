<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_concepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['income', 'deduction', 'contribution']);
            $table->enum('calculation_type', ['fixed', 'percentage', 'formula']);
            $table->decimal('value', 10, 4)->nullable();
            $table->text('formula')->nullable(); // Para cálculos complejos
            $table->boolean('taxable')->default(true);
            $table->boolean('affects_cts')->default(true);
            $table->boolean('affects_gratification')->default(true);
            $table->boolean('affects_vacation')->default(true);
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_concepts');
    }
};
