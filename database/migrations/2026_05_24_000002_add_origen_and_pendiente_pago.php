<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pedidos', 'origen')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        } else {
            DB::statement('SET foreign_key_checks=0');
        }

        Schema::create('pedidos_v2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento_puntos', 10, 2)->default(0);
            $table->decimal('descuento_manual', 10, 2)->default(0);
            $table->string('descuento_manual_tipo')->nullable();
            $table->decimal('descuento_manual_valor', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('origen', 10)->nullable()->default('pdv');
            $table->string('estado', 20)->default('pendiente_pago');
            $table->string('metodo_pago', 20)->default('efectivo');
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamps();
        });

        $cols = 'id, cliente_id, subtotal, descuento_puntos, descuento_manual, descuento_manual_tipo, descuento_manual_valor, total, estado, metodo_pago, notas, motivo_cancelacion, fecha_entrega, created_at, updated_at';
        DB::statement("INSERT INTO pedidos_v2 ({$cols}, origen) SELECT {$cols}, 'pdv' FROM pedidos");

        DB::statement("DROP TABLE IF EXISTS pedidos");
        DB::statement("ALTER TABLE pedidos_v2 RENAME TO pedidos");

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

        Schema::create('pedidos_v2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento_puntos', 10, 2)->default(0);
            $table->decimal('descuento_manual', 10, 2)->default(0);
            $table->string('descuento_manual_tipo')->nullable();
            $table->decimal('descuento_manual_valor', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('estado', 20)->default('pendiente');
            $table->string('metodo_pago', 20)->default('efectivo');
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamps();
        });

        $cols = 'id, cliente_id, subtotal, descuento_puntos, descuento_manual, descuento_manual_tipo, descuento_manual_valor, total, estado, metodo_pago, notas, motivo_cancelacion, fecha_entrega, created_at, updated_at';
        DB::statement("INSERT INTO pedidos_v2 ({$cols}) SELECT {$cols} FROM pedidos");

        DB::statement("DROP TABLE IF EXISTS pedidos");
        DB::statement("ALTER TABLE pedidos_v2 RENAME TO pedidos");

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        } else {
            DB::statement('SET foreign_key_checks=1');
        }
    }
};
