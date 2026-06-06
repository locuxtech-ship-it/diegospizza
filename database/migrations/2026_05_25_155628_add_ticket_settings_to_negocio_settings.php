<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->boolean('ticket_mostrar_logo')->default(true);
            $table->integer('ticket_escala')->default(100);
            $table->string('ticket_interlineado')->default('normal');
            $table->string('ticket_espaciado')->default('normal');
            $table->boolean('ticket_negritas')->default(true);
            $table->string('ticket_margen')->default('normal');
            $table->string('ticket_fuente')->default('courier');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_mostrar_logo',
                'ticket_escala',
                'ticket_interlineado',
                'ticket_espaciado',
                'ticket_negritas',
                'ticket_margen',
                'ticket_fuente',
            ]);
        });
    }
};
