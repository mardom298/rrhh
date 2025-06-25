<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->string('period_code')->after('name'); // 202401-BALL001
            $table->enum('consolidation_status', ['individual', 'consolidated', 'group_consolidated'])->default('individual');
            $table->foreignId('parent_period_id')->nullable()->constrained('payroll_periods')->onDelete('set null'); // Para consolidación grupal
            $table->json('consolidation_data')->nullable(); // Datos de consolidación
        });
    }

    public function down()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropForeign(['parent_period_id']);
            $table->dropColumn(['period_code', 'consolidation_status', 'parent_period_id', 'consolidation_data']);
        });
    }
};
