<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->string('period'); // 2024-Q1, 2024-H1, 2024
            $table->enum('type', ['self', 'supervisor', 'peer', '360', 'customer']);
            $table->date('evaluation_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'completed'])->default('draft');
            $table->decimal('overall_score', 3, 2)->nullable();
            $table->json('scores')->nullable(); // Puntuaciones por competencia
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals')->nullable();
            $table->text('development_plan')->nullable();
            $table->text('evaluator_comments')->nullable();
            $table->text('employee_comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_evaluations');
    }
};
