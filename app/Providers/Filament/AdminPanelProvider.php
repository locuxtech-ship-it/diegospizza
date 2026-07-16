<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\ChatBot;
use App\Filament\Pages\Comandas;
use App\Filament\Auth\EditProfile;
use App\Filament\Resources\CuponDescuentoResource;
use App\Filament\Resources\DescuentoProductoResource;
use App\Models\NegocioSetting;
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
            ->brandName("Diego's Pizza")
            ->brandLogo(fn (): string => NegocioSetting::first()?->logo
                ? asset('storage/' . NegocioSetting::first()->logo)
                : asset('images/logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->homeUrl('/admin/comandas')
            ->colors([
                'primary' => Color::Red,
            ])
            ->navigationGroups([
                'Punto de Venta',
                'Ventas',
                'Menu',
                'Promociones',
                'Configuración',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->resources([
                CuponDescuentoResource::class,
                DescuentoProductoResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Comandas::class,
                ChatBot::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
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

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => '<meta name="theme-color" content="#f47b20">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <link rel="manifest" href="/manifest-admin.json">
                <link rel="icon" type="image/x-icon" href="/favicon.ico">
                <link rel="apple-touch-icon" href="/images/icon-admin-192x192.png">
'
        );

        FilamentView::registerRenderHook(
            'panels::body.start',
            function (): string {
                if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'cajero'])) return '';
                $settings = \App\Models\NegocioSetting::getSettings();
                $abierto = $settings->isOpen();
                $hoy = $settings->getTodayHours();
                $dia = ucfirst(now()->locale('es')->dayName);
                $color = $abierto ? '#16a34a' : '#dc2626';
                $icono = $abierto ? '🟢' : '🔴';
                $texto = $abierto ? 'Abierto' : 'Cerrado';
                $pausado = $settings->isPaused();
                $banners = '<div style="text-align:center;padding:4px 0;">
                <span style="display:inline-flex;align-items:center;gap:8px;background:' . $color . ';color:white;padding:4px 16px;border-radius:9999px;font-size:13px;font-weight:600;flex-wrap:wrap;justify-content:center;">
                    <span>' . $icono . ' Menú Digital: ' . $texto . '</span>
                    <span style="opacity:0.85;">— ' . $dia . ' ' . $hoy['apertura'] . ' a ' . $hoy['cierre'] . '</span>
                </span>
            </div>';
                if ($pausado) {
                    $banners .= '<div style="text-align:center;padding:4px 0;">
                    <span style="display:inline-flex;align-items:center;gap:8px;background:#d97706;color:white;padding:4px 16px;border-radius:9999px;font-size:13px;font-weight:600;">
                        🟡 Pedidos web pausados — los clientes no pueden ordenar
                    </span>
                </div>';
                }
                return $banners;
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
