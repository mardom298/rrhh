<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_concept_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
            $table->json('calculation_details')->nullable();
            $table->timestamps();
            
            $table->unique(['payroll_period_id', 'user_id', 'payroll_concept_id'], 'payroll_items_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_items');
    }
};
