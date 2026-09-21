<?php

namespace App\Filament\Pages;

use App\Models\CierreCaja;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class HistorialCierres extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?string $navigationLabel = 'Historial Cierres';
    protected static ?string $title = 'Historial de Cierres';
    protected static ?string $slug = 'historial-cierres';
    protected static ?int $navigationSort = 5;
    protected static string | UnitEnum | null $navigationGroup = 'Punto de Venta';

    protected string $view = 'filament.pages.historial-cierres';

    public array $cierres = [];
    public bool $esAdmin = false;

    public function mount(): void
    {
        $this->esAdmin = auth()->user()?->isAdmin() ?? false;
        $this->cargarHistorial();
    }

    public function cargarHistorial(): void
    {
        $this->cierres = CierreCaja::with('user')
            ->orderBy('fecha', 'desc')
            ->limit(60)
            ->get()
            ->toArray();
    }

    public static function canAccess(): bool
    {
        return auth()->user() && in_array(auth()->user()->role, ['admin', 'cajero']);
    }
}
