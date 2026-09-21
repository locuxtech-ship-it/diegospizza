<?php

namespace App\Filament\Pages;

use App\Models\CierreCaja;
use App\Models\GastoCierre;
use App\Models\Pago;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CierreCajaPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'Cierre de Caja';
    protected static ?string $title = 'Cierre de Caja';
    protected static ?string $slug = 'cierre-caja';
    protected static ?int $navigationSort = 4;
    protected static string | UnitEnum | null $navigationGroup = 'Punto de Venta';

    protected string $view = 'filament.pages.cierre-caja';

    public static function canAccess(): bool
    {
        return auth()->user() && in_array(auth()->user()->role, ['admin', 'cajero']);
    }

    public ?CierreCaja $cierre = null;
    public array $gastos = [];
    public string $nuevoGastoDesc = '';
    public float $nuevoGastoMonto = 0;
    public ?float $efectivoReal = null;
    public string $observaciones = '';
    public bool $esAdmin = false;

    // Totals calculated from DB
    public float $totalEfectivo = 0;
    public float $totalTransferencias = 0;
    public float $totalTarjeta = 0;
    public float $totalVentas = 0;
    public float $totalGastos = 0;
    public float $efectivoEsperado = 0;

    public function mount(): void
    {
        $this->esAdmin = auth()->user()?->isAdmin() ?? false;
        $this->cargarDatos();
    }

    public function reabrirCierre(): void
    {
        if (!$this->cierre || $this->cierre->estado !== 'cuadrado') return;

        $this->cierre->update([
            'estado' => 'abierto',
            'efectivo_real' => null,
        ]);

        $this->cargarDatos();

        Notification::make()
            ->title('Cierre reabierto')
            ->body('Puedes ajustar los valores y guardar nuevamente.')
            ->warning()
            ->send();
    }

    public function cargarDatos(): void
    {
        $hoy = now()->startOfDay();
        $this->cierre = CierreCaja::with('gastos')->where('fecha', $hoy)->first();

        $this->calcularTotalesVentas();

        if ($this->cierre) {
            $this->totalGastos = (float) $this->cierre->total_gastos;
            $this->efectivoReal = $this->cierre->efectivo_real ? (float) $this->cierre->efectivo_real : null;
            $this->observaciones = $this->cierre->observaciones ?? '';
            $this->gastos = $this->cierre->gastos->toArray();
        } else {
            $this->efectivoReal = null;
            $this->observaciones = '';
            $this->gastos = [];
            $this->totalGastos = 0;
        }

        $this->efectivoEsperado = $this->totalEfectivo - $this->totalGastos;
    }

    private function calcularTotalesVentas(): void
    {
        $hoy = now()->format('Y-m-d');

        $this->totalEfectivo = (float) Pago::whereDate('created_at', $hoy)
            ->where('confirmado', true)
            ->where('metodo', 'efectivo')
            ->sum('monto');

        $this->totalTransferencias = (float) Pago::whereDate('created_at', $hoy)
            ->where('confirmado', true)
            ->where('metodo', 'transferencia')
            ->sum('monto');

        $this->totalTarjeta = (float) Pago::whereDate('created_at', $hoy)
            ->where('confirmado', true)
            ->where('metodo', 'tarjeta')
            ->sum('monto');

        $this->totalVentas = $this->totalEfectivo + $this->totalTransferencias + $this->totalTarjeta;
    }

    public function agregarGasto(): void
    {
        if (!trim($this->nuevoGastoDesc) || $this->nuevoGastoMonto <= 0) return;

        $this->gastos[] = [
            'id' => null,
            'descripcion' => trim($this->nuevoGastoDesc),
            'monto' => $this->nuevoGastoMonto,
        ];

        $this->totalGastos = (float) array_sum(array_column($this->gastos, 'monto'));
        $this->efectivoEsperado = $this->totalEfectivo - $this->totalGastos;

        $this->nuevoGastoDesc = '';
        $this->nuevoGastoMonto = 0;
    }

    public function quitarGasto(int $index): void
    {
        array_splice($this->gastos, $index, 1);
        $this->totalGastos = (float) array_sum(array_column($this->gastos, 'monto'));
        $this->efectivoEsperado = $this->totalEfectivo - $this->totalGastos;
    }

    public function guardarCierre(): void
    {
        $hoy = now()->startOfDay();
        $diferencia = $this->efectivoReal !== null ? $this->efectivoReal - $this->efectivoEsperado : 0;

        $this->cierre = CierreCaja::updateOrCreate(
            ['fecha' => $hoy],
            [
                'user_id' => auth()->id(),
                'total_efectivo' => $this->totalEfectivo,
                'total_transferencias' => $this->totalTransferencias,
                'total_tarjeta' => $this->totalTarjeta,
                'total_ventas' => $this->totalVentas,
                'total_gastos' => $this->totalGastos,
                'efectivo_esperado' => $this->efectivoEsperado,
                'efectivo_real' => $this->efectivoReal,
                'diferencia' => $diferencia,
                'observaciones' => $this->observaciones,
                'estado' => $this->efectivoReal !== null ? 'cuadrado' : 'abierto',
            ]
        );

        GastoCierre::where('cierre_id', $this->cierre->id)->delete();

        foreach ($this->gastos as $gasto) {
            GastoCierre::create([
                'cierre_id' => $this->cierre->id,
                'descripcion' => $gasto['descripcion'],
                'monto' => $gasto['monto'],
            ]);
        }

        $this->cierre->refresh();
        $this->cargarDatos();

        Notification::make()
            ->title('Cierre de caja guardado')
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        return [
            'cierre' => $this->cierre,
            'gastos' => $this->gastos,
            'totalEfectivo' => $this->totalEfectivo,
            'totalTransferencias' => $this->totalTransferencias,
            'totalTarjeta' => $this->totalTarjeta,
            'totalVentas' => $this->totalVentas,
            'totalGastos' => $this->totalGastos,
            'efectivoEsperado' => $this->efectivoEsperado,
            'efectivoReal' => $this->efectivoReal,
            'esAdmin' => $this->esAdmin,
        ];
    }
}
