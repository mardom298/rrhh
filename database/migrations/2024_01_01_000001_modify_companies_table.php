<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('business_group_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('company_code')->after('name'); // BALL001, BALL002, etc.
            $table->enum('company_type', ['principal', 'subsidiary', 'branch'])->default('subsidiary');
            $table->string('legal_representative')->nullable();
            $table->string('economic_activity')->nullable();
            $table->json('tax_settings')->nullable(); // Configuraciones tributarias específicas
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['business_group_id']);
            $table->dropColumn([
                'business_group_id', 
                'company_code', 
                'company_type', 
                'legal_representative', 
                'economic_activity',
                'tax_settings'
            ]);
        });
    }
};
