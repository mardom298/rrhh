<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['attendance', 'payroll', 'performance', 'recruitment', 'training', 'custom']);
            $table->json('parameters');
            $table->json('filters')->nullable();
            $table->enum('format', ['pdf', 'excel', 'csv', 'html']);
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->datetime('last_generated_at')->nullable();
            $table->datetime('next_generation_at')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_scheduled')->default(false);
            $table->json('recipients')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
