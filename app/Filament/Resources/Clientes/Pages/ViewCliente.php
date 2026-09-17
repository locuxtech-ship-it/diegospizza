<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use App\Models\Cliente;
use Filament\Resources\Pages\Page;

class ViewCliente extends Page
{
    protected static string $resource = ClienteResource::class;

    protected string $view = 'filament.resources.clientes.pages.view-cliente';

    public ?Cliente $cliente = null;

    public int $totalPedidos = 0;
    public float $montoTotal = 0;
    public int $puntosGanados = 0;
    public int $puntosCanjeados = 0;
    public array $pedidos = [];
    public array $puntos = [];

    public function mount(int|string $record): void
    {
        $this->cliente = Cliente::withCount('pedidos')->find($record);

        if (!$this->cliente) {
            abort(404);
        }

        $this->totalPedidos = $this->cliente->pedidos()->count();
        $this->montoTotal = (float) $this->cliente->pedidos()
            ->where('estado', 'finalizado')
            ->sum('total');

        $this->puntosGanados = (int) $this->cliente->puntos()
            ->where('puntos', '>', 0)
            ->sum('puntos');

        $this->puntosCanjeados = (int) abs($this->cliente->puntos()
            ->where('puntos', '<', 0)
            ->sum('puntos'));

        $this->pedidos = $this->cliente->pedidos()
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $this->puntos = $this->cliente->puntos()
            ->with('pedido')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
