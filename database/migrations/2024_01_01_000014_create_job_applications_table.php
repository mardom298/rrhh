<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('dni', 8)->nullable();
            $table->text('address')->nullable();
            $table->string('resume_path');
            $table->text('cover_letter')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->enum('status', ['applied', 'screening', 'interview', 'test', 'offer', 'hired', 'rejected'])->default('applied');
            $table->integer('score')->nullable();
            $table->json('evaluation_notes')->nullable();
            $table->timestamp('applied_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_applications');
    }
};
