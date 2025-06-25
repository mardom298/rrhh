<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ruc', 11)->unique();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('phone', 20);
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_groups');
    }
};
