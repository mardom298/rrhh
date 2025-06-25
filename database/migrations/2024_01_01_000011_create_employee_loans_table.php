<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('loan_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->decimal('monthly_payment', 10, 2);
            $table->integer('installments');
            $table->integer('paid_installments')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'paid', 'cancelled'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_loans');
    }
};
