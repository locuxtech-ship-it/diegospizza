<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descuentos_producto', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['porcentaje', 'monto']);
            $table->decimal('valor', 10, 2);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_expiracion');
            $table->boolean('activo')->default(true);
            $table->foreignId('producto_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuentos_producto');
    }
};
