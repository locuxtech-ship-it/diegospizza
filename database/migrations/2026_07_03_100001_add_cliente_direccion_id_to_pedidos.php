<?php

use App\Models\Cliente;
use App\Models\ClienteDireccion;
use App\Models\Pedido;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('cliente_direccion_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('cliente_direcciones')
                ->nullOnDelete();
        });

        // Create direcciones from existing clientes and link pedidos
        $clientes = Cliente::whereNotNull('conjunto')->where('conjunto', '!=', '')->get();
        foreach ($clientes as $cliente) {
            $dir = ClienteDireccion::create([
                'cliente_id' => $cliente->id,
                'alias' => 'Principal',
                'conjunto' => $cliente->conjunto,
                'torre' => $cliente->torre,
                'apto' => $cliente->apto,
                'es_principal' => true,
            ]);

            Pedido::where('cliente_id', $cliente->id)
                ->whereNull('cliente_direccion_id')
                ->update(['cliente_direccion_id' => $dir->id]);
        }
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cliente_direccion_id']);
            $table->dropColumn('cliente_direccion_id');
        });
    }
};
