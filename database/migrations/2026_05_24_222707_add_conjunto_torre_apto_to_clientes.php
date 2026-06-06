<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('conjunto')->nullable()->after('direccion');
            $table->string('torre')->nullable()->after('conjunto');
            $table->string('apto')->nullable()->after('torre');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['conjunto', 'torre', 'apto']);
        });
    }
};
