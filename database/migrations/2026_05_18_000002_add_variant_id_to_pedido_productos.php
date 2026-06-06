<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_productos', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->constrained('producto_variants')->nullOnDelete();
            $table->string('variant_tamanio')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pedido_productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
            $table->dropColumn('variant_tamanio');
        });
    }
};
