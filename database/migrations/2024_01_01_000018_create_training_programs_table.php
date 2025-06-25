<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['online', 'presencial', 'hibrido']);
            $table->enum('category', ['tecnico', 'liderazgo', 'soft_skills', 'compliance', 'seguridad']);
            $table->integer('duration_hours');
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('provider')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_participants')->nullable();
            $table->json('requirements')->nullable();
            $table->json('objectives')->nullable();
            $table->boolean('certification_available')->default(false);
            $table->enum('status', ['draft', 'published', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_programs');
    }
};
