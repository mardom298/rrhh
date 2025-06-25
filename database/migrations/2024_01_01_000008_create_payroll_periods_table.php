<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Enero 2024, Febrero 2024, etc.
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');
            $table->enum('type', ['monthly', 'gratification', 'cts', 'bonus'])->default('monthly');
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid', 'closed'])->default('draft');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_periods');
    }
};
