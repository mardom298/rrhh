<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Grupo Ballesteros
            $table->string('code')->unique(); // GB001
            $table->string('ruc_group', 11)->unique(); // RUC del grupo
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_groups');
    }
};
