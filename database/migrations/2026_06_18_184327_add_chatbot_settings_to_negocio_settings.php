<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->json('chatbot_settings')->nullable()->after('metodos_pago_activos');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->dropColumn('chatbot_settings');
        });
    }
};
