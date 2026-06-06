<?php

namespace App\Filament\Pages;

use App\Models\NegocioSetting;
use Filament\Pages\Page;
use UnitEnum;

class Fidelidad extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.fidelidad';
    protected static ?string $slug = 'fidelidad';
    protected static ?string $title = 'Programa de Fidelidad';
    protected static ?string $navigationLabel = 'Programa de Fidelidad';
    protected static UnitEnum|string|null $navigationGroup = 'Configuración';

    public $puntos_ganancia_monto = 100;
    public $puntos_ganancia_valor = 1;
    public $recompensas = [];
    public $mensaje = '';

    // New reward form
    public $editIndex = null;
    public $form_puntos = '';
    public $form_tipo = 'porcentaje';
    public $form_valor = '';

    public function mount()
    {
        $settings = NegocioSetting::first();
        if (!$settings) {
            $settings = NegocioSetting::create(['nombre_negocio' => "Diego's Pizza"]);
        }
        $this->puntos_ganancia_monto = $settings->puntos_ganancia_monto ?? 100;
        $this->puntos_ganancia_valor = $settings->puntos_ganancia_valor ?? 1;
        $this->recompensas = $settings->puntos_recompensas ?? [];
    }

    public function save()
    {
        $this->validate([
            'puntos_ganancia_monto' => 'required|numeric|min:1',
            'puntos_ganancia_valor' => 'required|integer|min:1',
        ]);

        NegocioSetting::first()->update([
            'puntos_ganancia_monto' => $this->puntos_ganancia_monto,
            'puntos_ganancia_valor' => $this->puntos_ganancia_valor,
            'puntos_recompensas' => $this->recompensas,
        ]);

        $this->mensaje = '✅ Configuración guardada correctamente';
    }

    public function agregarRecompensa()
    {
        $this->validate([
            'form_puntos' => 'required|integer|min:1',
            'form_tipo' => 'required|in:porcentaje,fijo',
            'form_valor' => 'required|numeric|min:0',
        ]);

        $entry = [
            'puntos' => (int) $this->form_puntos,
            'tipo' => $this->form_tipo,
            'valor' => (float) $this->form_valor,
        ];

        if ($this->editIndex !== null) {
            $this->recompensas[$this->editIndex] = $entry;
        } else {
            $this->recompensas[] = $entry;
        }

        usort($this->recompensas, fn($a, $b) => $a['puntos'] - $b['puntos']);

        $this->resetForm();
    }

    public function editarRecompensa(int $index)
    {
        $r = $this->recompensas[$index];
        $this->editIndex = $index;
        $this->form_puntos = $r['puntos'];
        $this->form_tipo = $r['tipo'];
        $this->form_valor = $r['valor'];
    }

    public function eliminarRecompensa(int $index)
    {
        unset($this->recompensas[$index]);
        $this->recompensas = array_values($this->recompensas);
    }

    public function resetForm()
    {
        $this->editIndex = null;
        $this->form_puntos = '';
        $this->form_tipo = 'porcentaje';
        $this->form_valor = '';
    }
}
