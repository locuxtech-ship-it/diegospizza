<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Doble capacidad: un cajero (u otro usuario) puede también repartir
            // sin cambiar de rol. Los repartidores actuales quedan en true.
            $table->boolean('puede_repartir')->default(false)->after('role');
        });

        DB::table('users')->where('role', 'repartidor')->update(['puede_repartir' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('puede_repartir');
        });
    }
};
