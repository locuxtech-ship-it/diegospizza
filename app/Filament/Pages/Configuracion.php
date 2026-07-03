<?php

namespace App\Filament\Pages;

use App\Models\NegocioSetting;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use UnitEnum;

class Configuracion extends Page
{
    use WithFileUploads;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.configuracion';
    protected static ?string $slug = 'configuracion';
    protected static ?string $title = 'Configuración';
    protected static ?string $navigationLabel = 'Configuración';
    protected static UnitEnum|string|null $navigationGroup = 'Configuración';

    public $nombre_negocio = '';
    public $telefono = '';
    public $direccion = '';
    public $horario_apertura = '11:00';
    public $horario_cierre = '23:00';
    public $dias_laborales = [];
    public $llave = '';
    public $daviplata = '';
    public $nequi = '';
    public $impresora_nombre = '';
    public $imprimir_automaticamente = false;
    public $ticket_size = '80';
    public $ticket_mostrar_logo = true;
    public $ticket_escala = 100;
    public $ticket_interlineado = 'normal';
    public $ticket_espaciado = 'normal';
    public $ticket_negritas = true;
    public $ticket_margen = '0.8';
    public $ticket_fuente = 'courier';
    public $logo;
    public $logoPreview = '';
    public $mensaje = '';
    public $metodos_pago_activos = [];
    public $horarios_por_dia = [];
    public $pedidos_pausados = false;

    public function mount()
    {
        $negocio = NegocioSetting::first();
        if (!$negocio) {
            $negocio = NegocioSetting::create([
                'nombre_negocio' => "Diego's Pizza",
                'horario_apertura' => '11:00',
                'horario_cierre' => '23:00',
                'dias_laborales' => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            ]);
        }
        $this->fillFromModel($negocio);
    }

    protected function fillFromModel(NegocioSetting $negocio): void
    {
        $this->nombre_negocio = $negocio->nombre_negocio;
        $this->telefono = $negocio->telefono;
        $this->direccion = $negocio->direccion;
        $this->horario_apertura = $negocio->horario_apertura;
        $this->horario_cierre = $negocio->horario_cierre;
        $this->dias_laborales = $negocio->dias_laborales ?? [];
        $this->llave = $negocio->llave;
        $this->daviplata = $negocio->daviplata;
        $this->nequi = $negocio->nequi;
        $this->impresora_nombre = $negocio->impresora_nombre;
        $this->imprimir_automaticamente = $negocio->imprimir_automaticamente ?? false;
        $this->ticket_size = $negocio->ticket_size ?? '80';
        $this->ticket_mostrar_logo = $negocio->ticket_mostrar_logo ?? true;
        $this->ticket_escala = $negocio->ticket_escala ?? 100;
        $this->ticket_interlineado = $negocio->ticket_interlineado ?? 'normal';
        $this->ticket_espaciado = $negocio->ticket_espaciado ?? 'normal';
        $this->ticket_negritas = $negocio->ticket_negritas ?? true;
        $this->ticket_margen = $negocio->ticket_margen ?? '0.8';
        $this->ticket_fuente = $negocio->ticket_fuente ?? 'courier';
        $this->metodos_pago_activos = $negocio->metodos_pago_activos ?? ['efectivo', 'tarjeta', 'transferencia'];
        $this->horarios_por_dia = $negocio->horarios_por_dia ?? [];
        $this->pedidos_pausados = $negocio->pedidos_pausados ?? false;
        $this->logoPreview = $negocio->logo ? Storage::disk('public')->url($negocio->logo) : '';
    }

    public function toggleMetodoPago(string $metodo): void
    {
        if (in_array($metodo, $this->metodos_pago_activos)) {
            if (count($this->metodos_pago_activos) > 1) {
                $this->metodos_pago_activos = array_values(array_filter($this->metodos_pago_activos, fn($m) => $m !== $metodo));
            }
        } else {
            $this->metodos_pago_activos[] = $metodo;
        }
    }

    public function save()
    {
        $this->validate([
            'nombre_negocio' => 'required',
            'horario_apertura' => 'required',
            'horario_cierre' => 'required',

        ]);

        $negocio = NegocioSetting::first();

        $data = [
            'nombre_negocio' => $this->nombre_negocio,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'horario_apertura' => $this->horario_apertura,
            'horario_cierre' => $this->horario_cierre,
            'dias_laborales' => $this->dias_laborales,
            'llave' => $this->llave,
            'daviplata' => $this->daviplata,
            'nequi' => $this->nequi,
            'impresora_nombre' => $this->impresora_nombre,
            'imprimir_automaticamente' => $this->imprimir_automaticamente,
            'ticket_size' => $this->ticket_size,
            'ticket_mostrar_logo' => $this->ticket_mostrar_logo,
            'ticket_escala' => (int) $this->ticket_escala,
            'ticket_interlineado' => $this->ticket_interlineado,
            'ticket_espaciado' => $this->ticket_espaciado,
            'ticket_negritas' => $this->ticket_negritas,
            'ticket_margen' => $this->ticket_margen,
            'ticket_fuente' => $this->ticket_fuente,
            'metodos_pago_activos' => $this->metodos_pago_activos,
            'horarios_por_dia' => $this->horarios_por_dia,
            'pedidos_pausados' => $this->pedidos_pausados,
        ];

        if ($this->logo) {
            if ($negocio->logo) {
                Storage::disk('public')->delete($negocio->logo);
            }
            $data['logo'] = $this->logo->store('negocio', 'public');
        }

        $negocio->update($data);
        $this->fillFromModel($negocio->fresh());
        $this->mensaje = '✅ Configuración guardada correctamente';
    }
}
