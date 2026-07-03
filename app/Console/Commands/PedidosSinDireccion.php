<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use Illuminate\Console\Command;

class PedidosSinDireccion extends Command
{
    protected $signature = 'pedidos:sin-direccion';
    protected $description = 'Lista pedidos sin direccion de cliente';

    public function handle()
    {
        $pedidos = Pedido::with('cliente')
            ->whereNull('cliente_direccion_id')
            ->get()
            ->filter(fn ($p) => !$p->cliente || empty($p->cliente->conjunto));

        if ($pedidos->isEmpty()) {
            $this->info('No hay pedidos sin direccion.');
            return;
        }

        $this->info('Pedidos sin direccion (' . $pedidos->count() . '):');
        foreach ($pedidos as $p) {
            $this->line("#{$p->numero_pedido} - Cliente: {$p->cliente?->nombre} ({$p->cliente?->telefono}) - {$p->created_at->format('d/m/Y')}");
        }
    }
}
