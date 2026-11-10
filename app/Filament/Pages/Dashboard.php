<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount(): void
    {
        if (!auth()->user()?->isAdmin()) {
            $this->redirect(
                auth()->user()?->isRepartidor()
                    ? \App\Filament\Pages\MisEntregas::getUrl()
                    : Comandas::getUrl()
            );
        }
    }
}
