<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if migration was already applied (table has updated enums)
        if (Schema::hasColumn('pedidos', 'origen')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        } else {
            DB::statement('SET foreign_key_checks=0');
        }

        Schema::rename('pedidos', 'pedidos_temp');

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento_puntos', 10, 2)->default(0);
            $table->decimal('descuento_manual', 10, 2)->default(0);
            $table->string('descuento_manual_tipo')->nullable();
            $table->decimal('descuento_manual_valor', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'en_proceso', 'en_camino', 'finalizado', 'cancelado'])->default('pendiente');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto'])->default('efectivo');
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamps();
        });

        DB::statement("INSERT INTO pedidos (id, cliente_id, subtotal, descuento_puntos, descuento_manual, descuento_manual_tipo, descuento_manual_valor, total, estado, metodo_pago, notas, motivo_cancelacion, fecha_entrega, created_at, updated_at)
                        SELECT id, cliente_id, subtotal, descuento_puntos, descuento_manual, descuento_manual_tipo, descuento_manual_valor, total,
                               CASE estado WHEN 'entregado' THEN 'finalizado' ELSE estado END,
                               CASE metodo_pago WHEN 'puntos' THEN 'mixto' ELSE metodo_pago END,
                               notas, motivo_cancelacion, fecha_entrega, created_at, updated_at FROM pedidos_temp");

        DB::statement('DROP TABLE pedidos_temp');
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        } else {
            DB::statement('SET foreign_key_checks=1');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        } else {
            DB::statement('SET foreign_key_checks=0');
        }
        Schema::rename('pedidos', 'pedidos_temp');

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento_puntos', 10, 2)->default(0);
            $table->decimal('descuento_manual', 10, 2)->default(0);
            $table->string('descuento_manual_tipo')->nullable();
            $table->decimal('descuento_manual_valor', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'en_proceso', 'en_camino', 'entregado', 'cancelado'])->default('pendiente');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'puntos'])->default('efectivo');
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamps();
        });

        DB::statement('INSERT INTO pedidos SELECT * FROM pedidos_temp');
        DB::statement('DROP TABLE pedidos_temp');
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        } else {
            DB::statement('SET foreign_key_checks=1');
        }
    }
};
