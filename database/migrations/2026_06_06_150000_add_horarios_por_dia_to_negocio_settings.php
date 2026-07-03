<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('negocio_settings', 'horarios_por_dia')) {
            Schema::table('negocio_settings', function ($table) {
                $table->text('horarios_por_dia')->nullable()->after('horario_cierre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('negocio_settings', 'horarios_por_dia')) {
            Schema::table('negocio_settings', function ($table) {
                $table->dropColumn('horarios_por_dia');
            });
        }
    }
};
