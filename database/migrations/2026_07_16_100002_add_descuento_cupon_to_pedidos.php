<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('descuento_cupon', 10, 2)->default(0)->after('cupon_descuento_id');
            $table->boolean('con_descuento_producto')->default(false)->after('descuento_cupon');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['descuento_cupon', 'con_descuento_producto']);
        });
    }
};
