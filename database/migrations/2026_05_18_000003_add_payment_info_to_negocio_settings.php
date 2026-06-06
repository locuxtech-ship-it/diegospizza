<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->string('llave')->nullable();
            $table->string('daviplata')->nullable();
            $table->string('nequi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->dropColumn(['llave', 'daviplata', 'nequi']);
        });
    }
};
