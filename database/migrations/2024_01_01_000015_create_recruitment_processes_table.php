<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recruitment_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            $table->enum('stage', ['screening', 'phone_interview', 'technical_test', 'interview_1', 'interview_2', 'reference_check', 'offer', 'hired', 'rejected'])->default('screening');
            $table->date('stage_date');
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('score')->nullable();
            $table->text('notes')->nullable();
            $table->json('feedback')->nullable();
            $table->enum('result', ['pass', 'fail', 'pending'])->default('pending');
            $table->date('next_stage_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recruitment_processes');
    }
};
