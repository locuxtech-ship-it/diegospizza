<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->unique();
            $table->foreignId('user_id')->constrained();
            $table->decimal('total_efectivo', 10, 2)->default(0);
            $table->decimal('total_transferencias', 10, 2)->default(0);
            $table->decimal('total_tarjeta', 10, 2)->default(0);
            $table->decimal('total_ventas', 10, 2)->default(0);
            $table->decimal('total_gastos', 10, 2)->default(0);
            $table->decimal('efectivo_esperado', 10, 2)->default(0);
            $table->decimal('efectivo_real', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->string('estado')->default('abierto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_caja');
    }
};
