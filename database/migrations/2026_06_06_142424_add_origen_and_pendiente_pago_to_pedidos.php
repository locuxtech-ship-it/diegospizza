<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pedidos ADD COLUMN origen VARCHAR(10) NOT NULL DEFAULT 'web' AFTER metodo_pago");
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado ENUM('pendiente_pago','en_proceso','en_camino','entregado','finalizado','cancelado') NOT NULL DEFAULT 'pendiente_pago'");
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN metodo_pago ENUM('efectivo','tarjeta','transferencia','mixto') NOT NULL DEFAULT 'efectivo'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pedidos DROP COLUMN origen");
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado ENUM('pendiente','en_proceso','en_camino','entregado','cancelado') NOT NULL DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN metodo_pago ENUM('efectivo','tarjeta','transferencia','puntos') NOT NULL DEFAULT 'efectivo'");
    }
};
