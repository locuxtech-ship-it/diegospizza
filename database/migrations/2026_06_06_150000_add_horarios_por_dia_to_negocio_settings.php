<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE negocio_settings ADD COLUMN horarios_por_dia LONGTEXT NULL AFTER horario_cierre");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE negocio_settings DROP COLUMN horarios_por_dia");
    }
};
