<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantPayment;
use App\Services\PlanService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use UnitEnum;

class MiPlan extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.pages.mi-plan';
    protected static ?string $slug = 'mi-plan';
    protected static ?string $title = 'Mi Plan';
    protected static ?string $navigationLabel = 'Mi Plan';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 20;

    public ?Tenant $tenant = null;
    public array $planes = [];
    public ?Plan $planActual = null;
    public array $featuresHabilitadas = [];
    public string $planStatus = '';
    public ?string $planExpira = null;
    public array $pagos = [];

    public string $monto = '';
    public string $metodo = 'transferencia';
    public string $referencia = '';
    public $comprobante = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->tenant = app()->bound(Tenant::class) ? app(Tenant::class) : $this->resolveTenantPorUsuario();

        if (!$this->tenant) {
            return;
        }

        $plans = app(PlanService::class)->forTenant($this->tenant);
        $this->planActual = $plans->effectivePlan($this->tenant);
        $this->planStatus = $this->tenant->plan_status;
        $this->planExpira = $this->tenant->plan_expira?->format('d/m/Y');
        $this->featuresHabilitadas = $plans->enabledFeatures();
        $this->planes = Plan::where('activo', true)->orderBy('orden')->get()->toArray();
        $this->pagos = TenantPayment::where('tenant_id', $this->tenant->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    private function resolveTenantPorUsuario(): ?Tenant
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        return Tenant::where('config', 'like', '%' . $user->email . '%')->first();
    }

    public function pagar(string $planSlug): void
    {
        $plan = Plan::bySlug($planSlug);

        $this->validate([
            'metodo' => 'required|in:transferencia,efectivo,tarjeta',
            'comprobante' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:4096',
        ]);

        $comprobantePath = null;
        if ($this->comprobante) {
            $comprobantePath = $this->comprobante->store('comprobantes', 'public');
        }

        TenantPayment::create([
            'tenant_id' => $this->tenant->id,
            'plan_id' => $plan?->id,
            'monto' => $plan?->precio_mensual ?? 0,
            'metodo' => $this->metodo,
            'referencia' => $this->referencia ?: null,
            'comprobante' => $comprobantePath,
            'estado' => 'pendiente',
            'periodo' => now()->format('Y-m'),
        ]);

        $this->reset(['monto', 'referencia', 'comprobante']);
        $this->mount();

        Notification::make()
            ->title('Pago enviado')
            ->body('Te contactaremos para confirmar tu pago. Mientras tanto sigues con tu plan actual.')
            ->success()
            ->send();
    }
}
