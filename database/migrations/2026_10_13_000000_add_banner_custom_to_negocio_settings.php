<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->string('banner_tipo')->default('defecto')->after('banner');
            $table->string('banner_color')->nullable()->after('banner_tipo');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->dropColumn(['banner_tipo', 'banner_color']);
        });
    }
};
