<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\ChatBot;
use App\Filament\Pages\CierreCajaPage;
use App\Filament\Pages\Comandas;
use App\Filament\Pages\Configuracion;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Fidelidad;
use App\Filament\Pages\HistorialPedidos;
use App\Filament\Pages\ManualOrder;
use App\Filament\Pages\MiPlan;
use App\Filament\Pages\Reportes;
use App\Filament\Pages\Reviews;
use App\Filament\Auth\EditProfile;
use App\Http\Middleware\IdentifyTenant;
use App\Models\NegocioSetting;
use App\Models\Tenant;
use App\Services\PlanService;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->brandName(fn (): string => \App\Models\NegocioSetting::getSettings()->nombre_negocio
                ?? $this->resolveTenant()?->nombre_negocio
                ?? config('app.name', 'Facilo'))
            ->brandLogo(fn (): string => \App\Models\NegocioSetting::getSettings()->logo
                ? asset('storage/' . \App\Models\NegocioSetting::getSettings()->logo)
                : asset('images/logo-facilo.svg'))
            ->brandLogoHeight('2.5rem')
            ->homeUrl('/admin/comandas')
            ->colors(fn () => [
                'primary' => $this->resolveTenant()?->colores && isset($this->resolveTenant()->colores['primary'])
                    ? Color::hex($this->resolveTenant()->colores['primary'])
                    : Color::hex('#F1590C'),
            ])
            ->navigationGroups([
                'Punto de Venta',
                'Ventas',
                'Menu',
                'Configuración',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages($this->planPages())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                IdentifyTenant::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Resuelve el tenant del negocio: desde el container (middleware IdentifyTenant)
     * o, en dev sin subdominio, buscando el tenant asociado al usuario autenticado.
     */
    protected function resolveTenant(): ?Tenant
    {
        if (app()->bound(Tenant::class)) {
            return app(Tenant::class);
        }

        $user = auth()->user();
        $tenant = null;
        if ($user) {
            $tenant = Tenant::where('config', 'like', '%' . $user->email . '%')->first();
            if (!$tenant) {
                $tenant = Tenant::where('config', 'like', '%"' . $user->id . '"%')->first();
            }
        }

        if ($tenant) {
            app(\App\Services\TenantService::class)->configureTenantConnection($tenant);
            app()->instance(Tenant::class, $tenant);
        }

        return $tenant;
    }

    /**
     * Páginas del panel condicionadas a las features del plan del tenant actual.
     * Las páginas críticas (PDV) se muestran siempre; el resto depende del plan.
     */
    protected function planPages(): array
    {
        $tenant = $this->resolveTenant();
        if ($tenant && !app()->bound(Tenant::class)) {
            app()->instance(Tenant::class, $tenant);
        }

        $plan = app(PlanService::class);

        $pages = [
            Comandas::class,
        ];

        if ($plan->can('punto_venta') || !$tenant) {
            $pages[] = ManualOrder::class;
            $pages[] = CierreCajaPage::class;
        }

        if ($plan->can('reportes') || !$tenant) {
            $pages[] = Reportes::class;
            $pages[] = HistorialPedidos::class;
        }

        if ($plan->can('fidelidad') || !$tenant) {
            $pages[] = Fidelidad::class;
        }

        if ($plan->can('whatsapp_bot') || !$tenant) {
            $pages[] = ChatBot::class;
        }

        if ($plan->can('resenas') || !$tenant) {
            $pages[] = Reviews::class;
        }

        $pages[] = Configuracion::class;
        $pages[] = Dashboard::class;
        $pages[] = MiPlan::class;

        return $pages;
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => '<meta name="theme-color" content="#f47b20">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <link rel="manifest" href="/manifest-admin.json">
                <link rel="icon" type="image/x-icon" href="/favicon.ico">
                <link rel="apple-touch-icon" href="/images/icon-admin-192x192.png">
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
                <style>
                    html:not(.dark) body { font-family: "Nunito", sans-serif; }
                    html:not(.dark) .fi-btn, html:not(.dark) .fi-input, html:not(.dark) .fi-badge,
                    html:not(.dark) .fi-dropdown, html:not(.dark) .fi-modal, html:not(.dark) .fi-sidebar {
                        border-radius: 10px !important;
                    }
                    html:not(.dark) .fi-widget, html:not(.dark) .fi-section, html:not(.dark) .fi-card {
                        border-radius: 14px !important;
                        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
                    }
                    html:not(.dark) .fi-btn-primary { box-shadow: 0 4px 14px rgba(241,89,12,0.25) !important; }
                    html:not(.dark) .fi-sidebar { background: #FFF8F0 !important; }
                    html:not(.dark) .fi-main { font-size: 14px; }
                    @media (max-width: 1024px) {
                        html:not(.dark) .fi-main { font-size: 13.5px; }
                        html:not(.dark) .fi-widget, html:not(.dark) .fi-section { padding: 0.9rem !important; }
                    }
                    @media (max-width: 768px) {
                        html:not(.dark) .fi-main { font-size: 13px; }
                        html:not(.dark) .fi-widget, html:not(.dark) .fi-section { padding: 0.8rem !important; }
                        html:not(.dark) .fi-btn, html:not(.dark) .fi-input { font-size: 13px !important; }
                    }
                </style>
'
        );

        FilamentView::registerRenderHook(
            'panels::topbar.logo.after',
            function (): string {
                if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'cajero'])) return '';
                $negocio = \App\Models\NegocioSetting::getSettings();
                $nombre = $negocio->nombre_negocio ?? $this->resolveTenant()?->nombre_negocio ?? 'Facilo';
                $tenant = $this->resolveTenant();
                $tiendaUrl = (app()->environment('production') && $tenant?->dominio)
                    ? 'https://' . $tenant->dominio
                    : url('/tienda');
                return '<span style="display:inline-flex;align-items:center;gap:10px;margin-left:10px;">
                    <span style="font-weight:800;font-size:15px;color:#111827;white-space:nowrap;">' . e($nombre) . '</span>
                    <a href="' . $tiendaUrl . '" target="_blank" style="display:inline-flex;align-items:center;gap:5px;background:#F1590C;color:#fff;font-weight:700;font-size:12px;padding:5px 12px;border-radius:999px;text-decoration:none;box-shadow:0 2px 8px rgba(241,89,12,0.25);">🏪 Ver mi tienda</a>
                </span>';
            }
        );

        FilamentView::registerRenderHook(
            'panels::body.start',
            function (): string {
                if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'cajero'])) return '';
                $abierto = \App\Models\NegocioSetting::isOpen();
                $hoy = \App\Models\NegocioSetting::getTodayHours();
                $dia = ucfirst(now()->locale('es')->dayName);
                $color = $abierto ? '#16a34a' : '#dc2626';
                $icono = $abierto ? '🟢' : '🔴';
                $texto = $abierto ? 'Abierto' : 'Cerrado';
                return '<div style="text-align:center;padding:4px 0;">
                <span style="display:inline-flex;align-items:center;gap:8px;background:' . $color . ';color:white;padding:4px 16px;border-radius:9999px;font-size:13px;font-weight:600;flex-wrap:wrap;justify-content:center;">
                    <span>' . $icono . ' Menú Digital: ' . $texto . '</span>
                    <span style="opacity:0.85;">— ' . $dia . ' ' . $hoy['apertura'] . ' a ' . $hoy['cierre'] . '</span>
                </span>
            </div>';
            }
        );

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => auth()->check() && in_array(auth()->user()->role, ['admin', 'cajero'])
                ? view('partials.global-notifications')->render()
                : ''
        );
    }
}
